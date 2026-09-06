<?php

namespace App\Services;

use App\Models\AgentMarginLog;
use App\Models\AgentMarginPayout;
use App\Models\Application;
use App\Models\User;
use App\Notifications\ParentMarginSettledNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AgentMarginPayoutService
{
    /**
     * Get aggregate KPI metrics for margin payouts.
     *
     * @return array<string, mixed>
     */
    public function getKpis(): array
    {
        $totalAccrued = (float) AgentMarginLog::where('status', 'ACCRUED')->sum('margin_amount');
        $totalSettled = (float) AgentMarginPayout::sum('amount');
        $pendingAgenciesCount = AgentMarginLog::where('status', 'ACCRUED')
            ->distinct('parent_agent_id')
            ->count('parent_agent_id');

        return [
            'total_accrued' => $totalAccrued,
            'total_settled' => $totalSettled,
            'pending_agencies_count' => $pendingAgenciesCount,
        ];
    }

    /**
     * Get list of parent agents who have pending accrued margins.
     */
    public function getAgenciesWithAccruedMargins(): \Illuminate\Support\Collection
    {
        return User::where('role', 'agent')
            ->whereNull('parent_id')
            ->whereHas('marginEarnings', fn ($q) => $q->where('status', 'ACCRUED'))
            ->withCount(['subAgents', 'marginEarnings as pending_items_count' => fn ($q) => $q->where('status', 'ACCRUED')])
            ->withSum(['marginEarnings as pending_amount' => fn ($q) => $q->where('status', 'ACCRUED')], 'margin_amount')
            ->orderByDesc('pending_amount')
            ->get();
    }

    /**
     * Get accrued logs for a specific parent agent, optionally filtered by IDs.
     */
    public function getAccruedLogs(User $parentAgent, ?array $logIds = null): Collection
    {
        $query = AgentMarginLog::with(['subAgent', 'application.service'])
            ->where('parent_agent_id', $parentAgent->id)
            ->where('status', 'ACCRUED');

        if (! empty($logIds)) {
            $query->whereIn('id', $logIds);
        }

        return $query->latest()->get();
    }

    /**
     * Execute manual settlement of accrued margins for a parent agent.
     */
    public function settle(User $admin, User $parentAgent, array $data): AgentMarginPayout
    {
        return DB::transaction(function () use ($admin, $parentAgent, $data) {
            $query = AgentMarginLog::where('parent_agent_id', $parentAgent->id)
                ->where('status', 'ACCRUED')
                ->lockForUpdate();

            if (! empty($data['log_ids'])) {
                $query->whereIn('id', $data['log_ids']);
            }

            $accruedLogs = $query->get();

            if ($accruedLogs->isEmpty()) {
                throw ValidationException::withMessages([
                    'log_ids' => ['No eligible accrued margins found for settlement.'],
                ]);
            }

            $totalAmount = round($accruedLogs->sum('margin_amount'), 2);
            if ($totalAmount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => ['Settlement amount must be greater than zero.'],
                ]);
            }

            // Generate unique voucher number: MPAY-YYYYMMDD-XXXX
            $datePrefix = Carbon::now()->format('Ymd');
            $dailySequence = AgentMarginPayout::whereDate('created_at', Carbon::today())->count() + 1;
            $payoutNumber = sprintf('MPAY-%s-%04d', $datePrefix, $dailySequence);

            $paymentDate = ! empty($data['payment_date'])
                ? Carbon::parse($data['payment_date'])
                : Carbon::today();

            $payout = AgentMarginPayout::create([
                'payout_number' => $payoutNumber,
                'parent_agent_id' => $parentAgent->id,
                'admin_id' => $admin->id,
                'amount' => $totalAmount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'transaction_reference' => $data['transaction_reference'],
                'payment_date' => $paymentDate,
                'notes' => $data['notes'] ?? null,
            ]);

            $logIds = $accruedLogs->pluck('id')->all();
            $appIds = $accruedLogs->pluck('application_id')->all();

            // Mark margin logs as PAID
            AgentMarginLog::whereIn('id', $logIds)->update([
                'status' => 'PAID',
                'margin_payout_id' => $payout->id,
                'payout_reference' => $data['transaction_reference'],
                'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [Settled in {$payoutNumber} on {$paymentDate->format('Y-m-d')}]')"),
            ]);

            // Mark underlying applications as PAID
            Application::whereIn('id', $appIds)->update([
                'parent_margin_status' => 'PAID',
                'parent_margin_refunded_at' => now(),
            ]);

            Log::info("Margin payout #{$payout->payout_number} of ₹{$totalAmount} settled for Parent Agent #{$parentAgent->id} by Admin #{$admin->id}.");

            // Dispatch notification to Parent Agent
            try {
                $parentAgent->notify(new ParentMarginSettledNotification($payout, count($logIds)));
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch payout settlement notification to agent #{$parentAgent->id}: ".$e->getMessage());
            }

            return $payout;
        });
    }
}

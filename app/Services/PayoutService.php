<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\AgentPayout;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function generate($startDate, $endDate, $agentId = null)
    {
        return DB::transaction(function () use ($startDate, $endDate, $agentId) {

            $query = Application::query()
                ->whereNull('payout_id')
                ->whereNotNull('commission_amount')
                ->where('payment_status', PaymentStatus::PAID->value)
                ->whereBetween('submitted_at', [$startDate, $endDate]);

            if ($agentId) {
                $query->where('agent_id', $agentId);
            }

            $applications = $query->get()->groupBy('agent_id');

            $payouts = [];

            foreach ($applications as $agent => $apps) {

                $totalCommission = $apps->sum('commission_amount');

                if ($totalCommission <= 0) {
                    continue;
                }

                $payout = AgentPayout::create([
                    'agent_id' => $agent,
                    'amount' => $totalCommission,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                ]);

                Application::whereIn('id', $apps->pluck('id'))
                    ->update([
                        'payout_id' => $payout->id,
                    ]);

                $payouts[] = $payout;
            }

            return $payouts;
        });
    }

    public function markPaid(AgentPayout $payout, $notes = null)
    {
        $payout->update([
            'paid_at' => now(),
            'notes' => $notes,
        ]);

        return $payout;
    }

    public function preview($startDate, $endDate, $agentId = null)
    {
        $query = Application::query()
            ->whereNull('payout_id')
            ->whereNotNull('commission_amount')
            ->where('payment_status', PaymentStatus::PAID->value)
            ->whereBetween('submitted_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        $applications = $query->get()->groupBy('agent_id');

        $preview = [];

        foreach ($applications as $agent => $apps) {

            $totalCommission = $apps->sum('commission_amount');

            if ($totalCommission <= 0) {
                continue;
            }

            $preview[] = [
                'agent_id' => $agent,
                'applications' => $apps->count(),
                'amount' => $totalCommission,
            ];
        }

        return $preview;
    }
}

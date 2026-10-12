<?php

namespace App\Exports;

use App\Enums\ApplicationStatus;
use App\Models\AgentMarginLog;
use App\Models\AgentPayout;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Services\AgentDashboardService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentMasterExport
{
    public function __construct(
        protected ?string $sessionLabel = null
    ) {}

    /**
     * Generate and stream the CSV download response.
     */
    public function download(): StreamedResponse
    {
        $session = $this->sessionLabel ?: 'All_Sessions';
        $safeSession = preg_replace('/[^A-Za-z0-9_\-]/', '_', $session);
        $fileName = "Agents_Master_Report_{$safeSession}_".date('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write Headers
            fputcsv($handle, $this->headings());

            // Query agents
            $agents = User::where('role', 'AGENT')
                ->with(['parentAgent'])
                ->withCount('subAgents')
                ->orderBy('id', 'asc')
                ->get();

            $dashboardService = app(AgentDashboardService::class);
            $serviceMap = Service::pluck('slug', 'id')->all();

            foreach ($agents as $agent) {
                $row = $this->buildAgentRow($agent, $dashboardService, $serviceMap);
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function headings(): array
    {
        return [
            'Agent ID',
            'Agent Code',
            'Agent Name',
            'Email',
            'Phone',
            'Role / Type',
            'Parent Agent',
            'Account Status',
            'Sub-Agents Count',
            'Date Joined',
            'Reporting Session',
            'Total Applications',
            'Completed Applications',
            'Pending / In-Progress',
            'Cancelled / Rejected',
            'ITR Filings (Completed)',
            'GST Registrations (Completed)',
            'GST Return Filings (Completed)',
            'Other Services (Completed)',
            'Gross Application Value (INR)',
            'Commission Earned (INR)',
            'Team Margin Earned (INR)',
            'Total Payouts Disbursed (INR)',
            'Net Unpaid Balance (INR)',
            'Unlocked / Eligible Gifts',
            'Unlocked Gifts Count',
            'Next Upcoming Gift Milestone',
            'Next Target Required',
            'Applications Remaining for Next Gift',
        ];
    }

    protected function buildAgentRow(User $agent, AgentDashboardService $dashboardService, array $serviceMap): array
    {
        // 1. Applications query
        $appQuery = Application::query();
        if ($agent->parent_id) {
            $appQuery->where('sub_agent_id', $agent->id);
        } else {
            $appQuery->where('agent_id', $agent->id);
        }

        if ($this->sessionLabel) {
            $appQuery->inSession($this->sessionLabel);
        }

        $apps = $appQuery->select([
            'id', 'service_id', 'status', 'amount', 'commission_amount',
            'sub_agent_commission', 'parent_margin',
        ])->get();

        $totalApps = $apps->count();
        $completedApps = $apps->where('status', ApplicationStatus::COMPLETED);
        $completedCount = $completedApps->count();
        $pendingCount = $apps->filter(fn ($a) => in_array($a->status, [
            ApplicationStatus::SUBMITTED,
            ApplicationStatus::IN_PROGRESS,
            ApplicationStatus::UNDER_REVIEW,
            ApplicationStatus::DOCUMENTS_REQUIRED,
            ApplicationStatus::E_FILING,
            ApplicationStatus::OTP_VERIFICATION,
        ]))->count();
        $cancelledCount = $apps->filter(fn ($a) => in_array($a->status, [ApplicationStatus::CANCELLED, ApplicationStatus::REJECTED]))->count();

        // 2. Service breakdown for completed applications
        $itrCompleted = 0;
        $gstRegCompleted = 0;
        $gstReturnCompleted = 0;
        $otherCompleted = 0;

        foreach ($completedApps as $app) {
            $slug = $serviceMap[$app->service_id] ?? '';
            if (str_starts_with($slug, 'itr')) {
                $itrCompleted++;
            } elseif ($slug === 'gst-registration') {
                $gstRegCompleted++;
            } elseif (str_starts_with($slug, 'gst-return')) {
                $gstReturnCompleted++;
            } else {
                $otherCompleted++;
            }
        }

        // 3. Financial calculations
        $grossValue = $apps->sum('amount');
        $commissionEarned = $agent->parent_id
            ? $apps->sum('sub_agent_commission')
            : $apps->sum('commission_amount');

        $teamMarginEarned = 0.0;
        if (! $agent->parent_id) {
            $teamMarginEarned = (float) AgentMarginLog::where('parent_agent_id', $agent->id)
                ->where('status', 'ACCRUED')
                ->sum('margin_amount');
        }

        $payoutsTotal = (float) AgentPayout::where('agent_id', $agent->id)->sum('amount');
        $totalEarnings = (float) $commissionEarned + $teamMarginEarned;
        $netBalance = max(0, $totalEarnings - $payoutsTotal);

        // 4. Gift Milestones & Eligibility
        $unlockedGiftNames = [];
        $nextMilestoneName = 'None / All Achieved';
        $nextMilestoneTarget = '—';
        $nextMilestoneNeeded = 0;

        if (! $agent->parent_id) {
            $milestoneGroups = $dashboardService->getMilestoneGroups($agent->id, $this->sessionLabel);
            $unlockedList = [];

            foreach ($milestoneGroups as $group) {
                foreach ($group['milestones'] ?? [] as $m) {
                    if (! empty($m['unlocked'])) {
                        $unlockedList[] = $m['name'];
                    } elseif ($nextMilestoneName === 'None / All Achieved') {
                        $nextMilestoneName = $m['name'];
                        $nextMilestoneTarget = $m['min_count'] ?? '—';
                        $nextMilestoneNeeded = $m['needed'] ?? 0;
                    }
                }
            }
            $unlockedGiftNames = array_unique($unlockedList);
        } else {
            $nextMilestoneName = 'N/A (Sub-Agent)';
        }

        return [
            $agent->id,
            $agent->agent_code ?? '—',
            $agent->name,
            $agent->email,
            $agent->phone ?? $agent->mobile_number ?? '—',
            $agent->parent_id ? 'Sub-Agent' : 'Primary Agent',
            $agent->parentAgent ? "{$agent->parentAgent->name} ({$agent->parentAgent->agent_code})" : 'Direct Primary Agent',
            $agent->is_active ? 'Active' : 'Suspended',
            $agent->sub_agents_count ?? 0,
            $agent->created_at ? $agent->created_at->format('Y-m-d H:i') : '—',
            $this->sessionLabel ?? 'All Time',
            $totalApps,
            $completedCount,
            $pendingCount,
            $cancelledCount,
            $itrCompleted,
            $gstRegCompleted,
            $gstReturnCompleted,
            $otherCompleted,
            number_format($grossValue, 2, '.', ''),
            number_format($commissionEarned, 2, '.', ''),
            number_format($teamMarginEarned, 2, '.', ''),
            number_format($payoutsTotal, 2, '.', ''),
            number_format($netBalance, 2, '.', ''),
            ! empty($unlockedGiftNames) ? implode('; ', $unlockedGiftNames) : 'None Yet',
            count($unlockedGiftNames),
            $nextMilestoneName,
            $nextMilestoneTarget,
            $nextMilestoneNeeded,
        ];
    }
}

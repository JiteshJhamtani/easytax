<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Gift;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AgentDashboardService
{
    /**
     * Injecting the existing tools so we don't repeat code!
     */
    public function __construct(
        protected GiftEligibilityService $eligibilityService,
        protected GiftPeriodResolver $periodResolver
    ) {}

    // =========================================================================
    // 1. DASHBOARD STATS & CHARTS
    // =========================================================================

    public function getStats($agentId, ?string $sessionLabel = null)
    {
        $sessionLabel = $sessionLabel ?? SessionResolver::current()['label'];

        return Cache::remember("agent_dashboard_stats_{$agentId}_{$sessionLabel}", 30, function () use ($agentId, $sessionLabel) {
            return Application::where('agent_id', $agentId)
                ->inSession($sessionLabel)
                ->selectRaw("
                    COUNT(*) as total_applications,
                    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_applications,
                    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as pending_applications,
                    SUM(CASE WHEN payment_status = 'PAID' THEN 1 ELSE 0 END) as successful_payments,
                    SUM(commission_amount) as total_commission,
                    SUM(CASE WHEN payout_id IS NULL THEN commission_amount ELSE 0 END) as pending_commission,
                    SUM(CASE WHEN payout_id IS NOT NULL THEN commission_amount ELSE 0 END) as paid_commission
                ")
                ->first();
        });
    }

    public function getMonthlyApplications($agentId, ?string $sessionLabel = null)
    {
        $sessionLabel = $sessionLabel ?? SessionResolver::current()['label'];

        return Application::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('agent_id', $agentId)
            ->inSession($sessionLabel)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getRecentApplications($agentId, ?string $sessionLabel = null)
    {
        $sessionLabel = $sessionLabel ?? SessionResolver::current()['label'];

        return Application::with('service')
            ->where('agent_id', $agentId)
            ->inSession($sessionLabel)
            ->latest()
            ->limit(10)
            ->get();
    }

    // =========================================================================
    // 2. GIFT MILESTONE LOGIC
    // =========================================================================

    public function getMilestoneGroups(int $agentId, ?string $sessionLabel = null): array
    {
        $gifts = Gift::where('is_active', true)
            ->with('conditionGroups.conditions.service', 'media')
            ->get();

        if ($gifts->isEmpty()) {
            return [];
        }

        $counts = $this->getSubmissionCountsByPeriod($agentId, $gifts, $sessionLabel);
        $groups = $this->processGiftsIntoGroups($gifts, $counts);

        return $this->sortGroups($groups);
    }

    protected function getSubmissionCountsByPeriod(int $agentId, Collection $gifts, ?string $sessionLabel = null): array
    {
        $allServiceIds = $gifts->flatMap->conditionGroups
            ->flatMap->conditions
            ->pluck('service_id')
            ->unique();

        $periodTypes = $gifts->pluck('period_type')->unique();
        $counts = [];

        foreach ($periodTypes as $periodType) {
            // Using the injected resolver to prevent duplicate code!
            [$from, $to] = $this->periodResolver->resolve($periodType, sessionLabel: $sessionLabel);

            $counts[$periodType] = Application::where('agent_id', $agentId)
                ->whereIn('service_id', $allServiceIds)
                ->where('status', ApplicationStatus::COMPLETED)
                ->whereBetween('completed_at', [$from, $to])
                ->selectRaw('service_id, COUNT(*) as total')
                ->groupBy('service_id')
                ->pluck('total', 'service_id');
        }

        return $counts;
    }

    protected function processGiftsIntoGroups(Collection $gifts, array $counts): array
    {
        $serviceGroups = [];

        foreach ($gifts as $gift) {
            $allConds = $gift->conditionGroups->flatMap->conditions;
            $uniqueServices = $allConds->pluck('service_id')->unique();
            $isSingleService = $uniqueServices->count() === 1;

            $periodType = $gift->period_type;
            $periodCounts = $counts[$periodType] ?? collect();
            $minCount = $allConds->min('min_count');

            if ($isSingleService) {
                $serviceGroups = $this->appendSingleServiceGift(
                    $serviceGroups, $gift, $uniqueServices->first(),
                    $allConds->first()->service->name ?? 'Unknown', $periodCounts, $minCount
                );
            } else {
                $serviceGroups = $this->appendMultiServiceGift(
                    $serviceGroups, $gift, $allConds, $periodCounts, $minCount
                );
            }
        }

        return $this->finalizeGroupStats($serviceGroups);
    }

    protected function appendSingleServiceGift(array $groups, Gift $gift, int $serviceId, string $serviceName, Collection $periodCounts, int $minCount): array
    {
        $agentCount = (int) ($periodCounts->get($serviceId) ?? 0);
        $groupKey = $serviceId.'_'.$gift->period_type;

        if (! isset($groups[$groupKey])) {
            $groups[$groupKey] = [
                'type' => 'single',
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'period_type' => $gift->period_type,
                'period_label' => $this->periodLabel($gift->period_type),
                'period_range' => $this->periodRangeLabel($gift->period_type),
                'agent_count' => $agentCount,
                'milestones' => [],
            ];
        }

        $groups[$groupKey]['milestones'][] = [
            'id' => $gift->id,
            'name' => $gift->name,
            'icon' => $gift->icon ?? '🎁',
            'image_url' => $gift->getFirstMediaUrl('gift_banner'),
            'min_count' => $minCount,
            'unlocked' => $agentCount >= $minCount,
            'needed' => max(0, $minCount - $agentCount),
        ];

        return $groups;
    }

    protected function appendMultiServiceGift(array $groups, Gift $gift, Collection $allConds, Collection $periodCounts, int $minCount): array
    {
        $conditionDetails = $allConds->map(function ($cond) use ($periodCounts) {
            $agentCount = (int) ($periodCounts->get($cond->service_id) ?? 0);

            return [
                'service_id' => $cond->service_id,
                'service_name' => $cond->service->name ?? '—',
                'min_count' => $cond->min_count,
                'agent_count' => $agentCount,
                'unlocked' => $agentCount >= $cond->min_count,
                'needed' => max(0, $cond->min_count - $agentCount),
                'pct' => min(100, $cond->min_count > 0 ? round(($agentCount / $cond->min_count) * 100) : 100),
            ];
        })->values()->all();

        $groups['multi_'.$gift->id] = [
            'type' => 'multi',
            'service_id' => null,
            'service_name' => 'Multi-service',
            'period_type' => $gift->period_type,
            'period_label' => $this->periodLabel($gift->period_type),
            'period_range' => $this->periodRangeLabel($gift->period_type),
            'agent_count' => null,
            'milestones' => [[
                'id' => $gift->id,
                'name' => $gift->name,
                'icon' => $gift->icon ?? '🎁',
                'image_url' => $gift->getFirstMediaUrl('gift_banner'),
                'min_count' => $minCount,
                // Using our new lighting-fast check!
                'unlocked' => $this->eligibilityService->isEligibleFromTotals($gift, $periodCounts),
                'conditions' => $conditionDetails,
            ]],
        ];

        return $groups;
    }

    protected function finalizeGroupStats(array $groups): array
    {
        foreach ($groups as &$group) {
            usort($group['milestones'], fn ($a, $b) => $a['min_count'] <=> $b['min_count']);

            if ($group['type'] === 'single') {
                $max = collect($group['milestones'])->max('min_count');
                $group['max_threshold'] = $max;
                $group['progress_pct'] = $max > 0 ? min(100, round(($group['agent_count'] / $max) * 100)) : 100;
                $group['next_milestone'] = collect($group['milestones'])->firstWhere('unlocked', false);
                $group['unlocked_count'] = collect($group['milestones'])->where('unlocked', true)->count();
            }
        }

        return array_values($groups);
    }

    protected function sortGroups(array $groups): array
    {
        $order = ['monthly' => 0, 'quarterly' => 1, 'yearly' => 2];
        uasort($groups, fn ($a, $b) => ($order[$a['period_type']] ?? 9) <=> ($order[$b['period_type']] ?? 9));

        return $groups;
    }

    // =========================================================================
    // 3. UI TEXT FORMATTING HELPERS
    // =========================================================================

    protected function periodLabel(string $type): string
    {
        return match ($type) {
            'monthly' => 'This month',
            'quarterly' => 'This quarter',
            'yearly' => 'This year',
            'session' => 'This session',
            default => ucfirst($type),
        };
    }

    protected function periodRangeLabel(string $type): string
    {
        return match ($type) {
            'monthly' => now()->format('F Y'),
            'quarterly' => 'Q'.now()->quarter.' '.now()->year,
            'yearly' => (string) now()->year,
            'session' => SessionResolver::activeSessionLabel(),
            default => '',
        };
    }
}

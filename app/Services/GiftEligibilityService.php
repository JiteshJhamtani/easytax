<?php

// app/Services/GiftEligibilityService.php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Gift;
use App\Models\User;
use Illuminate\Support\Collection;

class GiftEligibilityService
{
    public function __construct(private GiftPeriodResolver $resolver) {}

    /**
     * Used for Admin Reports: Calculates eligibility for ALL agents.
     * (Kept exactly as you originally wrote it so nothing breaks!)
     */
    public function getResults(Gift $gift, array $params): Collection
    {
        [$from, $to] = $this->resolver->resolve(
            $gift->period_type,
            $params['year'] ?? null,
            $params['quarter'] ?? null,
            $params['month'] ?? null,
        );

        $gift->load('conditionGroups.conditions.service');

        $serviceIds = $gift->conditionGroups
            ->flatMap(fn ($g) => $g->conditions->pluck('service_id'))
            ->unique();

        $submissions = Application::query()
            ->whereIn('service_id', $serviceIds)
            ->where('status', ApplicationStatus::COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->selectRaw('agent_id, service_id, COUNT(*) as total')
            ->groupBy('agent_id', 'service_id')
            ->get()
            ->groupBy('agent_id');

        return User::where('role', 'AGENT')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (User $agent) use ($gift, $submissions) {
                $counts = $submissions->get($agent->id, collect())->keyBy('service_id');

                return [
                    'agent' => $agent,
                    'eligible' => $this->isEligible($gift, $counts),
                    'counts' => $counts,
                ];
            });
    }

    /**
     * Used for Agent Dashboard: Lightning-fast check using pre-calculated totals.
     * NO DATABASE QUERIES ALLOWED HERE.
     */
    public function isEligibleFromTotals(Gift $gift, Collection $totalsByServiceId): bool
    {
        foreach ($gift->conditionGroups as $group) {
            $groupPasses = true;
            foreach ($group->conditions as $condition) {
                $total = (int) ($totalsByServiceId->get($condition->service_id) ?? 0);
                if ($total < $condition->min_count) {
                    $groupPasses = false;
                    break;
                }
            }
            if ($groupPasses) {
                return true;
            }
        }

        return false;
    }

    // Used internally by getResults
    private function isEligible(Gift $gift, Collection $counts): bool
    {
        foreach ($gift->conditionGroups as $group) {
            $groupPasses = true;
            foreach ($group->conditions as $condition) {
                $row = $counts->get($condition->service_id);
                $total = $row ? (int) $row->total : 0;
                if ($total < $condition->min_count) {
                    $groupPasses = false;
                    break;
                }
            }
            if ($groupPasses) {
                return true;
            }
        }

        return false;
    }
}

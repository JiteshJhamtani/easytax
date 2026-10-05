<?php

namespace App\Services;

use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use Illuminate\Pagination\AbstractPaginator;
use InvalidArgumentException;

class SubAgentPricingService
{
    /**
     * Resolve the cascading pricing breakdown for a service across the agent network.
     *
     * @param  float|null  $overrideBasePrice  Optional pre-calculated dynamic base price
     * @param  float|null  $overrideBaseCommission  Optional pre-calculated dynamic base commission
     * @return array{
     *     base_price: float,
     *     base_commission: float,
     *     company_minimum: float,
     *     sub_agent_price: float,
     *     sub_agent_commission: float,
     *     sub_agent_payable: float,
     *     parent_margin: float,
     *     margins_breakdown: array<int, array{agent_id: int, margin: float, tier_level: int, agent: User}>
     * }
     */
    public static function resolveForSubAgent(
        Service $service,
        User $subAgent,
        ?float $overrideBasePrice = null,
        ?float $overrideBaseCommission = null,
        ?float $overrideCompanyMinimum = null
    ): array {
        // 1. Determine company base price & commission
        $basePrice = $overrideBasePrice ?? (float) $service->price;
        $baseCommission = $overrideBaseCommission ?? (float) $service->calculateCommission($basePrice);
        $companyMinimum = $overrideCompanyMinimum ?? max(0.0, round($basePrice - $baseCommission, 2));

        if ($subAgent->isRootAgent()) {
            return [
                'base_price' => round($basePrice, 2),
                'base_commission' => round($baseCommission, 2),
                'company_minimum' => round($companyMinimum, 2),
                'sub_agent_price' => round($basePrice, 2),
                'sub_agent_commission' => round($baseCommission, 2),
                'sub_agent_payable' => round($companyMinimum, 2),
                'parent_margin' => 0.0,
                'margins_breakdown' => [],
            ];
        }

        // 2. Fetch upline ancestors ordered from root master down to direct parent
        $ancestors = AgentLineageService::getAncestors($subAgent);
        if ($ancestors->isEmpty()) {
            $parent = $subAgent->parentAgent ?? User::find($subAgent->parent_id);
            $ancestors = $parent ? collect([$parent]) : collect();
        }

        // Ordered from Root Master (index 0) down to Direct Parent (closest)
        $chain = $ancestors->reverse()->values();
        // Add the filing agent at the end of the node chain
        $nodes = $chain->push($subAgent);

        $currentCost = $companyMinimum;
        $marginsBreakdown = [];
        $finalSubPrice = $basePrice;
        $finalSubCommission = $baseCommission;

        // Traverse each parent -> child link down the hierarchy branch
        for ($i = 0; $i < $nodes->count() - 1; $i++) {
            /** @var User $parent */
            $parent = $nodes[$i];
            /** @var User $child */
            $child = $nodes[$i + 1];

            $rule = SubAgentServicePricing::where('parent_agent_id', $parent->id)
                ->where('service_id', $service->id)
                ->where(function ($q) use ($child) {
                    $q->where('sub_agent_id', $child->id)
                        ->orWhereNull('sub_agent_id');
                })
                ->orderByRaw('sub_agent_id IS NULL ASC')
                ->first();

            if ($rule) {
                $rulePrice = (float) $rule->price;
                $ruleCommission = (float) $rule->commission;
                $rawPayable = max(0.0, round($rulePrice - $ruleCommission, 2));

                // Zero-loss company invariant: child cost cannot be less than parent's current cost
                $childCost = max($currentCost, $rawPayable);
                $stepMargin = max(0.0, round($childCost - $currentCost, 2));

                $stepPrice = $rulePrice;
                $stepCommission = $ruleCommission;
            } else {
                $childCost = $currentCost;
                $stepMargin = 0.0;
                $stepPrice = $basePrice;
                $stepCommission = max(0.0, round($basePrice - $childCost, 2));
            }

            if ($stepMargin > 0) {
                // Tier level relative to the filing agent (Direct parent = Tier 1, Grandparent = Tier 2, etc.)
                $tierLevel = ($nodes->count() - 1) - $i;

                $marginsBreakdown[] = [
                    'agent_id' => $parent->id,
                    'margin' => round($stepMargin, 2),
                    'tier_level' => $tierLevel,
                    'agent' => $parent,
                ];
            }

            $currentCost = $childCost;

            // When reaching the direct parent of the filing sub-agent, record retail price & commission
            if ($i === $nodes->count() - 2) {
                $finalSubPrice = $stepPrice;
                $finalSubCommission = $stepCommission;
            }
        }

        $totalParentMargin = array_sum(array_column($marginsBreakdown, 'margin'));

        return [
            'base_price' => round($basePrice, 2),
            'base_commission' => round($baseCommission, 2),
            'company_minimum' => round($companyMinimum, 2),
            'sub_agent_price' => round($finalSubPrice, 2),
            'sub_agent_commission' => round($finalSubCommission, 2),
            'sub_agent_payable' => round($currentCost, 2),
            'parent_margin' => round($totalParentMargin, 2),
            'margins_breakdown' => $marginsBreakdown,
        ];
    }

    /**
     * Validate whether a proposed pricing configuration satisfies company and upline minimums.
     *
     * @throws InvalidArgumentException
     */
    public static function assertValidPricing(
        Service|float $serviceOrPrice,
        float $commissionOrPrice,
        Service|float|User|null $companyMinimumOrCommission = null,
        ?User $parentAgent = null
    ): void {
        $parent = $parentAgent instanceof User
            ? $parentAgent
            : ($companyMinimumOrCommission instanceof User ? $companyMinimumOrCommission : null);

        if ($serviceOrPrice instanceof Service) {
            $service = $serviceOrPrice;
            $price = (float) $commissionOrPrice;
            $commission = is_numeric($companyMinimumOrCommission) ? (float) $companyMinimumOrCommission : 0.0;

            if ($parent && $parent->parent_id) {
                $minimumCost = self::resolveForSubAgent($service, $parent)['sub_agent_payable'];
            } else {
                $basePrice = (float) $service->price;
                $baseComm = (float) $service->calculateCommission($basePrice);
                $minimumCost = max(0.0, round($basePrice - $baseComm, 2));
            }
        } else {
            $price = (float) $serviceOrPrice;
            $commission = (float) $commissionOrPrice;
            $minimumCost = is_numeric($companyMinimumOrCommission) ? (float) $companyMinimumOrCommission : 0.0;
        }

        $net = round($price - $commission, 2);

        if ($net < $minimumCost) {
            throw new InvalidArgumentException(
                "Sub-agent net payable (₹{$net}) cannot be less than the required minimum cost (₹{$minimumCost})."
            );
        }
    }

    /**
     * Apply sub-agent custom pricing to an iterable/collection of services.
     *
     * @param  iterable<Service>  $services
     */
    public static function applySubAgentPricingToCollection(iterable $services, User $subAgent): void
    {
        $items = $services instanceof AbstractPaginator
            ? $services->getCollection()
            : collect($services);

        if ($items->isEmpty()) {
            return;
        }

        foreach ($items as $service) {
            $pricing = self::resolveForSubAgent($service, $subAgent);
            $service->price = $pricing['sub_agent_price'];
            $service->commission_value = $pricing['sub_agent_commission'];
            if ($pricing['sub_agent_price'] != (float) $service->getOriginal('price')) {
                $service->is_custom_sub_agent_price = true;
            }
        }
    }
}

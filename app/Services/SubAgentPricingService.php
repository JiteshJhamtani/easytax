<?php

namespace App\Services;

use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use InvalidArgumentException;

class SubAgentPricingService
{
    /**
     * Resolve the pricing breakdown for a service submitted by a sub-agent.
     *
     * @param  float|null  $overrideBasePrice  Optional pre-calculated dynamic base price (e.g. from ITR/GST rules)
     * @param  float|null  $overrideBaseCommission  Optional pre-calculated dynamic base commission
     * @return array{
     *     base_price: float,
     *     base_commission: float,
     *     company_minimum: float,
     *     sub_agent_price: float,
     *     sub_agent_commission: float,
     *     sub_agent_payable: float,
     *     parent_margin: float
     * }
     */
    public static function resolveForSubAgent(
        Service $service,
        User $subAgent,
        ?float $overrideBasePrice = null,
        ?float $overrideBaseCommission = null
    ): array {
        $parentAgent = $subAgent->parentAgent;
        $parentId = $parentAgent ? $parentAgent->id : $subAgent->effectiveParentId();

        // 1. Determine company base price & commission
        $basePrice = $overrideBasePrice ?? (float) $service->price;
        $baseCommission = $overrideBaseCommission ?? (float) $service->calculateCommission($basePrice);
        $companyMinimum = max(0.0, round($basePrice - $baseCommission, 2));

        // 2. Query custom pricing rule (check specific sub-agent first, then parent default)
        $rule = SubAgentServicePricing::where('parent_agent_id', $parentId)
            ->where('service_id', $service->id)
            ->where(function ($q) use ($subAgent) {
                $q->where('sub_agent_id', $subAgent->id)
                    ->orWhereNull('sub_agent_id');
            })
            ->orderByRaw('sub_agent_id IS NULL ASC') // specific sub_agent_id comes first
            ->first();

        if ($rule) {
            $subPrice = (float) $rule->price;
            $subCommission = (float) $rule->commission;
            $rawPayable = max(0.0, round($subPrice - $subCommission, 2));

            // CRITICAL ZERO-LOSS SECURITY GUARDRAIL:
            // Sub-agent payable CANNOT be less than company minimum!
            $subPayable = max($companyMinimum, $rawPayable);
            $parentMargin = max(0.0, round($subPayable - $companyMinimum, 2));
        } else {
            // Default: Sub-agent pays the standard agent net price (company minimum)
            $subPrice = $basePrice;
            $subCommission = $baseCommission;
            $subPayable = $companyMinimum;
            $parentMargin = 0.0;
        }

        return [
            'base_price' => round($basePrice, 2),
            'base_commission' => round($baseCommission, 2),
            'company_minimum' => round($companyMinimum, 2),
            'sub_agent_price' => round($subPrice, 2),
            'sub_agent_commission' => round($subCommission, 2),
            'sub_agent_payable' => round($subPayable, 2),
            'parent_margin' => round($parentMargin, 2),
        ];
    }

    /**
     * Validate whether a proposed pricing configuration satisfies company minimum.
     *
     * @throws InvalidArgumentException
     */
    public static function assertValidPricing(Service|float $serviceOrPrice, float $commissionOrPrice, ?float $companyMinimumOrCommission = null): void
    {
        if ($serviceOrPrice instanceof Service) {
            $basePrice = (float) $serviceOrPrice->price;
            $baseComm = (float) $serviceOrPrice->calculateCommission($basePrice);
            $companyMinimum = max(0.0, round($basePrice - $baseComm, 2));
            $price = (float) $commissionOrPrice;
            $commission = (float) $companyMinimumOrCommission;
        } else {
            $price = (float) $serviceOrPrice;
            $commission = (float) $commissionOrPrice;
            $companyMinimum = (float) $companyMinimumOrCommission;
        }

        $net = round($price - $commission, 2);
        if ($net < $companyMinimum) {
            throw new InvalidArgumentException(
                "Sub-agent net payable (₹{$net}) cannot be less than the company minimum receivable (₹{$companyMinimum})."
            );
        }
    }
}

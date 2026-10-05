<?php

namespace App\Services;

use App\Models\AgentMarginLog;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Notifications\ParentMarginCreditedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParentMarginRefundService
{
    /**
     * Atomically process and confirm the extra margin refund for the parent agent(s) across all tiers.
     */
    public static function processMarginRefund(Application $application, ?array $paymentDetails = null): ?AgentMarginLog
    {
        // Only process if this is a sub-agent application with a parent margin > 0
        if (! $application->sub_agent_id || (float) $application->parent_margin <= 0) {
            return null;
        }

        return DB::transaction(function () use ($application, $paymentDetails) {
            // Lock application row to prevent race conditions
            $lockedApp = Application::where('id', $application->id)->lockForUpdate()->first();
            if (! $lockedApp) {
                return null;
            }

            // Strict Idempotency Check: if already processed, return existing log
            $existingLogs = AgentMarginLog::where('application_id', $lockedApp->id)->get();
            if ($existingLogs->isNotEmpty()) {
                return $existingLogs->first();
            }

            $totalMarginAmount = (float) $lockedApp->parent_margin;
            $companyRetained = (float) ($lockedApp->company_minimum_amount ?? round($lockedApp->amount - $lockedApp->commission_amount, 2));
            $subAgentPaid = round($companyRetained + $totalMarginAmount, 2);

            $txnRef = $paymentDetails['id']
                ?? $lockedApp->payment_reference
                ?? ('TXN_MARGIN_'.time().'_'.$lockedApp->id);

            // Resolve multi-tier breakdown
            $subAgent = $lockedApp->subAgent ?? User::find($lockedApp->sub_agent_id);
            $service = $lockedApp->service ?? Service::find($lockedApp->service_id);

            $marginsBreakdown = [];
            if ($subAgent && $service) {
                $pricing = SubAgentPricingService::resolveForSubAgent(
                    $service,
                    $subAgent,
                    null,
                    null,
                    $lockedApp->company_minimum_amount !== null ? (float) $lockedApp->company_minimum_amount : null
                );
                $marginsBreakdown = $pricing['margins_breakdown'] ?? [];
            }

            // Fallback for single-tier or if breakdown was not calculated
            if (empty($marginsBreakdown)) {
                $marginsBreakdown = [
                    [
                        'agent_id' => $lockedApp->agent_id ?? $subAgent?->parent_id,
                        'margin' => $totalMarginAmount,
                        'tier_level' => 1,
                        'agent' => $lockedApp->agent ?? $subAgent?->parentAgent,
                    ],
                ];
            }

            $createdLogs = [];

            foreach ($marginsBreakdown as $tier) {
                $marginAmount = (float) ($tier['margin'] ?? 0.0);
                if ($marginAmount <= 0) {
                    continue;
                }

                $parentAgentId = (int) $tier['agent_id'];
                $tierLevel = (int) ($tier['tier_level'] ?? 1);

                $marginLog = AgentMarginLog::create([
                    'parent_agent_id' => $parentAgentId,
                    'sub_agent_id' => $lockedApp->sub_agent_id,
                    'application_id' => $lockedApp->id,
                    'sub_agent_paid' => $subAgentPaid,
                    'company_retained' => $companyRetained,
                    'margin_amount' => $marginAmount,
                    'tier_level' => $tierLevel,
                    'status' => 'ACCRUED',
                    'refund_reference' => $txnRef,
                    'notes' => "Accrued Tier {$tierLevel} margin of ₹{$marginAmount} recorded for Application #{$lockedApp->id} (awaiting admin payout).",
                ]);

                $createdLogs[] = $marginLog;

                Log::info("Tier {$tierLevel} margin of ₹{$marginAmount} accrued for Application #{$lockedApp->id} to Parent Agent #{$parentAgentId}");

                // Dispatch notification to each earning parent agent
                $earningAgent = $tier['agent'] ?? User::find($parentAgentId);
                if ($earningAgent) {
                    try {
                        $earningAgent->notify(new ParentMarginCreditedNotification($lockedApp, $marginLog));
                    } catch (\Throwable $e) {
                        Log::warning("Could not dispatch margin notification to agent #{$earningAgent->id}: ".$e->getMessage());
                    }
                }
            }

            // Mark application status as ACCRUED
            $lockedApp->update([
                'parent_margin_status' => 'ACCRUED',
                'parent_margin_refunded_at' => null,
            ]);

            return $createdLogs[0] ?? null;
        });
    }
}

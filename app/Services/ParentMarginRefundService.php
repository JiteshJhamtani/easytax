<?php

namespace App\Services;

use App\Models\AgentMarginLog;
use App\Models\Application;
use App\Notifications\ParentMarginCreditedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParentMarginRefundService
{
    /**
     * Atomically process and confirm the extra margin refund for the parent agent.
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
            $existingLog = AgentMarginLog::where('application_id', $lockedApp->id)->first();
            if ($existingLog) {
                return $existingLog;
            }

            $marginAmount = (float) $lockedApp->parent_margin;
            $companyRetained = (float) ($lockedApp->company_minimum_amount ?? round($lockedApp->amount - $lockedApp->commission_amount, 2));
            $subAgentPaid = round($companyRetained + $marginAmount, 2);

            $txnRef = $paymentDetails['id']
                ?? $lockedApp->payment_reference
                ?? ('TXN_MARGIN_'.time().'_'.$lockedApp->id);

            // 1. Create audit log entry
            $marginLog = AgentMarginLog::create([
                'parent_agent_id' => $lockedApp->agent_id,
                'sub_agent_id' => $lockedApp->sub_agent_id,
                'application_id' => $lockedApp->id,
                'sub_agent_paid' => $subAgentPaid,
                'company_retained' => $companyRetained,
                'margin_amount' => $marginAmount,
                'status' => 'ACCRUED',
                'refund_reference' => $txnRef,
                'notes' => "Accrued margin of ₹{$marginAmount} recorded for Application #{$lockedApp->id} (awaiting admin payout).",
            ]);

            // 2. Mark application status as ACCRUED
            $lockedApp->update([
                'parent_margin_status' => 'ACCRUED',
                'parent_margin_refunded_at' => null,
            ]);

            Log::info("Parent margin of ₹{$marginAmount} accrued for Application #{$lockedApp->id} to Parent Agent #{$lockedApp->agent_id}");

            // 3. Dispatch notification to Parent Agent if model exists
            $parentAgent = $lockedApp->agent;
            if ($parentAgent) {
                try {
                    $parentAgent->notify(new ParentMarginCreditedNotification($lockedApp, $marginLog));
                } catch (\Throwable $e) {
                    Log::warning("Could not dispatch margin notification to agent #{$parentAgent->id}: ".$e->getMessage());
                }
            }

            return $marginLog;
        });
    }
}

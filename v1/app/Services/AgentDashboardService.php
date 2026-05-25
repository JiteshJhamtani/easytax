<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AgentDashboardService
{
    public function getStats($agentId)
    {
        return Cache::remember("agent_dashboard_stats_$agentId", 30, function () use ($agentId) {

            return Application::where('agent_id', $agentId)
                ->selectRaw("
                    COUNT(*) as total_applications,
                    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_applications,
                    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as pending_applications,
                    SUM(CASE WHEN payment_status = 'SUCCESS' THEN 1 ELSE 0 END) as successful_payments,
                    SUM(commission_amount) as total_commission,
                    SUM(CASE WHEN payout_id IS NULL THEN commission_amount ELSE 0 END) as pending_commission,
                    SUM(CASE WHEN payout_id IS NOT NULL THEN commission_amount ELSE 0 END) as paid_commission
                ")
                ->first();

        });
    }

    public function getMonthlyApplications($agentId)
    {
        return Application::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('agent_id', $agentId)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getRecentApplications($agentId)
    {
        return Application::with('service')
            ->where('agent_id', $agentId)
            ->latest()
            ->limit(10)
            ->get();
    }
}

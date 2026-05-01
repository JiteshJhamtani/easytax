<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
      // ── KPI Cards ──
        $kpis = [
            // 1. Total Active (Excludes Drafts, Cancelled, and Failed)
            'total_applications' => Application::query()
                ->whereNotIn('status', ['DRAFT', 'CANCELLED'])
                ->where('payment_status', '!=', 'FAILED')
                ->count(),
                
            'completed_applications' => Application::query()->where('status', 'COMPLETED')->count(), 
            
            // 2. Pending Active
            'pending_applications' => Application::query()
                ->whereNotIn('status', ['COMPLETED', 'DRAFT', 'CANCELLED'])
                ->where('payment_status', '!=', 'FAILED')
                ->count(),

            // 3. NEW: Processed (Drafts, Cancelled, or Failed Payments)
            'processed_applications' => Application::query()
                ->where(function ($query) {
                    $query->whereIn('status', ['DRAFT', 'CANCELLED'])
                          ->orWhere('payment_status', 'FAILED');
                })
                ->count(),
            
            // 4. Financials & Agents
            'total_revenue' => Application::query()->where('payment_status', 'SUCCESS')->sum('amount'),
            
            'total_commission' => Application::query()->where('status', 'COMPLETED')->sum('commission_amount'),
            
           'total_agents' => User::query()
                ->where('role', 'AGENT')
                ->where('is_active', true) 
                ->count(),
                
            // NEW: Count active marketers (checking both cases just to be safe!)
            'total_marketers' => User::query()
                ->whereIn('role', ['marketer', 'MARKETER'])
                ->where('is_active', true)
                ->count(),
        ];

        // ── Monthly Charts (last 12 months) ──
        $monthlyData = Application::query()
            ->select(
                DB::raw("DATE_FORMAT(submitted_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as applications_count'),
                DB::raw("SUM(CASE WHEN payment_status = 'SUCCESS' THEN amount ELSE 0 END) as revenue")
            )
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = $monthlyData->pluck('month')->toArray();
        $chartApplications = $monthlyData->pluck('applications_count')->toArray();
        $chartRevenue = $monthlyData->pluck('revenue')->map(fn ($v) => (float) $v)->toArray();

        // ── Top 10 Agents ──
        $topAgents = User::query()
            ->select(
                'users.id',
                'users.agent_code',
                'users.name',
                DB::raw('COUNT(applications.id) as applications_count'),
                DB::raw('COALESCE(SUM(applications.amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(applications.commission_amount), 0) as commission_earned')
            )
            ->join('applications', 'users.id', '=', 'applications.agent_id')
            ->where('users.role', 'AGENT')
            ->whereNotIn('applications.status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('applications.payment_status', '!=', 'FAILED')
            ->groupBy('users.id', 'users.agent_code', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // ── Top 10 Services (Fixed for Cross-Server databases) ──
        
        // 1. We query the LOCAL applications table first (keeping your filters intact)
        $topServicesStats = Application::query()
            ->selectRaw('service_id, COUNT(id) as applications_count, COALESCE(SUM(amount), 0) as revenue')
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->groupBy('service_id')
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get();

        // 2. We ask Laravel to silently fetch the matching Service names from the Master Server
        $topServicesStats->load('service:id,name');

        // 3. Now we format it exactly how your blade file expects it:
        $topServices = $topServicesStats->map(function ($stat) {
            return (object) [
                'id' => $stat->service_id,
                'name' => $stat->service ? $stat->service->name : 'Unknown Service',
                'applications_count' => $stat->applications_count,
                'revenue' => $stat->revenue,
            ];
        });

        // ── Recent 10 Applications ──
        $recentApplications = Application::query()
            ->with(['agent:id,name', 'service:id,name'])
            ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'CANCELED', 'FAILED', 'draft', 'cancelled', 'canceled', 'failed'])
            ->where('payment_status', '!=', 'FAILED')
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'kpis',
            'chartLabels',
            'chartApplications',
            'chartRevenue',
            'topAgents',
            'topServices',
            'recentApplications'
        ));
    }
}
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
            'total_applications' => Application::query()->count(),
            'total_revenue' => Application::query()->where('payment_status', 'SUCCESS')->sum('amount'),
            'total_commission' => Application::query()->sum('commission_amount'),
            'total_agents' => User::query()->where('role', 'AGENT')->count(),
            'pending_applications' => Application::query()->where('status', '!=', 'COMPLETED')->count(),
        ];

        // ── Monthly Charts (last 12 months) ──
        $monthlyData = Application::query()
            ->select(
                DB::raw("DATE_FORMAT(submitted_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as applications_count'),
                DB::raw("SUM(CASE WHEN payment_status = 'SUCCESS' THEN amount ELSE 0 END) as revenue")
            )
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
            ->groupBy('users.id', 'users.agent_code', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // ── Top 10 Services ──
        $topServices = Service::query()
            ->select(
                'services.id',
                'services.name',
                DB::raw('COUNT(applications.id) as applications_count'),
                DB::raw('COALESCE(SUM(applications.amount), 0) as revenue')
            )
            ->join('applications', 'services.id', '=', 'applications.service_id')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get();

        // ── Recent 10 Applications ──
        $recentApplications = Application::query()
            ->with(['agent:id,name', 'service:id,name'])
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
            'recentApplications',
        ));
    }
}

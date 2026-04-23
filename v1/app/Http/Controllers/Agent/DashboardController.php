<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Services\AgentDashboardService;

class DashboardController extends Controller
{
    public function index(AgentDashboardService $dashboard)
    {
        $agentId = auth()->id();

        $stats               = $dashboard->getStats($agentId);
        $monthlyApplications = $dashboard->getMonthlyApplications($agentId);
        $recentApplications  = $dashboard->getRecentApplications($agentId);

        return view('agent.dashboard', [
            'stats'               => $stats,
            'monthlyApplications' => $monthlyApplications,
            'recentApplications'  => $recentApplications
        ]);
    }
}

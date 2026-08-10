<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\AgentDashboardService;

class DashboardController extends Controller
{
    /**
     * Only one dependency injected!
     * The service handles all the heavy lifting.
     */
    public function __construct(
        private AgentDashboardService $dashboardService
    ) {}

    public function index()
    {
        $agentId = auth()->id();

        return view('agent.dashboard', [
            'stats' => $this->dashboardService->getStats($agentId),
            'monthlyApplications' => $this->dashboardService->getMonthlyApplications($agentId),
            'recentApplications' => $this->dashboardService->getRecentApplications($agentId),
            'giftGroups' => $this->dashboardService->getMilestoneGroups($agentId),
        ]);
    }

    public function gifts()
    {
        return view('agent.gifts.index', [
            'giftGroups' => $this->dashboardService->getMilestoneGroups(auth()->id()),
        ]);
    }
}

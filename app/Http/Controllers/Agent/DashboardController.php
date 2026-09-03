<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\AgentDashboardService;
use App\Services\SessionResolver;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Only one dependency injected!
     * The service handles all the heavy lifting.
     */
    public function __construct(
        private AgentDashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $agentId = auth()->id();

        $sessions = SessionResolver::all();
        $currentSessionLabel = $request->get('session', SessionResolver::current()['label']);

        $selectedSession = SessionResolver::fromLabel($currentSessionLabel)
            ?? SessionResolver::current();
        $currentSessionLabel = $selectedSession['label'];

        return view('agent.dashboard', [
            'sessions' => $sessions,
            'currentSessionLabel' => $currentSessionLabel,
            'stats' => $this->dashboardService->getStats($agentId, $currentSessionLabel),
            'monthlyApplications' => $this->dashboardService->getMonthlyApplications($agentId, $currentSessionLabel),
            'recentApplications' => $this->dashboardService->getRecentApplications($agentId, $currentSessionLabel),
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

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
        $user = auth()->user();
        $isSubAgent = $user->isSubAgent();
        $agentId = $user->effectiveParentId();
        $subAgentId = $isSubAgent ? $user->id : null;

        $sessions = SessionResolver::all();
        $currentSessionLabel = SessionResolver::activeSessionLabel($request->get('session'));

        $teamStats = ! $isSubAgent ? $this->dashboardService->getTeamStats($agentId, $currentSessionLabel) : null;
        $giftGroups = ! $isSubAgent ? $this->dashboardService->getMilestoneGroups($agentId, $currentSessionLabel) : [];

        return view('agent.dashboard', [
            'sessions' => $sessions,
            'currentSessionLabel' => $currentSessionLabel,
            'stats' => $this->dashboardService->getStats($agentId, $currentSessionLabel, $subAgentId),
            'monthlyApplications' => $this->dashboardService->getMonthlyApplications($agentId, $currentSessionLabel, $subAgentId),
            'recentApplications' => $this->dashboardService->getRecentApplications($agentId, $currentSessionLabel, $subAgentId),
            'giftGroups' => $giftGroups,
            'teamStats' => $teamStats,
            'isSubAgent' => $isSubAgent,
        ]);
    }

    public function gifts()
    {
        if (auth()->user()->isSubAgent()) {
            abort(403, 'Gifts and milestone rewards are accessible to primary agency agents only.');
        }

        $currentSessionLabel = SessionResolver::activeSessionLabel();

        return view('agent.gifts.index', [
            'giftGroups' => $this->dashboardService->getMilestoneGroups(auth()->id(), $currentSessionLabel),
        ]);
    }
}

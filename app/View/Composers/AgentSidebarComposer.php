<?php

namespace App\View\Composers;

use App\Services\SidebarBadgeService;
use Illuminate\View\View;

class AgentSidebarComposer
{
    public function __construct(
        protected SidebarBadgeService $badgeService
    ) {}

    public function compose(View $view): void
    {
        $agentId = auth()->id();
        $view->with('sidebarBadges', $this->badgeService->getAllBadgesForSidebar($agentId));
    }
}

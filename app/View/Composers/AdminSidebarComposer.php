<?php

namespace App\View\Composers;

use App\Services\SessionResolver;
use App\Services\SidebarBadgeService;
use Illuminate\View\View;

class AdminSidebarComposer
{
    public function __construct(
        protected SidebarBadgeService $badgeService
    ) {}

    public function compose(View $view): void
    {
        $sessionLabel = SessionResolver::activeSessionLabel();
        $view->with('sidebarBadges', $this->badgeService->getAllBadgesForSidebar(null, $sessionLabel));
    }
}

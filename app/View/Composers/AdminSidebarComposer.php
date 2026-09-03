<?php

namespace App\View\Composers;

use App\Services\SidebarBadgeService;
use Illuminate\View\View;

class AdminSidebarComposer
{
    public function __construct(
        protected SidebarBadgeService $badgeService
    ) {}

    public function compose(View $view): void
    {
        $view->with('sidebarBadges', $this->badgeService->getAllBadgesForSidebar());
    }
}

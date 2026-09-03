<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SidebarBadgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class TabBadgeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (! auth()->user()->isAdmin() || strtoupper(auth()->user()->role) === 'SUB-ADMIN') {
                    abort(403, 'Unauthorized action.');
                }

                return $next($request);
            }),
        ];
    }

    public function index(SidebarBadgeService $badgeService): View
    {
        $configs = $badgeService->getConfigs();
        $metrics = $badgeService->getMetricDefinitions();
        $colors = $badgeService->getColorOptions();
        $tabCounts = $badgeService->getTabCounts();
        $previewBadges = $badgeService->getAllBadgesForSidebar();

        return view('admin.tab-badges.index', compact(
            'configs',
            'metrics',
            'colors',
            'tabCounts',
            'previewBadges'
        ));
    }

    public function update(Request $request, SidebarBadgeService $badgeService): RedirectResponse
    {
        $validated = $request->validate([
            'badges' => 'required|array|min:1|max:4',
            'badges.*.label' => 'required|string|max:50',
            'badges.*.metric' => 'required|string',
            'badges.*.color' => 'required|string',
            'badges.*.icon' => 'nullable|string|max:50',
            'badges.*.tooltip' => 'nullable|string|max:100',
            'badges.*.is_active' => 'nullable',
        ]);

        $sanitizedConfigs = [];
        $allowedMetrics = array_keys($badgeService->getMetricDefinitions());
        $allowedColors = array_keys($badgeService->getColorOptions());

        foreach ($validated['badges'] as $index => $item) {
            $metric = in_array($item['metric'], $allowedMetrics, true) ? $item['metric'] : 'pending';
            $color = in_array($item['color'], $allowedColors, true) ? $item['color'] : 'blue';

            $sanitizedConfigs[] = [
                'id' => 'badge_'.($index + 1),
                'label' => trim(strip_tags($item['label'])),
                'metric' => $metric,
                'color' => $color,
                'icon' => ! empty($item['icon']) ? trim(strip_tags($item['icon'])) : null,
                'tooltip' => ! empty($item['tooltip']) ? trim(strip_tags($item['tooltip'])) : '{count}',
                'is_active' => isset($item['is_active']) && ($item['is_active'] === '1' || $item['is_active'] === 1 || $item['is_active'] === true || $item['is_active'] === 'on'),
            ];
        }

        $badgeService->saveConfigs($sanitizedConfigs);

        return back()->with('success', 'Sidebar tab badges updated successfully!');
    }

    public function reset(SidebarBadgeService $badgeService): RedirectResponse
    {
        $badgeService->saveConfigs($badgeService->getDefaultConfigs());

        return back()->with('success', 'Reset sidebar tab badges to defaults.');
    }
}

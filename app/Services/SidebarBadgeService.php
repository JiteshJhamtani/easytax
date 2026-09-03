<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SidebarBadgeService
{
    public const CACHE_KEY = 'admin_sidebar_tab_counts';

    public const CACHE_TTL = 60; // 60 seconds

    /**
     * Get all available metric definitions.
     *
     * @return array<string, string>
     */
    public function getMetricDefinitions(): array
    {
        return [
            'today' => "Today's Volume (Received Today)",
            'pending' => 'Pending Review (Awaiting Completion)',
            'submitted' => 'Submitted (New)',
            'in_progress' => 'In Progress (Processing)',
            'under_review' => 'Under Review',
            'docs_required' => 'Documents Required',
            'completed_today' => 'Completed Today',
            'completed' => 'Total Completed',
            'failed_payment' => 'Failed Payments',
            'total_volume' => 'Total Volume (All Time)',
        ];
    }

    /**
     * Available color options for badges.
     *
     * @return array<string, array{name: string, bg: string, text: string, class: string}>
     */
    public function getColorOptions(): array
    {
        return [
            'red' => [
                'name' => 'Notification Red (Alert)',
                'bg' => '#ef4444',
                'text' => '#ffffff',
                'class' => 'sb-badge--red',
            ],
            'blue' => [
                'name' => 'Royal Blue (Informational)',
                'bg' => '#3b82f6',
                'text' => '#ffffff',
                'class' => 'sb-badge--blue',
            ],
            'amber' => [
                'name' => 'Vibrant Orange (Warning)',
                'bg' => '#f97316',
                'text' => '#ffffff',
                'class' => 'sb-badge--amber',
            ],
            'green' => [
                'name' => 'Emerald Green (Success)',
                'bg' => '#10b981',
                'text' => '#ffffff',
                'class' => 'sb-badge--green',
            ],
            'purple' => [
                'name' => 'Purple Violet',
                'bg' => '#8b5cf6',
                'text' => '#ffffff',
                'class' => 'sb-badge--purple',
            ],
            'pink' => [
                'name' => 'Vibrant Pink',
                'bg' => '#ec4899',
                'text' => '#ffffff',
                'class' => 'sb-badge--pink',
            ],
            'teal' => [
                'name' => 'Teal Cyan',
                'bg' => '#06b6d4',
                'text' => '#ffffff',
                'class' => 'sb-badge--teal',
            ],
            'indigo' => [
                'name' => 'Deep Indigo',
                'bg' => '#6366f1',
                'text' => '#ffffff',
                'class' => 'sb-badge--indigo',
            ],
        ];
    }

    /**
     * Default badge slot configurations.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDefaultConfigs(): array
    {
        return [
            [
                'id' => 'badge_1',
                'label' => "Today's Volume",
                'metric' => 'today',
                'color' => 'blue',
                'tooltip' => "Today's Applications: {count}",
                'is_active' => true,
            ],
            [
                'id' => 'badge_2',
                'label' => 'Pending Review',
                'metric' => 'pending',
                'color' => 'red',
                'tooltip' => 'Pending Review: {count}',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get stored badge configurations.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getConfigs(): array
    {
        $raw = setting('sidebar_badge_configs');

        if (empty($raw)) {
            return $this->getDefaultConfigs();
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) && ! empty($decoded) ? $decoded : $this->getDefaultConfigs();
    }

    /**
     * Save badge configurations and clear cache.
     *
     * @param  array<int, array<string, mixed>>  $configs
     */
    public function saveConfigs(array $configs): void
    {
        Setting::set('sidebar_badge_configs', json_encode(array_values($configs)));
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Compute and cache application counts grouped by sidebar tab key in a single SQL query.
     *
     * @return array<string, array<string, int>>
     */
    public function getTabCounts(?int $agentId = null, ?string $sessionLabel = null): array
    {
        $sessionLabel = $sessionLabel ?? SessionResolver::activeSessionLabel();
        $safeSession = preg_replace('/[^A-Za-z0-9_-]/', '_', $sessionLabel);
        $cacheKey = $agentId ? self::CACHE_KEY.'_agent_'.$agentId.'_'.$safeSession : self::CACHE_KEY.'_'.$safeSession;

        return Cache::remember($cacheKey, now()->addSeconds(self::CACHE_TTL), function () use ($agentId, $sessionLabel) {
            $today = now()->toDateString();
            $bounds = SessionResolver::fromLabel($sessionLabel);

            $specialServiceIds = Service::whereIn('slug', ['itr-filing', 'gst-registration', 'gst-return-filing'])
                ->pluck('id', 'slug')
                ->toArray();

            $itrId = (int) ($specialServiceIds['itr-filing'] ?? 0);
            $gstRegId = (int) ($specialServiceIds['gst-registration'] ?? 0);
            $gstReturnId = (int) ($specialServiceIds['gst-return-filing'] ?? 0);

            $rows = DB::table('applications as a')
                ->whereNull('a.deleted_at')
                ->where(function ($q) use ($sessionLabel, $bounds) {
                    $q->where('a.session_label', $sessionLabel);
                    if ($bounds) {
                        $q->orWhere(function ($sub) use ($bounds) {
                            $sub->whereNull('a.session_label')
                                ->whereBetween('a.created_at', [$bounds['from'], $bounds['to']]);
                        });
                    }
                })
                ->when($agentId !== null, function ($query) use ($agentId) {
                    $query->where('a.agent_id', $agentId);
                })
                ->selectRaw("
                    CASE 
                        WHEN a.status IN ('DRAFT', 'CANCELLED', 'FAILED') OR (a.payment_status IN ('FAILED', 'PENDING') AND a.status != 'COMPLETED') THEN 'incomplete'
                        WHEN a.service_id = {$itrId} THEN 'itr-filing'
                        WHEN a.service_id = {$gstRegId} THEN 'gst-registration'
                        WHEN a.service_id = {$gstReturnId} THEN 'gst-return-filing'
                        ELSE 'other'
                    END as tab_key,
                    COUNT(*) as total_volume,
                    SUM(CASE WHEN DATE(a.created_at) = '{$today}' THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN a.status != 'COMPLETED' AND a.status NOT IN ('DRAFT', 'CANCELLED', 'FAILED') THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN a.status = 'SUBMITTED' THEN 1 ELSE 0 END) as submitted,
                    SUM(CASE WHEN a.status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN a.status = 'UNDER_REVIEW' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN a.status = 'DOCUMENTS_REQUIRED' THEN 1 ELSE 0 END) as docs_required,
                    SUM(CASE WHEN a.status = 'COMPLETED' AND DATE(a.completed_at) = '{$today}' THEN 1 ELSE 0 END) as completed_today,
                    SUM(CASE WHEN a.status = 'COMPLETED' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN a.payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed_payment
                ")
                ->groupBy('tab_key')
                ->get();

            $counts = [];
            $defaultMetrics = [
                'total_volume' => 0,
                'today' => 0,
                'pending' => 0,
                'submitted' => 0,
                'in_progress' => 0,
                'under_review' => 0,
                'docs_required' => 0,
                'completed_today' => 0,
                'completed' => 0,
                'failed_payment' => 0,
            ];

            $allTabs = ['itr-filing', 'gst-registration', 'gst-return-filing', 'other', 'incomplete'];
            foreach ($allTabs as $tab) {
                $counts[$tab] = $defaultMetrics;
            }

            foreach ($rows as $row) {
                if (isset($counts[$row->tab_key])) {
                    $counts[$row->tab_key] = [
                        'total_volume' => (int) $row->total_volume,
                        'today' => (int) $row->today,
                        'pending' => (int) $row->pending,
                        'submitted' => (int) $row->submitted,
                        'in_progress' => (int) $row->in_progress,
                        'under_review' => (int) $row->under_review,
                        'docs_required' => (int) $row->docs_required,
                        'completed_today' => (int) $row->completed_today,
                        'completed' => (int) $row->completed,
                        'failed_payment' => (int) $row->failed_payment,
                    ];
                }
            }

            return $counts;
        });
    }

    /**
     * Get computed badge objects ready for Blade rendering for all tabs.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getAllBadgesForSidebar(?int $agentId = null, ?string $sessionLabel = null): array
    {
        $sessionLabel = $sessionLabel ?? SessionResolver::activeSessionLabel();
        $configs = $this->getConfigs();
        $counts = $this->getTabCounts($agentId, $sessionLabel);
        $colors = $this->getColorOptions();

        $tabBadges = [];
        $allTabs = ['itr-filing', 'gst-registration', 'gst-return-filing', 'other', 'incomplete'];

        foreach ($allTabs as $tabKey) {
            $tabBadges[$tabKey] = [];
            $tabMetrics = $counts[$tabKey] ?? [];

            foreach ($configs as $cfg) {
                if (! ($cfg['is_active'] ?? true)) {
                    continue;
                }

                $metric = $cfg['metric'] ?? 'pending';
                $count = $tabMetrics[$metric] ?? 0;

                // If count is zero or negative, do not render this badge
                if ($count <= 0) {
                    continue;
                }

                $colorKey = $cfg['color'] ?? 'blue';
                $colorData = $colors[$colorKey] ?? $colors['blue'];

                $tooltip = $cfg['tooltip'] ?? '{count}';
                $tooltip = str_replace('{count}', (string) $count, $tooltip);

                $tabBadges[$tabKey][] = [
                    'count' => $count,
                    'formatted_count' => $count > 999 ? '999+' : (string) $count,
                    'tooltip' => $tooltip,
                    'color_class' => $colorData['class'],
                    'bg' => $colorData['bg'],
                    'text' => $colorData['text'],
                    'icon' => $cfg['icon'] ?? null,
                ];
            }
        }

        return $tabBadges;
    }
}

<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Application;
use App\Models\Gift;
use App\Enums\ApplicationStatus;
use App\FormEngine\Form;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('active', true)
            ->orderBy('sort_order', 'asc') // Sorts by your custom numbers first (0, 1, 2...)
            ->orderBy('name', 'asc')       // If numbers are the same, sorts alphabetically
            ->paginate(12);

        return view('front.pages.services.index', compact('services'));
    }
    // public function show($slug)
    // {
    //     $service = Service::where('slug', $slug)
    //         ->where('active', true)
    //         ->firstOrFail();

    //     $form = Form::fromService($service);

    //     $giftMilestones = $this->getGiftMilestones($service);

    //     return view('front.pages.services.show', compact('service', 'form', 'giftMilestones'));
    // }
    
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $form = Form::fromService($service);

        // Commission vars for the payment confirm popup
        $commissionAmount = $service->calculateCommission((float) $service->price);
        $amountToPay      = max(0, $service->price - $commissionAmount);

        // Gift milestones (existing logic — keep as-is)
        $giftMilestones = $this->getGiftMilestones($service);

        return view('front.pages.services.show', compact(
            'service',
            'form',
            'commissionAmount',
            'amountToPay',
            'giftMilestones',
        ));
    }
    // ── Gift milestone data for the progress bar ──────────────────────────

    private function getGiftMilestones(Service $service): array
    {
        $agent = Auth::user();

        // Only load gifts that are:
        // 1. Active
        // 2. Single-service gifts — ALL conditions across ALL groups
        //    point to this service only (no multi-service gifts here)
        $gifts = Gift::where('is_active', true)
            ->whereHas(
                'conditionGroups.conditions',
                fn($q) =>
                $q->where('service_id', $service->id)
            )
            ->with('conditionGroups.conditions')
            ->get()
            ->filter(function (Gift $gift) use ($service) {
                // Exclude multi-service gifts — keep only gifts where
                // every single condition targets this service
                $allServiceIds = $gift->conditionGroups
                    ->flatMap->conditions
                    ->pluck('service_id')
                    ->unique();

                return $allServiceIds->count() === 1
                    && $allServiceIds->first() == $service->id;
            });

        if ($gifts->isEmpty()) {
            return [];
        }

        // Group by period_type so we show monthly / quarterly / yearly
        // progress bars separately
        $grouped = [];

        foreach ($gifts->groupBy('period_type') as $periodType => $periodGifts) {

            [$from, $to] = $this->periodRange($periodType);

            // Agent's submission count for this service in this period
            $count = Application::where('agent_id', $agent->id)
                ->where('service_id', $service->id)
                ->where('status', ApplicationStatus::COMPLETED)
                ->whereBetween('completed_at', [$from, $to])
                ->count();

            // Sort milestones low → high
            $milestones = $periodGifts
                ->map(fn(Gift $g) => [
                    'id'         => $g->id,
                    'name'       => $g->name,
                    'min_count'  => $g->conditionGroups
                        ->flatMap->conditions
                        ->min('min_count'),
                    'icon'       => $g->icon ?? '🎁',
                    'unlocked'   => false, // set below
                    'banner_url' => $g->hasMedia('gift_banner')
                        ? $g->getFirstMediaUrl('gift_banner')
                        : null,
                ])
                ->sortBy('min_count')
                ->values()
                ->map(function ($m) use ($count) {
                    $m['unlocked'] = $count >= $m['min_count'];
                    $m['needed']   = max(0, $m['min_count'] - $count);
                    return $m;
                })
                ->all();

            $maxThreshold = collect($milestones)->max('min_count');

            $grouped[] = [
                'period_type'    => $periodType,
                'period_label'   => $this->periodLabel($periodType),
                'period_range'   => $this->periodRangeLabel($periodType),
                'count'          => $count,
                'color'          => substr($periodType, 0, 1), // 'm', 'q', 'y'
                'max_threshold'  => $maxThreshold,
                // Progress % capped at 100
                'progress_pct'   => $maxThreshold > 0
                    ? min(100, round(($count / $maxThreshold) * 100))
                    : 100,
                'milestones'     => $milestones,
                'unlocked_count' => collect($milestones)->where('unlocked', true)->count(),
            ];
        }

        // Sort groups: monthly first, then quarterly, then yearly
        $order = ['monthly' => 0, 'quarterly' => 1, 'yearly' => 2];
        usort(
            $grouped,
            fn($a, $b) =>
            ($order[$a['period_type']] ?? 9) <=> ($order[$b['period_type']] ?? 9)
        );

        return $grouped;
    }

    private function periodRange(string $periodType): array
    {
        return match ($periodType) {
            'monthly' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ],
            'quarterly' => [
                now()->startOfQuarter(),
                now()->endOfQuarter(),
            ],
            'yearly' => [
                now()->startOfYear(),
                now()->endOfYear(),
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function periodLabel(string $periodType): string
    {
        return match ($periodType) {
            'monthly' => 'This month',
            'quarterly' => 'This quarter',
            'yearly' => 'This year',
            default => ucfirst($periodType),
        };
    }

    private function periodRangeLabel(string $periodType): string
    {
        return match ($periodType) {
            'monthly' => now()->format('F Y'),
            'quarterly' => now()->startOfQuarter()->format('j F') . ' - ' . now()->endOfQuarter()->format('j F'), 'yearly' => (string) now()->year,
            default => '',
        };
    }
}
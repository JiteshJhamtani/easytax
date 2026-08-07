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

  // ── Gift milestone data for the progress bar ──────────────────────────
    private function getGiftMilestones(Service $service): array
    {
        $agent = Auth::user();

        // 1. Load active gifts where THIS service is part of the required conditions
        $gifts = Gift::where('is_active', true)
            ->whereHas('conditionGroups.conditions', function ($q) use ($service) {
                $q->where('service_id', $service->id);
            })
            ->with('conditionGroups.conditions')
            ->get()
            ->filter(function (Gift $gift) use ($service) {
                // THE FIX: Allow the gift to show up if this service is ANY part of the bundle.
                // (The old code forced it to ONLY be single-service gifts)
                $allServiceIds = $gift->conditionGroups
                    ->flatMap->conditions
                    ->pluck('service_id')
                    ->unique();

                return $allServiceIds->contains($service->id);
            });

        if ($gifts->isEmpty()) {
            return [];
        }

        $grouped = [];
        $annualPackageService = Service::where('slug', 'gst-annual-package')->first();

        foreach ($gifts->groupBy('period_type') as $periodType => $periodGifts) {

            [$from, $to] = $this->periodRange($periodType);

            // 2. THE NEW UPSell LOGIC ("Same Client PAN Matching")
            $query = Application::where('agent_id', $agent->id)
                ->where('service_id', $service->id)
                ->where('status', ApplicationStatus::COMPLETED)
                ->whereBetween('completed_at', [$from, $to]);

            // If we are looking at GST Registration, ENFORCE the Annual Package PAN Match!
            if ($service->slug === 'gst-registration' && $annualPackageService) {
                $query->whereExists(function ($subquery) use ($agent, $annualPackageService) {
                    $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('applications as app2')
                        ->where('app2.agent_id', $agent->id)
                        ->where('app2.service_id', $annualPackageService->id)
                        ->where('app2.status', ApplicationStatus::COMPLETED)
                        // This links them: Ensure the PAN Number in the JSON matches exactly!
                        ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(applications.form_data, '$.pan_number')) = JSON_UNQUOTE(JSON_EXTRACT(app2.form_data, '$.pan_number'))")
                        // Ensure it isn't empty
                        ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(applications.form_data, '$.pan_number')) IS NOT NULL");
                });
            }

            $count = $query->count();

            // 3. Sort milestones low → high
            $milestones = $periodGifts
                ->map(fn(Gift $g) => [
                    'id'         => $g->id,
                    'name'       => $g->name,
                    'min_count'  => $g->conditionGroups
                        ->flatMap->conditions
                        ->where('service_id', $service->id) // Get threshold specifically for this service
                        ->min('min_count') ?? 0,
                    'icon'       => $g->icon ?? '🎁',
                    'unlocked'   => false, 
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
                'color'          => substr($periodType, 0, 1), 
                'max_threshold'  => $maxThreshold,
                'progress_pct'   => $maxThreshold > 0
                    ? min(100, round(($count / $maxThreshold) * 100))
                    : 100,
                'milestones'     => $milestones,
                'unlocked_count' => collect($milestones)->where('unlocked', true)->count(),
            ];
        }

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
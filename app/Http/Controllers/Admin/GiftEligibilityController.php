<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use App\Services\GiftEligibilityService;
use App\Services\GiftPeriodResolver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GiftEligibilityController extends Controller
{
    public function __construct(
        private GiftEligibilityService $eligibility,
        private GiftPeriodResolver $periodResolver,
    ) {
    }

    public function hub(Request $request)
    {
        $gifts = Gift::where('is_active', true)->orderBy('name')->get();

        // AJAX request from DataTables
        if ($request->ajax() && $request->filled('gift_id')) {
            return $this->buildDatatable($request);
        }


        $giftsJson = $gifts->load('conditionGroups.conditions.service')->map(fn($g) => [
            'id'              => $g->id,
            'name'            => $g->name,
            'description'     => $g->description,
            'period_type'     => $g->period_type,
            'is_active'       => $g->is_active,
            'banner_url'      => $g->hasMedia('gift_banner') ? $g->getFirstMediaUrl('gift_banner') : null,
            'conditionGroups' => $g->conditionGroups->map(fn($grp) => [
                'conditions' => $grp->conditions->map(fn($c) => [
                    'service_id'   => $c->service_id,
                    'service_name' => $c->service?->name ?? '—',
                    'min_count'    => $c->min_count,
                ])->values()->all(),
            ])->values()->all(),
        ])->keyBy('id')->toArray();


        return view('admin.gifts.eligibility', [
            'gifts'   => $gifts,
            'gift'    => $request->filled('gift_id')
                ? Gift::findOrFail($request->integer('gift_id'))
                : null,
            'year'    => $request->integer('year', now()->year),
            'quarter' => $request->integer('quarter', now()->quarter),
            'month'   => $request->integer('month', now()->month),
            'filter'  => $request->get('filter', 'all'),
            'giftsJson' => $giftsJson,
        ]);
    }

    private function buildDatatable(Request $request)
    {
        $gift = Gift::with('conditionGroups.conditions')->findOrFail(
            $request->integer('gift_id')
        );

        [$from, $to] = $this->periodResolver->resolve(
            $gift->period_type,
            $request->integer('year', now()->year),
            $request->integer('quarter', now()->quarter),
            $request->integer('month', now()->month),
        );

        // All service IDs this gift cares about
        $serviceIds = $gift->conditionGroups
            ->flatMap(fn($g) => $g->conditions->pluck('service_id'))
            ->unique();

        // Submission counts per agent per service in the period
        $submissions = Application::query()
            ->whereIn('service_id', $serviceIds)
            ->where('status', ApplicationStatus::COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->selectRaw('agent_id, service_id, COUNT(*) as total')
            ->groupBy('agent_id', 'service_id')
            ->get()
            ->groupBy('agent_id');

        // Unique services for column data
        $uniqueConditions = $gift->conditionGroups
            ->flatMap->conditions
            ->unique('service_id')
            ->values();

        $query = \App\Models\User::where('role', 'AGENT')
            ->where('is_active', true)
            ->select(['id', 'name', 'email', 'agent_code']);

        return DataTables::of($query)
            ->addColumn('agent_code', fn($u) => $u->agent_code ?? '—')
            ->addColumn('eligible', function ($user) use ($gift, $submissions) {
                $counts   = $submissions->get($user->id, collect())->keyBy('service_id');
                $eligible = $this->isEligible($gift, $counts);
                return $eligible ? 'yes' : 'no';
            })
            // One column per service
            ->addColumn('counts', function ($user) use ($uniqueConditions, $submissions) {
                $counts = $submissions->get($user->id, collect())->keyBy('service_id');
                $data   = [];
                foreach ($uniqueConditions as $cond) {
                    $data[$cond->service_id] = [
                        'count'     => (int) ($counts->get($cond->service_id)?->total ?? 0),
                        'min_count' => $cond->min_count,
                    ];
                }
                return $data;
            })
            ->filterColumn('eligible', function ($query, $keyword) use ($gift, $submissions) {
                // handled client-side via the eligible column
            })
            ->rawColumns([])
            ->toJson();
    }

    // Groups OR-ed, conditions within group AND-ed
    private function isEligible(Gift $gift, $counts): bool
    {
        foreach ($gift->conditionGroups as $group) {
            $pass = true;
            foreach ($group->conditions as $cond) {
                $total = (int) ($counts->get($cond->service_id)?->total ?? 0);
                if ($total < $cond->min_count) {
                    $pass = false;
                    break;
                }
            }
            if ($pass)
                return true;
        }
        return false;
    }

    public function index(Request $request, Gift $gift)
    {
        return redirect()->route('admin.gifts.eligibility.hub', array_merge(
            ['gift_id' => $gift->id],
            $request->only('year', 'quarter', 'month', 'filter')
        ));
    }
}
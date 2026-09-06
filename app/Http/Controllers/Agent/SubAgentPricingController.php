<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use App\Services\SubAgentPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubAgentPricingController extends Controller
{
    /**
     * Display the pricing configuration matrix for the parent agent's team.
     */
    public function index(Request $request)
    {
        $parent = auth()->user();
        $subAgents = User::where('parent_id', $parent->id)->orderBy('name')->get();
        $selectedSubAgentId = $request->query('sub_agent_id');

        $services = Service::where('active', true)->orderBy('name')->get();

        // Load existing pricing rules for this parent (scoped optionally to specific sub-agent or agency-wide)
        $rulesQuery = SubAgentServicePricing::where('parent_agent_id', $parent->id);
        if ($selectedSubAgentId) {
            $rulesQuery->where('sub_agent_id', $selectedSubAgentId);
        } else {
            $rulesQuery->whereNull('sub_agent_id');
        }
        $existingRules = $rulesQuery->get()->keyBy('service_id');

        $pricingRows = $services->map(function ($service) use ($existingRules) {
            $basePrice = (float) $service->price;
            $baseCommission = (float) $service->calculateCommission($basePrice);
            $companyMinimum = max(0.0, round($basePrice - $baseCommission, 2));

            $rule = $existingRules->get($service->id);
            $subPrice = $rule ? (float) $rule->price : $basePrice;
            $subCommission = $rule ? (float) $rule->commission : $baseCommission;
            $subPayable = max(0.0, round($subPrice - $subCommission, 2));
            $margin = max(0.0, round($subPayable - $companyMinimum, 2));

            return [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'service_slug' => $service->slug,
                'base_price' => $basePrice,
                'base_commission' => $baseCommission,
                'company_minimum' => $companyMinimum,
                'sub_price' => $subPrice,
                'sub_commission' => $subCommission,
                'sub_payable' => $subPayable,
                'margin' => $margin,
                'is_customized' => $rule !== null,
            ];
        });

        return view('agent.sub_agents.pricing', compact('pricingRows', 'subAgents', 'selectedSubAgentId'));
    }

    /**
     * Save/update the team pricing rules.
     */
    public function update(Request $request, SubAgentPricingService $pricingService)
    {
        $parent = auth()->user();

        $data = $request->validate([
            'sub_agent_id' => 'nullable|exists:users,id',
            'pricing' => 'required|array|min:1',
            'pricing.*.service_id' => 'required|exists:services,id',
            'pricing.*.price' => 'required|numeric|min:0',
            'pricing.*.commission' => 'required|numeric|min:0',
        ]);

        $subAgentId = $data['sub_agent_id'] ?? null;
        if ($subAgentId) {
            // Verify this sub-agent belongs to the parent
            $subAgent = User::where('id', $subAgentId)->where('parent_id', $parent->id)->firstOrFail();
        }

        $services = Service::whereIn('id', collect($data['pricing'])->pluck('service_id'))->get()->keyBy('id');
        $errors = [];

        // Pre-validate all rules against the zero-loss company invariant
        foreach ($data['pricing'] as $row) {
            $service = $services->get($row['service_id']);
            if (! $service) {
                continue;
            }

            $basePrice = (float) $service->price;
            $baseCommission = (float) $service->calculateCommission($basePrice);
            $companyMinimum = max(0.0, round($basePrice - $baseCommission, 2));

            $price = (float) $row['price'];
            $commission = (float) $row['commission'];

            try {
                $pricingService->assertValidPricing($price, $commission, $companyMinimum);
            } catch (\InvalidArgumentException $e) {
                $errors[] = "For {$service->name}: ".$e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return back()->withInput()->with('error', implode('<br>', $errors));
        }

        // Save atomically
        DB::transaction(function () use ($data, $parent, $subAgentId) {
            foreach ($data['pricing'] as $row) {
                SubAgentServicePricing::updateOrCreate(
                    [
                        'parent_agent_id' => $parent->id,
                        'sub_agent_id' => $subAgentId,
                        'service_id' => $row['service_id'],
                    ],
                    [
                        'price' => $row['price'],
                        'commission' => $row['commission'],
                    ]
                );
            }
        });

        $targetText = $subAgentId ? 'for selected team member' : 'for entire agency team';

        return back()->with('success', "Team pricing rules {$targetText} updated successfully!");
    }
}

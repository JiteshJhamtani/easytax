<?php

use App\Models\AgentMarginLog;
use App\Models\Application;
use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use App\Notifications\ParentMarginCreditedNotification;
use App\Services\AgentCodeService;
use App\Services\AgentLineageService;
use App\Services\AgentMarginPayoutService;
use App\Services\ParentMarginRefundService;
use App\Services\SubAgentPricingService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    // 1. Level 1: Root Master Agent A
    $this->rootAgentA = User::factory()->create([
        'name' => 'Master Agent A',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => null,
        'agent_code' => 'AGT-800001',
        'can_recruit' => true,
    ]);
    AgentLineageService::assignRoot($this->rootAgentA);

    // 2. Level 2: Child Agent B (under A)
    $this->childAgentB = User::factory()->create([
        'name' => 'Child Agent B',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->rootAgentA->id,
        'agent_code' => AgentCodeService::generateSubAgentCode($this->rootAgentA),
        'can_recruit' => true,
    ]);
    AgentLineageService::assignParent($this->childAgentB, $this->rootAgentA);

    // 3. Level 3: Child Agent C (under B)
    $this->childAgentC = User::factory()->create([
        'name' => 'Child Agent C',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->childAgentB->id,
        'agent_code' => AgentCodeService::generateSubAgentCode($this->childAgentB),
        'can_recruit' => true,
    ]);
    AgentLineageService::assignParent($this->childAgentC, $this->childAgentB);

    // 4. Level 4: Leaf Sub-Agent D (under C, can_recruit = false)
    $this->leafAgentD = User::factory()->create([
        'name' => 'Leaf Agent D',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->childAgentC->id,
        'agent_code' => AgentCodeService::generateSubAgentCode($this->childAgentC),
        'can_recruit' => false,
    ]);
    AgentLineageService::assignParent($this->leafAgentD, $this->childAgentC);

    // Service: Base Price = 1000, Base Commission = 200 -> Company Min = 800
    $this->service = Service::updateOrCreate(
        ['slug' => 'test-multi-tier-service'],
        [
            'name' => 'Multi-Tier Network Service',
            'price' => 1000.00,
            'commission_type' => 'flat',
            'commission_value' => 200.00,
            'active' => true,
        ]
    );
});

/*
|--------------------------------------------------------------------------
| 1. Ancestry Path & Materialized Tree Hierarchy
|--------------------------------------------------------------------------
*/

it('correctly calculates ancestry path and depth for all levels', function () {
    expect($this->rootAgentA->ancestry_path)->toBe("/{$this->rootAgentA->id}/")
        ->and($this->rootAgentA->depth)->toBe(1)
        ->and($this->rootAgentA->isRootAgent())->toBeTrue();

    expect($this->childAgentB->ancestry_path)->toBe("/{$this->rootAgentA->id}/{$this->childAgentB->id}/")
        ->and($this->childAgentB->depth)->toBe(2);

    expect($this->childAgentC->ancestry_path)->toBe("/{$this->rootAgentA->id}/{$this->childAgentB->id}/{$this->childAgentC->id}/")
        ->and($this->childAgentC->depth)->toBe(3);

    expect($this->leafAgentD->ancestry_path)->toBe("/{$this->rootAgentA->id}/{$this->childAgentB->id}/{$this->childAgentC->id}/{$this->leafAgentD->id}/")
        ->and($this->leafAgentD->depth)->toBe(4);
});

it('resolves ancestors and downline correctly', function () {
    // Ancestors of C (ordered closest to root: B, then A)
    $ancestorsOfC = AgentLineageService::getAncestors($this->childAgentC);
    expect($ancestorsOfC->pluck('id')->all())->toBe([$this->childAgentB->id, $this->rootAgentA->id]);

    // Root agent of D
    expect($this->leafAgentD->rootAgent()->id)->toBe($this->rootAgentA->id);

    // Descendants of A (B, C, D)
    $descendantIdsA = AgentLineageService::getDescendantIds($this->rootAgentA);
    expect($descendantIdsA)->toContain($this->childAgentB->id, $this->childAgentC->id, $this->leafAgentD->id);

    // Descendants of B (C, D)
    $descendantIdsB = AgentLineageService::getDescendantIds($this->childAgentB);
    expect($descendantIdsB)->toBe([$this->childAgentC->id, $this->leafAgentD->id]);

    // Descendants of D (empty)
    $descendantIdsD = AgentLineageService::getDescendantIds($this->leafAgentD);
    expect($descendantIdsD)->toBeEmpty();
});

it('prevents cyclical assignment in agent hierarchy', function () {
    expect(AgentLineageService::wouldCreateCycle($this->rootAgentA, $this->leafAgentD))->toBeTrue();
    expect(AgentLineageService::wouldCreateCycle($this->childAgentB, $this->childAgentC))->toBeTrue();
    expect(AgentLineageService::wouldCreateCycle($this->rootAgentA, $this->rootAgentA))->toBeTrue();

    // False for valid parent assignment
    expect(AgentLineageService::wouldCreateCycle($this->leafAgentD, $this->rootAgentA))->toBeFalse();
});

it('generates multi-tier agent codes dynamically', function () {
    expect($this->childAgentB->agent_code)->toBe('AGT-800001-01');
    expect($this->childAgentC->agent_code)->toBe('AGT-800001-01-01');
    expect($this->leafAgentD->agent_code)->toBe('AGT-800001-01-01-01');
});

/*
|--------------------------------------------------------------------------
| 2. Cascading Pricing Waterfall & Margins
|--------------------------------------------------------------------------
*/

it('calculates cascading waterfall margins across multi-tier network', function () {
    // Level 1 (A) sets pricing for Level 2 (B):
    // Price = 1100, Commission = 100 -> B payable = 1000.
    // A's step margin = 1000 - 800 (company min) = 200.
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentB->id,
        'service_id' => $this->service->id,
        'price' => 1100.00,
        'commission' => 100.00,
    ]);

    // Level 2 (B) sets pricing for Level 3 (C):
    // Price = 1350, Commission = 50 -> C payable = 1300.
    // B's step margin = 1300 - 1000 (B's cost) = 300.
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'price' => 1350.00,
        'commission' => 50.00,
    ]);

    $pricing = SubAgentPricingService::resolveForSubAgent($this->service, $this->childAgentC);

    expect($pricing['company_minimum'])->toBe(800.00)
        ->and($pricing['sub_agent_price'])->toBe(1350.00)
        ->and($pricing['sub_agent_commission'])->toBe(50.00)
        ->and($pricing['sub_agent_payable'])->toBe(1300.00)
        ->and($pricing['parent_margin'])->toBe(500.00); // 300 + 200

    expect($pricing['margins_breakdown'])->toHaveCount(2);

    // Tier 1: Direct parent B earns 300
    expect($pricing['margins_breakdown'][1]['agent_id'])->toBe($this->childAgentB->id)
        ->and($pricing['margins_breakdown'][1]['margin'])->toBe(300.00)
        ->and($pricing['margins_breakdown'][1]['tier_level'])->toBe(1);

    // Tier 2: Grandparent A earns 200
    expect($pricing['margins_breakdown'][0]['agent_id'])->toBe($this->rootAgentA->id)
        ->and($pricing['margins_breakdown'][0]['margin'])->toBe(200.00)
        ->and($pricing['margins_breakdown'][0]['tier_level'])->toBe(2);
});

it('enforces zero-loss platform protection across all tiers', function () {
    // Root cost is 800. If A tries to set pricing for B below 800, reject it.
    expect(function () {
        SubAgentPricingService::assertValidPricing($this->service, 700.00, 0.00, $this->rootAgentA);
    })->toThrow(InvalidArgumentException::class);

    // A sets B's price to 1000 (valid)
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentB->id,
        'service_id' => $this->service->id,
        'price' => 1000.00,
        'commission' => 0.00,
    ]);

    // B's cost is now 1000. If B tries to set C's payable to 900 (below B's cost of 1000), reject it!
    expect(function () {
        SubAgentPricingService::assertValidPricing($this->service, 900.00, 0.00, $this->childAgentB);
    })->toThrow(InvalidArgumentException::class);
});

/*
|--------------------------------------------------------------------------
| 3. Payment Settlement & Multi-Tier Margin Accrual
|--------------------------------------------------------------------------
*/

it('accrues margin logs for each earning ancestor upon payment confirmation', function () {
    Notification::fake();

    // A sets B's cost: 1000 (A margin = 200)
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentB->id,
        'service_id' => $this->service->id,
        'price' => 1100.00,
        'commission' => 100.00,
    ]);

    // B sets C's cost: 1300 (B margin = 300)
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'price' => 1350.00,
        'commission' => 50.00,
    ]);

    // C files application
    $application = Application::create([
        'agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Multi-Tier Client'],
        'amount' => 1350.00,
        'commission_amount' => 50.00,
        'sub_agent_amount' => 1350.00,
        'sub_agent_commission' => 50.00,
        'company_minimum_amount' => 800.00,
        'parent_margin' => 500.00,
        'parent_margin_status' => 'PENDING',
        'payment_status' => 'PAID',
        'status' => 'SUBMITTED',
    ]);

    // Process margin refund
    $log = ParentMarginRefundService::processMarginRefund($application);

    expect($log)->toBeInstanceOf(AgentMarginLog::class);

    // Verify 2 margin logs exist for this application
    $logs = AgentMarginLog::where('application_id', $application->id)->get();
    expect($logs)->toHaveCount(2);

    $tier1Log = $logs->firstWhere('parent_agent_id', $this->childAgentB->id);
    expect($tier1Log)->not->toBeNull()
        ->and((float) $tier1Log->margin_amount)->toBe(300.00)
        ->and((int) $tier1Log->tier_level)->toBe(1)
        ->and($tier1Log->status)->toBe('ACCRUED');

    $tier2Log = $logs->firstWhere('parent_agent_id', $this->rootAgentA->id);
    expect($tier2Log)->not->toBeNull()
        ->and((float) $tier2Log->margin_amount)->toBe(200.00)
        ->and((int) $tier2Log->tier_level)->toBe(2)
        ->and($tier2Log->status)->toBe('ACCRUED');

    // Mathematical zero-loss invariant check:
    // Company Retained (800) + Tier 1 Margin (300) + Tier 2 Margin (200) = Paid by subagent (1300)
    expect((float) $tier1Log->company_retained + (float) $tier1Log->margin_amount + (float) $tier2Log->margin_amount)
        ->toBe(1300.00);

    // Verify notifications sent to both A and B
    Notification::assertSentTo($this->childAgentB, ParentMarginCreditedNotification::class);
    Notification::assertSentTo($this->rootAgentA, ParentMarginCreditedNotification::class);

    // Idempotency: calling again should not create additional logs
    ParentMarginRefundService::processMarginRefund($application);
    expect(AgentMarginLog::where('application_id', $application->id)->count())->toBe(2);
});

/*
|--------------------------------------------------------------------------
| 4. Permissions & Lineage Scoping
|--------------------------------------------------------------------------
*/

it('allows sub-agents with can_recruit to manage team and blocks leaf agents', function () {
    // Child Agent B (can_recruit = true) CAN access team management
    $this->actingAs($this->childAgentB)
        ->get(route('agent.sub-agents.index'))
        ->assertSuccessful();

    $this->actingAs($this->childAgentB)
        ->get(route('agent.team-pricing.index'))
        ->assertSuccessful();

    $this->actingAs($this->childAgentB)
        ->get(route('agent.margin-ledger.index'))
        ->assertSuccessful();

    // Leaf Agent D (can_recruit = false) CANNOT access team management
    $this->actingAs($this->leafAgentD)
        ->get(route('agent.sub-agents.index'))
        ->assertForbidden();

    $this->actingAs($this->leafAgentD)
        ->get(route('agent.team-pricing.index'))
        ->assertForbidden();

    $this->actingAs($this->leafAgentD)
        ->get(route('agent.margin-ledger.index'))
        ->assertForbidden();
});

it('allows upline agents to view downline applications and blocks unauthorized agents', function () {
    // D files an application
    $appD = Application::create([
        'agent_id' => $this->childAgentC->id,
        'sub_agent_id' => $this->leafAgentD->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Leaf Client'],
        'amount' => 1000.00,
        'commission_amount' => 200.00,
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    // Unrelated external agent
    $otherAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'agent_code' => 'AGT-999999',
    ]);
    AgentLineageService::assignRoot($otherAgent);

    // D can access own application
    $this->actingAs($this->leafAgentD)
        ->get(route('agent.applications.show', $appD->id))
        ->assertSuccessful();

    // C (direct parent) can access
    $this->actingAs($this->childAgentC)
        ->get(route('agent.applications.show', $appD->id))
        ->assertSuccessful();

    // B (grandparent) can access
    $this->actingAs($this->childAgentB)
        ->get(route('agent.applications.show', $appD->id))
        ->assertSuccessful();

    // A (root master) can access
    $this->actingAs($this->rootAgentA)
        ->get(route('agent.applications.show', $appD->id))
        ->assertSuccessful();

    // Unrelated agent cannot access
    $this->actingAs($otherAgent)
        ->get(route('agent.applications.show', $appD->id))
        ->assertForbidden();
});

it('renders tier badges in margin ledger data for upline agents', function () {
    $app = Application::create([
        'agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Badge Client'],
        'amount' => 1350.00,
        'commission_amount' => 50.00,
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    // Create Tier 1 log for B and Tier 2 log for A
    AgentMarginLog::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 300.00,
        'tier_level' => 1,
        'status' => 'ACCRUED',
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 200.00,
        'tier_level' => 2,
        'status' => 'ACCRUED',
    ]);

    // B queries margin ledger data: should see Tier 1
    $responseB = $this->actingAs($this->childAgentB)
        ->getJson(route('agent.margin-ledger.data'))
        ->assertSuccessful();

    $dataB = $responseB->json('data');
    expect($dataB)->toHaveCount(1)
        ->and($dataB[0]['sub_agent'])->toContain('Tier 1');

    // A queries margin ledger data: should see Tier 2
    $responseA = $this->actingAs($this->rootAgentA)
        ->getJson(route('agent.margin-ledger.data'))
        ->assertSuccessful();

    $dataA = $responseA->json('data');
    expect($dataA)->toHaveCount(1)
        ->and($dataA[0]['sub_agent'])->toContain('Tier 2');
});

it('allows child agent B to recruit a new sub-agent and sets lineage automatically', function () {
    $this->actingAs($this->childAgentB)
        ->post(route('agent.sub-agents.store'), [
            'name' => 'Recruit of B',
            'email' => 'recruit.b@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'can_recruit' => 1,
        ])
        ->assertRedirect(route('agent.sub-agents.index'));

    $newAgent = User::where('email', 'recruit.b@example.com')->first();
    expect($newAgent)->not->toBeNull()
        ->and($newAgent->parent_id)->toBe($this->childAgentB->id)
        ->and($newAgent->ancestry_path)->toBe("/{$this->rootAgentA->id}/{$this->childAgentB->id}/{$newAgent->id}/")
        ->and($newAgent->depth)->toBe(3)
        ->and($newAgent->agent_code)->toStartWith($this->childAgentB->agent_code);
});

it('handles intermediate tier without custom pricing by falling back to parent cost with 0 markup', function () {
    // Only A sets pricing for B: Price = 1100, Commission = 100 -> B payable = 1000. (A margin = 200)
    SubAgentServicePricing::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentB->id,
        'service_id' => $this->service->id,
        'price' => 1100.00,
        'commission' => 100.00,
    ]);

    // B does NOT set pricing for C.
    // Pricing for C should inherit B's cost (1000) with 0 markup for B.
    $pricing = SubAgentPricingService::resolveForSubAgent($this->service, $this->childAgentC);

    expect($pricing['company_minimum'])->toBe(800.00)
        ->and($pricing['sub_agent_payable'])->toBe(1000.00)
        ->and($pricing['parent_margin'])->toBe(200.00); // Only A's margin

    expect($pricing['margins_breakdown'])->toHaveCount(1);
    expect($pricing['margins_breakdown'][0]['agent_id'])->toBe($this->rootAgentA->id)
        ->and($pricing['margins_breakdown'][0]['margin'])->toBe(200.00)
        ->and($pricing['margins_breakdown'][0]['tier_level'])->toBe(2);
});

it('does not generate margin logs when root agent files directly', function () {
    $pricing = SubAgentPricingService::resolveForSubAgent($this->service, $this->rootAgentA);

    expect($pricing['company_minimum'])->toBe(800.00)
        ->and($pricing['sub_agent_payable'])->toBe(800.00)
        ->and($pricing['parent_margin'])->toBe(0.0)
        ->and($pricing['margins_breakdown'])->toBeEmpty();

    $app = Application::create([
        'agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => null,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Root Client'],
        'amount' => 1000.00,
        'commission_amount' => 200.00,
        'company_minimum_amount' => 800.00,
        'parent_margin' => 0.0,
        'parent_margin_status' => 'NONE',
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    $result = ParentMarginRefundService::processMarginRefund($app);
    expect($result)->toBeNull();
    expect(AgentMarginLog::where('application_id', $app->id)->count())->toBe(0);
});

it('isolates downline visibility across complex branching tree structures', function () {
    // Branch 1: A -> B -> C -> D (already set up in beforeEach)
    // Branch 2: A -> B2 -> C2
    $agentB2 = User::factory()->create([
        'name' => 'Branch 2 Agent B2',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->rootAgentA->id,
        'can_recruit' => true,
    ]);
    AgentLineageService::assignParent($agentB2, $this->rootAgentA);

    $agentC2 = User::factory()->create([
        'name' => 'Branch 2 Agent C2',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $agentB2->id,
        'can_recruit' => false,
    ]);
    AgentLineageService::assignParent($agentC2, $agentB2);

    // Root A descendants includes all branches: B, C, D, B2, C2
    $descendantsA = AgentLineageService::getDescendantIds($this->rootAgentA);
    expect($descendantsA)->toHaveCount(5)
        ->and($descendantsA)->toContain($this->childAgentB->id, $this->childAgentC->id, $this->leafAgentD->id, $agentB2->id, $agentC2->id);

    // B1 descendants only contains C and D (NOT B2 or C2)
    $descendantsB1 = AgentLineageService::getDescendantIds($this->childAgentB);
    expect($descendantsB1)->toHaveCount(2)
        ->and($descendantsB1)->toBe([$this->childAgentC->id, $this->leafAgentD->id])
        ->and($descendantsB1)->not->toContain($agentB2->id, $agentC2->id);

    // B2 descendants only contains C2
    $descendantsB2 = AgentLineageService::getDescendantIds($agentB2);
    expect($descendantsB2)->toHaveCount(1)
        ->and($descendantsB2)->toBe([$agentC2->id]);
});

it('voids all multi-tier accrued margin logs when an application is cancelled', function () {
    $app = Application::create([
        'agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Cancel Test Client'],
        'amount' => 1350.00,
        'commission_amount' => 50.00,
        'parent_margin' => 500.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 300.00,
        'tier_level' => 1,
        'status' => 'ACCRUED',
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 200.00,
        'tier_level' => 2,
        'status' => 'ACCRUED',
    ]);

    // Agent C cancels application
    $this->actingAs($this->childAgentC)
        ->patch(route('agent.applications.cancel', $app->id))
        ->assertRedirect();

    $app->refresh();
    expect($app->status->value)->toBe('CANCELLED')
        ->and($app->parent_margin_status)->toBe('CANCELLED');

    // Both Tier 1 and Tier 2 logs must be CANCELLED
    $logs = AgentMarginLog::where('application_id', $app->id)->get();
    expect($logs)->toHaveCount(2);
    expect($logs->every(fn ($l) => $l->status === 'CANCELLED'))->toBeTrue();
});

it('voids accrued margins when application payment is marked REFUNDED by admin', function () {
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    $app = Application::create([
        'agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Refund Test Client'],
        'amount' => 1350.00,
        'commission_amount' => 50.00,
        'parent_margin' => 500.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 300.00,
        'tier_level' => 1,
        'status' => 'ACCRUED',
    ]);

    AgentMarginLog::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 200.00,
        'tier_level' => 2,
        'status' => 'ACCRUED',
    ]);

    // Admin marks payment refunded
    $this->actingAs($admin)
        ->patch(route('admin.applications.updatePaymentStatus', $app->id), [
            'payment_status' => 'REFUNDED',
        ])
        ->assertRedirect();

    $app->refresh();
    expect($app->payment_status->value)->toBe('REFUNDED')
        ->and($app->parent_margin_status)->toBe('CANCELLED');

    $logs = AgentMarginLog::where('application_id', $app->id)->get();
    expect($logs->every(fn ($l) => $l->status === 'CANCELLED'))->toBeTrue();
});

it('supports partial settlement across multiple upline tiers without conflict', function () {
    $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);

    $app = Application::create([
        'agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'service_id' => $this->service->id,
        'form_data' => ['applicant_name' => 'Partial Settlement Client'],
        'amount' => 1350.00,
        'commission_amount' => 50.00,
        'parent_margin' => 500.00,
        'parent_margin_status' => 'ACCRUED',
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    $logB = AgentMarginLog::create([
        'parent_agent_id' => $this->childAgentB->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 300.00,
        'tier_level' => 1,
        'status' => 'ACCRUED',
    ]);

    $logA = AgentMarginLog::create([
        'parent_agent_id' => $this->rootAgentA->id,
        'sub_agent_id' => $this->childAgentC->id,
        'application_id' => $app->id,
        'sub_agent_paid' => 1300.00,
        'company_retained' => 800.00,
        'margin_amount' => 200.00,
        'tier_level' => 2,
        'status' => 'ACCRUED',
    ]);

    $payoutService = app(AgentMarginPayoutService::class);

    // Admin settles Parent B's margins first (Tier 1: 300)
    $payoutService->settle($admin, $this->childAgentB, [
        'transaction_reference' => 'UTR_B_123456',
        'payment_method' => 'bank_transfer',
        'log_ids' => [$logB->id],
    ]);

    $logB->refresh();
    $logA->refresh();
    $app->refresh();

    expect($logB->status)->toBe('PAID')
        ->and($logA->status)->toBe('ACCRUED') // A is still waiting for payout!
        ->and($app->parent_margin_status)->toBe('PARTIALLY_SETTLED');

    // Admin now settles Grandparent A's margins (Tier 2: 200)
    $payoutService->settle($admin, $this->rootAgentA, [
        'transaction_reference' => 'UTR_A_987654',
        'payment_method' => 'upi',
        'log_ids' => [$logA->id],
    ]);

    $logA->refresh();
    $app->refresh();

    expect($logA->status)->toBe('PAID')
        ->and($app->parent_margin_status)->toBe('PAID');
});

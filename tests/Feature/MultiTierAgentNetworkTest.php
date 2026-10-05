<?php

use App\Models\AgentMarginLog;
use App\Models\Application;
use App\Models\Service;
use App\Models\SubAgentServicePricing;
use App\Models\User;
use App\Notifications\ParentMarginCreditedNotification;
use App\Services\AgentCodeService;
use App\Services\AgentLineageService;
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

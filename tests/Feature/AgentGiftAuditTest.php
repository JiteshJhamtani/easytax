<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Gift;
use App\Models\Service;
use App\Models\User;
use App\Services\AgentDashboardService;
use App\Services\SessionResolver;

beforeEach(function () {
    Gift::query()->delete();
    $this->service = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Filing', 'active' => true, 'price' => 100, 'commission_type' => 'flat', 'commission_value' => 10]
    );

    $this->service2 = Service::firstOrCreate(
        ['slug' => 'gst-reg-audit'],
        ['name' => 'GST Registration Audit', 'active' => true, 'price' => 500, 'commission_type' => 'flat', 'commission_value' => 50]
    );

    $this->agent = User::factory()->create([
        'name' => 'Audit Agent One',
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => null,
    ]);

    $this->admin = User::factory()->create([
        'role' => 'ADMIN',
        'is_active' => true,
    ]);
});

it('inspects and verifies that 15 completed ITRs correctly marks 15-target silver coin as completed and shows progress for 25 target', function () {
    // 1. Create 3 tiered monthly gifts for ITR Filing
    $gift5 = Gift::create(['name' => 'Rs 200 Wallet', 'period_type' => 'monthly', 'is_active' => true]);
    $g5 = $gift5->conditionGroups()->create(['sort_order' => 0]);
    $g5->conditions()->create(['service_id' => $this->service->id, 'min_count' => 5]);

    $gift15 = Gift::create(['name' => '2 Gms Silver Coin', 'period_type' => 'monthly', 'is_active' => true]);
    $g15 = $gift15->conditionGroups()->create(['sort_order' => 0]);
    $g15->conditions()->create(['service_id' => $this->service->id, 'min_count' => 15]);

    $gift25 = Gift::create(['name' => 'Xiaomi Powerbank', 'period_type' => 'monthly', 'is_active' => true]);
    $g25 = $gift25->conditionGroups()->create(['sort_order' => 0]);
    $g25->conditions()->create(['service_id' => $this->service->id, 'min_count' => 25]);

    // 2. Create 15 completed applications for this agent this month
    for ($i = 0; $i < 15; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }

    $service = app(AgentDashboardService::class);
    $groups = $service->getMilestoneGroups($this->agent->id);

    expect($groups)->toHaveCount(1);
    $group = $groups[0];

    expect($group['agent_count'])->toBe(15);
    expect($group['milestones'])->toHaveCount(3);

    // Milestone 0: Rs 200 Wallet (5) -> unlocked: true
    expect($group['milestones'][0]['name'])->toBe('Rs 200 Wallet');
    expect($group['milestones'][0]['min_count'])->toBe(5);
    expect($group['milestones'][0]['unlocked'])->toBeTrue();

    // Milestone 1: 2 Gms Silver Coin (15) -> unlocked: true
    expect($group['milestones'][1]['name'])->toBe('2 Gms Silver Coin');
    expect($group['milestones'][1]['min_count'])->toBe(15);
    expect($group['milestones'][1]['unlocked'])->toBeTrue();

    // Milestone 2: Xiaomi Powerbank (25) -> unlocked: false, needed: 10
    expect($group['milestones'][2]['name'])->toBe('Xiaomi Powerbank');
    expect($group['milestones'][2]['min_count'])->toBe(25);
    expect($group['milestones'][2]['unlocked'])->toBeFalse();
    expect($group['milestones'][2]['needed'])->toBe(10);

    // Now test the agent view response: every milestone card must be rendered in HTML
    $response = $this->actingAs($this->agent)->get(route('agent.gifts'));
    $response->assertSuccessful();
    $response->assertSee('Rs 200 Wallet');
    $response->assertSee('2 Gms Silver Coin');
    $response->assertSee('Xiaomi Powerbank');
    $response->assertSee('Target');
    $response->assertSee('Eligible');
    $response->assertSee('Pending');
});

it('ignores non-completed applications or applications completed outside the period window', function () {
    $gift = Gift::create(['name' => 'Monthly Gift 10', 'period_type' => 'monthly', 'is_active' => true]);
    $g = $gift->conditionGroups()->create(['sort_order' => 0]);
    $g->conditions()->create(['service_id' => $this->service->id, 'min_count' => 10]);

    // 5 COMPLETED this month
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }

    // 5 SUBMITTED / PENDING this month (should NOT count toward gift)
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::SUBMITTED,
            'payment_status' => PaymentStatus::PAID,
            'created_at' => now(),
            'amount' => 100,
        ]);
    }

    // 5 COMPLETED last month (should NOT count for monthly gift)
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now()->subMonths(2),
            'amount' => 100,
        ]);
    }

    $service = app(AgentDashboardService::class);
    $groups = $service->getMilestoneGroups($this->agent->id);

    expect($groups[0]['agent_count'])->toBe(5);
    expect($groups[0]['milestones'][0]['unlocked'])->toBeFalse();
    expect($groups[0]['milestones'][0]['needed'])->toBe(5);
});

it('handles composite multi-service conditions requiring multiple services to qualify', function () {
    $bundleGift = Gift::create(['name' => 'Mega Combo Kit', 'period_type' => 'monthly', 'is_active' => true]);
    $group = $bundleGift->conditionGroups()->create(['sort_order' => 0]);
    $group->conditions()->create(['service_id' => $this->service->id, 'min_count' => 5]);
    $group->conditions()->create(['service_id' => $this->service2->id, 'min_count' => 3]);

    // Agent has 5 ITR filings and only 2 GST Registrations -> Not unlocked yet
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }
    for ($i = 0; $i < 2; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service2->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 500,
        ]);
    }

    $service = app(AgentDashboardService::class);
    $groups = $service->getMilestoneGroups($this->agent->id);
    expect($groups[0]['milestones'][0]['unlocked'])->toBeFalse();

    // Complete 1 more GST registration
    Application::create([
        'agent_id' => $this->agent->id,
        'service_id' => $this->service2->id,
        'status' => ApplicationStatus::COMPLETED,
        'payment_status' => PaymentStatus::PAID,
        'completed_at' => now(),
        'amount' => 500,
    ]);

    $groupsAfter = $service->getMilestoneGroups($this->agent->id);
    expect($groupsAfter[0]['milestones'][0]['unlocked'])->toBeTrue();
});

it('calculates session-based gift milestones and filters accurately', function () {
    $sessionGift = Gift::create(['name' => 'Session Star Award', 'period_type' => 'session', 'is_active' => true]);
    $group = $sessionGift->conditionGroups()->create(['sort_order' => 0]);
    $group->conditions()->create(['service_id' => $this->service->id, 'min_count' => 20]);

    $currentSession = SessionResolver::current();

    // 20 applications completed within the current session
    for ($i = 0; $i < 20; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => $currentSession['from']->copy()->addDay(),
            'amount' => 100,
        ]);
    }

    $service = app(AgentDashboardService::class);
    $groups = $service->getMilestoneGroups($this->agent->id, $currentSession['label']);

    expect($groups)->toHaveCount(1);
    expect($groups[0]['agent_count'])->toBe(20);
    expect($groups[0]['milestones'][0]['unlocked'])->toBeTrue();
});

it('admin gift eligibility hub returns accurate stats and filters out sub-agents', function () {
    $gift = Gift::create(['name' => 'Admin Test Gift', 'period_type' => 'monthly', 'is_active' => true]);
    $group = $gift->conditionGroups()->create(['sort_order' => 0]);
    $group->conditions()->create(['service_id' => $this->service->id, 'min_count' => 5]);

    // Primary agent 1: 5 completions -> eligible
    for ($i = 0; $i < 5; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }

    // Primary agent 2: 2 completions -> not eligible
    $agent2 = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => null,
    ]);
    for ($i = 0; $i < 2; $i++) {
        Application::create([
            'agent_id' => $agent2->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }

    // Sub-agent (parent_id is set) -> should be excluded from admin primary agent eligibility hub
    $subAgent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
        'parent_id' => $this->agent->id,
    ]);
    for ($i = 0; $i < 10; $i++) {
        Application::create([
            'agent_id' => $subAgent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => now(),
            'amount' => 100,
        ]);
    }

    // Call Admin Eligibility Hub Ajax endpoint
    $response = $this->actingAs($this->admin)->getJson(route('admin.gifts.eligibility.hub', [
        'gift_id' => $gift->id,
        'year' => now()->year,
        'month' => now()->month,
    ]));

    $response->assertSuccessful();
    $data = $response->json();

    // Check eligible total is 1
    expect($data['eligible_total'])->toBe(1);

    // Check rows count: only primary agents, subAgent excluded
    $totalPrimaryAgents = User::where('role', 'AGENT')->where('is_active', true)->whereNull('parent_id')->count();
    expect($data['recordsTotal'])->toBe($totalPrimaryAgents);

    // Check server side filtering for eligible=yes
    $resYes = $this->actingAs($this->admin)->getJson(route('admin.gifts.eligibility.hub', [
        'gift_id' => $gift->id,
        'year' => now()->year,
        'month' => now()->month,
        'columns' => [
            ['data' => 'name', 'searchable' => 'true', 'search' => ['value' => '']],
            ['data' => 'agent_code', 'searchable' => 'true', 'search' => ['value' => '']],
            ['data' => 'counts.'.$this->service->id, 'searchable' => 'false', 'search' => ['value' => '']],
            ['data' => 'eligible', 'searchable' => 'true', 'search' => ['value' => 'yes']],
        ],
    ]));
    $resYes->assertSuccessful();
    $dataYes = $resYes->json();
    expect($dataYes['recordsFiltered'])->toBe(1);
    expect($dataYes['data'][0]['name'])->toBe($this->agent->name);
    expect($dataYes['data'][0]['eligible'])->toBe('yes');

    // Check server side filtering for eligible=no
    $resNo = $this->actingAs($this->admin)->getJson(route('admin.gifts.eligibility.hub', [
        'gift_id' => $gift->id,
        'year' => now()->year,
        'month' => now()->month,
        'columns' => [
            ['data' => 'name', 'searchable' => 'true', 'search' => ['value' => '']],
            ['data' => 'agent_code', 'searchable' => 'true', 'search' => ['value' => '']],
            ['data' => 'counts.'.$this->service->id, 'searchable' => 'false', 'search' => ['value' => '']],
            ['data' => 'eligible', 'searchable' => 'true', 'search' => ['value' => 'no']],
        ],
    ]));
    $resNo->assertSuccessful();
    $dataNo = $resNo->json();
    expect($dataNo['recordsFiltered'])->toBe($totalPrimaryAgents - 1);
    expect(collect($dataNo['data'])->pluck('name'))->not->toContain($this->agent->name);
});

it('service giftConditions relationship executes cleanly without SQL errors', function () {
    $gift = Gift::create(['name' => 'Service Rel Gift', 'period_type' => 'monthly', 'is_active' => true]);
    $group = $gift->conditionGroups()->create(['sort_order' => 0]);
    $cond = $group->conditions()->create(['service_id' => $this->service->id, 'min_count' => 10]);

    expect($this->service->giftConditions)->toHaveCount(1);
    expect($this->service->giftConditions->first()->id)->toBe($cond->id);
});

it('front service page resolves gift milestones across all period types including session', function () {
    $sessionGift = Gift::create(['name' => 'Session Trophy', 'period_type' => 'session', 'is_active' => true]);
    $g = $sessionGift->conditionGroups()->create(['sort_order' => 0]);
    $g->conditions()->create(['service_id' => $this->service->id, 'min_count' => 12]);

    $currentSession = SessionResolver::current();

    for ($i = 0; $i < 12; $i++) {
        Application::create([
            'agent_id' => $this->agent->id,
            'service_id' => $this->service->id,
            'status' => ApplicationStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => $currentSession['from']->copy()->addDay(),
            'amount' => 100,
        ]);
    }

    $response = $this->actingAs($this->agent)->get(route('services.show', $this->service->slug));
    $response->assertSuccessful();
    $response->assertSee('Session Trophy');
    $response->assertSee('Current Session');
});

it('admin gift create validation failure redirects back with old groups intact in session', function () {
    $response = $this->actingAs($this->admin)->from(route('admin.gifts.create'))->post(route('admin.gifts.store'), [
        'name' => '', // missing name triggers validation error
        'period_type' => 'monthly',
        'is_active' => 1,
        'groups' => [
            [
                'conditions' => [
                    [
                        'service_id' => $this->service->id,
                        'min_count' => 7,
                    ],
                ],
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.gifts.create'));
    $response->assertSessionHasErrors(['name']);
    $response->assertSessionHas('_old_input.groups');

    $oldGroups = session('_old_input.groups');
    expect($oldGroups[0]['conditions'][0]['service_id'])->toEqual($this->service->id);
    expect($oldGroups[0]['conditions'][0]['min_count'])->toEqual(7);
});

<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Services\SidebarBadgeService;

beforeEach(function () {
    $this->agentA = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);
    $this->agentB = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);

    $this->itrService = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Filing', 'price' => 100, 'active' => true]
    );
});

it('renders sidebar tab badges in agent layout', function () {
    $this->actingAs($this->agentA)
        ->get(route('agent.applications.index', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertSee('sb-badge-cluster')
        ->assertSee('sb-badge');
});

it('scopes badge counts strictly to the logged-in agent', function () {
    // Agent A has 1 pending ITR application
    Application::factory()->create([
        'agent_id' => $this->agentA->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    // Agent B has 5 pending ITR applications
    Application::factory()->count(5)->create([
        'agent_id' => $this->agentB->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
    ]);

    $badgeService = app(SidebarBadgeService::class);

    $agentACounts = $badgeService->getTabCounts($this->agentA->id);
    $agentBCounts = $badgeService->getTabCounts($this->agentB->id);
    $systemCounts = $badgeService->getTabCounts(null);

    // Agent A should see 1 pending
    expect($agentACounts['itr-filing']['pending'])->toBe(1)
        ->and($agentACounts['itr-filing']['total_volume'])->toBe(1);

    // Agent B should see 5 pending
    expect($agentBCounts['itr-filing']['pending'])->toBe(5)
        ->and($agentBCounts['itr-filing']['total_volume'])->toBe(5);

    // System-wide (Admin) sees at least the combined total of both agents
    expect($systemCounts['itr-filing']['pending'])->toBeGreaterThanOrEqual(6)
        ->and($systemCounts['itr-filing']['total_volume'])->toBeGreaterThanOrEqual(6);
});

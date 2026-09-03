<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Services\SessionResolver;
use App\Services\SidebarBadgeService;

beforeEach(function () {
    $this->agentA = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);
    $this->agentB = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);

    $this->itrService = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Filing', 'price' => 100, 'active' => true]
    );
});

it('does not render badges when count is zero', function () {
    // Agent A has 0 applications
    $this->actingAs($this->agentA)
        ->get(route('agent.applications.index', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertDontSee('<span class="sb-badge-cluster"', false)
        ->assertDontSee('<span class="sb-badge ', false);
});

it('renders sidebar tab badges when count is at least 1', function () {
    $currentSession = SessionResolver::activeSessionLabel();

    Application::factory()->create([
        'agent_id' => $this->agentA->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => $currentSession,
    ]);

    $this->actingAs($this->agentA)
        ->get(route('agent.applications.index', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertSee('sb-badge-cluster')
        ->assertSee('sb-badge');
});

it('does not count applications from previous sessions in current session', function () {
    $currentSession = SessionResolver::activeSessionLabel();

    // Create an application in an old session (e.g. 2024-25 S1)
    Application::factory()->create([
        'agent_id' => $this->agentA->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => '2024-25 S1',
        'created_at' => '2024-10-01 10:00:00',
    ]);

    $badgeService = app(SidebarBadgeService::class);
    $countsInCurrentSession = $badgeService->getTabCounts($this->agentA->id, $currentSession);

    // Agent A has 0 in current session
    expect($countsInCurrentSession['itr-filing']['pending'])->toBe(0)
        ->and($countsInCurrentSession['itr-filing']['total_volume'])->toBe(0);

    // When querying the old session explicitly, the count is 1
    $countsInOldSession = $badgeService->getTabCounts($this->agentA->id, '2024-25 S1');
    expect($countsInOldSession['itr-filing']['pending'])->toBe(1);
});

it('scopes badge counts strictly to the logged-in agent', function () {
    $currentSession = SessionResolver::activeSessionLabel();

    // Agent A has 1 pending ITR application
    Application::factory()->create([
        'agent_id' => $this->agentA->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => $currentSession,
    ]);

    // Agent B has 5 pending ITR applications
    Application::factory()->count(5)->create([
        'agent_id' => $this->agentB->id,
        'service_id' => $this->itrService->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => $currentSession,
    ]);

    $badgeService = app(SidebarBadgeService::class);

    $agentACounts = $badgeService->getTabCounts($this->agentA->id, $currentSession);
    $agentBCounts = $badgeService->getTabCounts($this->agentB->id, $currentSession);
    $systemCounts = $badgeService->getTabCounts(null, $currentSession);

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

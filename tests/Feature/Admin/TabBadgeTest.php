<?php

use App\Models\User;
use App\Services\SidebarBadgeService;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
    $this->agent = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);
});

it('allows admin to view tab badges configuration page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.tab-badges.index'))
        ->assertSuccessful()
        ->assertSee('Notification Badges Configurator')
        ->assertSee('Live Sidebar Preview');
});

it('prevents non-admin from accessing tab badges configuration', function () {
    $this->actingAs($this->agent)
        ->get(route('admin.tab-badges.index'))
        ->assertForbidden();

    $subAdmin = User::factory()->create(['role' => 'SUB-ADMIN', 'is_active' => true]);
    $this->actingAs($subAdmin)
        ->get(route('admin.tab-badges.index'))
        ->assertForbidden();
});

it('allows admin to update tab badge configurations', function () {
    $payload = [
        'badges' => [
            [
                'label' => 'New Inflow',
                'metric' => 'today',
                'color' => 'green',
                'icon' => 'fas fa-sun',
                'tooltip' => 'Today: {count}',
                'is_active' => '1',
            ],
            [
                'label' => 'Submitted Work',
                'metric' => 'submitted',
                'color' => 'purple',
                'icon' => 'fas fa-paper-plane',
                'tooltip' => 'Submitted: {count}',
                'is_active' => '1',
            ],
        ],
    ];

    $this->actingAs($this->admin)
        ->post(route('admin.tab-badges.update'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $service = app(SidebarBadgeService::class);
    $configs = $service->getConfigs();

    expect($configs)->toHaveCount(2)
        ->and($configs[0]['label'])->toBe('New Inflow')
        ->and($configs[0]['metric'])->toBe('today')
        ->and($configs[0]['color'])->toBe('green')
        ->and($configs[1]['metric'])->toBe('submitted')
        ->and($configs[1]['color'])->toBe('purple');
});

it('allows admin to reset badge configurations to defaults', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.tab-badges.reset'))
        ->assertRedirect()
        ->assertSessionHas('success');

    $service = app(SidebarBadgeService::class);
    $configs = $service->getConfigs();

    expect($configs)->toBe($service->getDefaultConfigs());
});

it('renders sidebar tab badges in admin layout', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('sb-badge-cluster')
        ->assertSee('sb-badge')
        ->assertSee('background-color:')
        ->assertSee('Tab Badges');
});

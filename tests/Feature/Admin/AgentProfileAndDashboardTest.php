<?php

use App\Models\Application;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
    $this->marketer = User::factory()->create(['role' => 'MARKETER', 'name' => 'John Marketer', 'is_active' => true]);
    $this->agent = User::factory()->create([
        'role' => 'AGENT',
        'name' => 'Test Agent',
        'agent_code' => 'AGT-999999',
        'is_active' => true,
        'mobile_number' => '9876543210',
        'whatsapp_no' => '9876543210',
        'address' => 'Test Street 123',
        'marketer_id' => $this->marketer->id,
    ]);
});

it('allows admin to view agent 360 profile with complete stats', function () {
    $service = Service::factory()->create(['name' => 'ITR Filing Test']);

    Application::factory()->create([
        'agent_id' => $this->agent->id,
        'service_id' => $service->id,
        'status' => 'COMPLETED',
        'payment_status' => 'PAID',
        'amount' => 1000,
        'commission_amount' => 200,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.agents.show', $this->agent))
        ->assertSuccessful()
        ->assertSee('Agent 360° Profile')
        ->assertSee('Test Agent')
        ->assertSee('AGT-999999')
        ->assertSee('9876543210')
        ->assertSee('Marketer: John Marketer')
        ->assertSee('ITR Filing Test');
});

it('allows admin to toggle agent active and suspended status', function () {
    expect($this->agent->is_active)->toBeTrue();

    $this->actingAs($this->admin)
        ->patch(route('admin.agents.toggle-status', $this->agent))
        ->assertRedirect();

    $this->agent->refresh();
    expect($this->agent->is_active)->toBeFalse();

    $this->actingAs($this->admin)
        ->patch(route('admin.agents.toggle-status', $this->agent))
        ->assertRedirect();

    $this->agent->refresh();
    expect($this->agent->is_active)->toBeTrue();
});

it('allows admin to view dashboard with dynamic top agents section and query via ajax', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Top 10 Agents')
        ->assertSee('topAgentsLimitSelect');

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.dashboard', ['top_agents_limit' => '15']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'limit' => '15',
        ]);
});

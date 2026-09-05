<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN']);
    $this->application = Application::factory()->create(['status' => ApplicationStatus::SUBMITTED]);
});

it('allows admin to update application status to IN_PROGRESS', function () {
    actingAs($this->admin)
        ->patch(route('admin.applications.updateStatus', $this->application->id), [
            'status' => 'IN_PROGRESS',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->application->fresh()->status)->toBe(ApplicationStatus::IN_PROGRESS);
});

it('allows admin to update application status to COMPLETED', function () {
    actingAs($this->admin)
        ->patch(route('admin.applications.updateStatus', $this->application->id), [
            'status' => 'COMPLETED',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('renders status action form with hidden status field and alert messages on show page', function () {
    actingAs($this->admin)
        ->get(route('admin.applications.show', $this->application->id))
        ->assertSuccessful()
        ->assertSee('id="adminAppStatusField"', false)
        ->assertSee('no-loader');
});

it('fails validation if status is missing or invalid', function () {
    actingAs($this->admin)
        ->patch(route('admin.applications.updateStatus', $this->application->id), [])
        ->assertSessionHasErrors(['status']);

    expect($this->application->fresh()->status)->toBe(ApplicationStatus::SUBMITTED);
});

it('renders E-Filing and OTP Verification options in admin application status filter dropdown', function () {
    actingAs($this->admin)
        ->get(route('admin.applications.index'))
        ->assertSuccessful()
        ->assertSee('<option value="E_FILING">E-Filing</option>', false)
        ->assertSee('<option value="OTP_VERIFICATION">OTP Verification</option>', false);
});

it('correctly filters applications by E_FILING in admin data endpoint', function () {
    $eFilingApp = Application::factory()->create([
        'status' => ApplicationStatus::E_FILING,
    ]);

    $inProgressApp = Application::factory()->create([
        'status' => ApplicationStatus::IN_PROGRESS,
    ]);

    actingAs($this->admin)
        ->getJson(route('admin.applications.data', ['status' => 'E_FILING']))
        ->assertSuccessful()
        ->assertJsonFragment(['id' => $eFilingApp->id])
        ->assertJsonMissing(['id' => $inProgressApp->id]);
});

it('renders E-Filing and OTP Verification options in agent application status filter dropdown', function () {
    $agent = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);

    actingAs($agent)
        ->get(route('agent.applications.index'))
        ->assertSuccessful()
        ->assertSee('<option value="E_FILING">E-Filing</option>', false)
        ->assertSee('<option value="OTP_VERIFICATION">OTP Verification</option>', false);
});

it('correctly filters applications by E_FILING in agent data endpoint', function () {
    $agent = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);

    $eFilingApp = Application::factory()->create([
        'agent_id' => $agent->id,
        'status' => ApplicationStatus::E_FILING,
    ]);

    $inProgressApp = Application::factory()->create([
        'agent_id' => $agent->id,
        'status' => ApplicationStatus::IN_PROGRESS,
    ]);

    actingAs($agent)
        ->getJson(route('agent.applications.data', ['status' => 'E_FILING']))
        ->assertSuccessful()
        ->assertJsonFragment(['id' => $eFilingApp->id])
        ->assertJsonMissing(['id' => $inProgressApp->id]);
});

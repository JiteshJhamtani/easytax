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

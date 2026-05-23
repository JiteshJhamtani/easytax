<?php

use App\Models\Application;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN']);
    $this->application = Application::factory()->create();
});

it('allows admin to soft delete an application', function () {
    actingAs($this->admin)
        ->delete(route('admin.applications.destroy', $this->application->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    assertSoftDeleted($this->application);
});

it('allows admin to restore a soft deleted application', function () {
    $this->application->delete(); // Soft delete it first
    assertSoftDeleted($this->application);

    actingAs($this->admin)
        ->post(route('admin.applications.restore', $this->application->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    assertNotSoftDeleted($this->application);
});

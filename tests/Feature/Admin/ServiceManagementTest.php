<?php

use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN']);
    $this->agent = User::factory()->create(['role' => 'AGENT']);
});

/*
|--------------------------------------------------------------------------
| Index
|--------------------------------------------------------------------------
*/

test('admin can view the services index page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.services.index'))
        ->assertSuccessful();
});

test('non-admin cannot access services index', function () {
    $this->actingAs($this->agent)
        ->get(route('admin.services.index'))
        ->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| Create & Store
|--------------------------------------------------------------------------
*/

test('admin can view the create service form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.services.create'))
        ->assertSuccessful();
});

test('admin can create a service', function () {
    $data = [
        'name' => 'Test Service',
        'slug' => 'test-service',
        'description' => 'A test service description',
        'price' => 1500.00,
        'commission_type' => 'flat',
        'commission_value' => 200.00,
    ];

    $this->actingAs($this->admin)
        ->post(route('admin.services.store'), $data)
        ->assertRedirect();

    $this->assertDatabaseHas('services', [
        'name' => 'Test Service',
        'slug' => 'test-service',
    ]);
});

test('store validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.services.store'), [])
        ->assertSessionHasErrors(['name', 'slug', 'price', 'commission_type', 'commission_value']);
});

test('store validates slug uniqueness', function () {
    Service::factory()->create(['slug' => 'existing-slug']);

    $this->actingAs($this->admin)
        ->post(route('admin.services.store'), [
            'name' => 'Another Service',
            'slug' => 'existing-slug',
            'price' => 1000,
            'commission_type' => 'flat',
            'commission_value' => 100,
        ])
        ->assertSessionHasErrors(['slug']);
});

/*
|--------------------------------------------------------------------------
| Show
|--------------------------------------------------------------------------
*/

test('admin can view a service', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.services.show', $service))
        ->assertSuccessful()
        ->assertSee($service->name);
});

/*
|--------------------------------------------------------------------------
| Edit & Update
|--------------------------------------------------------------------------
*/

test('admin can view the edit service form', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.services.edit', $service))
        ->assertSuccessful();
});

test('admin can update a service', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('admin.services.update', $service), [
            'name' => 'Updated Service',
            'slug' => $service->slug,
            'price' => 2000.00,
            'commission_type' => 'percentage',
            'commission_value' => 10.00,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Updated Service',
    ]);
});

/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

test('admin can toggle service status', function () {
    $service = Service::factory()->create(['active' => true]);

    $this->actingAs($this->admin)
        ->patch(route('admin.services.toggle-status', $service))
        ->assertRedirect();

    expect($service->fresh()->active)->toBeFalse();
});

test('admin can reactivate a service', function () {
    $service = Service::factory()->inactive()->create();

    $this->actingAs($this->admin)
        ->patch(route('admin.services.toggle-status', $service))
        ->assertRedirect();

    expect($service->fresh()->active)->toBeTrue();
});

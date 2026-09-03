<?php

use App\Models\Application;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
});

it('renders application index with application type tabs and without tailwind play cdn', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.applications.index'));

    $response->assertSuccessful();
    $response->assertSee('applications-type-tabs');
    $response->assertSee('app-tab-btn');
    $response->assertSee('data-type="itr-filing"', false);
    $response->assertSee('data-type="gst-registration"', false);
    $response->assertSee('data-type="gst-return-filing"', false);
    $response->assertSee('data-type="incomplete"', false);

    // Verify Tailwind Play CDN is completely removed
    $response->assertDontSee('cdn.tailwindcss.com');
    // Verify artificial is-leaving delay script is removed
    $response->assertDontSee('is-leaving');
});

it('returns live stats with applications data ajax response', function () {
    $service = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Service', 'price' => 500, 'active' => true]
    );
    Application::factory()->count(3)->create([
        'service_id' => $service->id,
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    $response = $this->actingAs($this->admin)->getJson(route('admin.applications.data', ['type' => 'itr-filing']));

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data',
        'stats' => ['total', 'pending', 'completed', 'failed'],
    ]);
});

it('renders dashboard without tailwind play cdn or artificial animation delay', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertDontSee('cdn.tailwindcss.com');
    $response->assertDontSee('bodyFadeOut');
    $response->assertDontSee('is-leaving');
});

it('ensures brand logo is optimized under 100kb', function () {
    $logoPath = public_path('assets/images/logo11.png');

    expect(file_exists($logoPath))->toBeTrue();
    expect(filesize($logoPath))->toBeLessThan(100 * 1024);
});

it('ensures guest layout has no tailwind play cdn', function () {
    $response = $this->get(route('login'));

    $response->assertSuccessful();
    $response->assertDontSee('cdn.tailwindcss.com');
});

it('returns live stats with agent applications data ajax response', function () {
    $agent = User::factory()->create(['role' => 'AGENT', 'is_active' => true]);
    $service = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Service', 'price' => 500, 'active' => true]
    );
    Application::factory()->count(2)->create([
        'agent_id' => $agent->id,
        'service_id' => $service->id,
        'status' => 'SUBMITTED',
        'payment_status' => 'PAID',
    ]);

    $response = $this->actingAs($agent)->getJson(route('agent.applications.data', ['type' => 'itr-filing']));

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data',
        'stats' => ['total', 'pending', 'failed', 'monthly'],
    ]);
});

it('verifies critical performance indexes exist on applications and users tables', function () {
    $appIndexes = collect(DB::select('SHOW INDEX FROM applications'))->pluck('Key_name')->unique();
    $userIndexes = collect(DB::select('SHOW INDEX FROM users'))->pluck('Key_name')->unique();

    expect($appIndexes)->toContain('applications_status_index')
        ->toContain('applications_payment_status_index')
        ->toContain('applications_created_at_index')
        ->toContain('applications_deleted_at_index');

    expect($userIndexes)->toContain('users_role_is_active_index');
});

<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use App\Services\SessionResolver;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN']);
    $this->service = Service::firstOrCreate(
        ['slug' => 'itr-filing'],
        ['name' => 'ITR Filing', 'active' => true, 'price' => 100, 'commission_type' => 'flat', 'commission_value' => 10]
    );
});

it('scopes admin application index KPI stats to the active session', function () {
    $currentSession = SessionResolver::current()['label'];
    $olderSessionCount = Application::where('service_id', $this->service->id)
        ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
        ->where('payment_status', '!=', 'FAILED')
        ->inSession('2025-26 S2')
        ->count();

    // Create an application in an older session
    Application::factory()->create([
        'service_id' => $this->service->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => '2025-26 S2',
        'created_at' => '2026-08-01 10:00:00',
    ]);

    // When viewing for current session (which has 0 applications)
    actingAs($this->admin)
        ->withSession(['easytax_active_session' => $currentSession])
        ->get(route('admin.applications.index', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertViewHas('stats', fn ($stats) => (int) ($stats->total ?? 0) === 0);

    // When viewing for the older session
    actingAs($this->admin)
        ->withSession(['easytax_active_session' => '2025-26 S2'])
        ->get(route('admin.applications.index', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertViewHas('stats', fn ($stats) => (int) ($stats->total ?? 0) === ($olderSessionCount + 1));
});

it('scopes admin application datatable data to the active session', function () {
    $currentSession = SessionResolver::current()['label'];
    $olderDatatableCount = Application::where('service_id', $this->service->id)
        ->whereNotIn('status', ['DRAFT', 'CANCELLED', 'FAILED'])
        ->inSession('2025-26 S2')
        ->count();

    Application::factory()->create([
        'service_id' => $this->service->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => '2025-26 S2',
        'created_at' => '2026-08-01 10:00:00',
    ]);

    // Datatable for current session
    actingAs($this->admin)
        ->withSession(['easytax_active_session' => $currentSession])
        ->getJson(route('admin.applications.data', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertJsonPath('recordsTotal', 0);

    // Datatable for the older session
    actingAs($this->admin)
        ->withSession(['easytax_active_session' => '2025-26 S2'])
        ->getJson(route('admin.applications.data', ['type' => 'itr-filing']))
        ->assertSuccessful()
        ->assertJsonPath('recordsTotal', $olderDatatableCount + 1);
});

it('handles datatable sorting by joined service and agent columns with active session without ambiguous column error', function () {
    $subAdmin = User::factory()->create(['role' => 'SUB-ADMIN']);

    Application::factory()->create([
        'service_id' => $this->service->id,
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'session_label' => '2026-27 S1',
        'created_at' => '2026-09-15 10:00:00',
    ]);

    // DataTables request with order on column index 2 (service.name)
    $response = actingAs($subAdmin)
        ->withSession(['easytax_active_session' => '2026-27 S1'])
        ->getJson(route('admin.applications.data', [
            'type' => 'itr-filing',
            'columns' => [
                ['data' => 'id', 'name' => 'id', 'orderable' => 'true'],
                ['data' => 'agent', 'name' => 'agent.name', 'orderable' => 'true'],
                ['data' => 'service', 'name' => 'service.name', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'status', 'orderable' => 'true'],
            ],
            'order' => [
                ['column' => 2, 'dir' => 'asc'],
            ],
        ]));

    $response->assertSuccessful();
    expect($response->json('recordsTotal'))->toBeGreaterThanOrEqual(1);

    // Also test sorting by column index 1 (agent.name)
    $agentResponse = actingAs($subAdmin)
        ->withSession(['easytax_active_session' => '2026-27 S1'])
        ->getJson(route('admin.applications.data', [
            'type' => 'itr-filing',
            'columns' => [
                ['data' => 'id', 'name' => 'id', 'orderable' => 'true'],
                ['data' => 'agent', 'name' => 'agent.name', 'orderable' => 'true'],
                ['data' => 'service', 'name' => 'service.name', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'status', 'orderable' => 'true'],
            ],
            'order' => [
                ['column' => 1, 'dir' => 'asc'],
            ],
        ]));

    $agentResponse->assertSuccessful();
    expect($agentResponse->json('recordsTotal'))->toBeGreaterThanOrEqual(1);
});

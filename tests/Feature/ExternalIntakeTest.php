<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
    config(['services.easytax.external_secret' => 'test_intake_secret_key_12345']);
});

it('rejects external intake requests with missing or invalid secret', function () {
    $payload = [
        'idempotency_key' => 'test_key_unauth',
        'service_slug' => 'gst-registration',
        'service_name' => 'GST Registration',
        'customer' => [
            'name' => 'John Doe',
            'phone' => '9876543210',
            'email' => 'john@example.com',
        ],
        'payment' => [
            'reference' => 'PAY_UNAUTH_1',
            'amount' => 999.00,
        ],
    ];

    // No header
    $this->postJson('/api/v1/applications/external-intake', $payload)
        ->assertStatus(401)
        ->assertJson([
            'ok' => false,
        ]);

    // Invalid header
    $this->withHeaders(['X-EasyTax-Secret' => 'invalid_secret'])
        ->postJson('/api/v1/applications/external-intake', $payload)
        ->assertStatus(401)
        ->assertJson([
            'ok' => false,
        ]);
});

it('creates retail application and attaches documents successfully', function () {
    $secret = config('services.easytax.external_secret');
    $service = Service::firstOrCreate(
        ['slug' => 'gst-registration'],
        ['name' => 'GST Registration', 'price' => 999, 'active' => true]
    );

    $dummyPdfBase64 = base64_encode('%PDF-1.4 sample pdf content for testing');

    $payload = [
        'idempotency_key' => 'order_pay_test_001',
        'service_slug' => 'gst-registration',
        'service_name' => 'GST Registration',
        'customer' => [
            'name' => 'Rahul Verma',
            'phone' => '9876543210',
            'email' => 'rahul@example.com',
        ],
        'payment' => [
            'reference' => 'RZP_ORDER_9999',
            'amount' => 1499.00,
        ],
        'form_data' => [
            'pan_number' => 'ABCDE1234F',
            'trade_name' => 'Verma Enterprises',
        ],
        'documents' => [
            [
                'document_label' => 'PAN Card Copy',
                'file_name' => 'pan_card.pdf',
                'file_base64' => $dummyPdfBase64,
            ],
            [
                'document_label' => 'Electricity Bill',
                'file_name' => 'electricity.pdf',
                'file_base64' => 'data:application/pdf;base64,'.$dummyPdfBase64,
            ],
        ],
    ];

    $response = $this->withHeaders(['X-EasyTax-Secret' => $secret])
        ->postJson('/api/v1/applications/external-intake', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'ok' => true,
            'status' => 'created',
        ]);

    $appId = $response->json('application_id');
    expect($appId)->toBeInt();

    $app = Application::with('media')->find($appId);
    expect($app)->not->toBeNull()
        ->and($app->source)->toBe('WEBSITE_DIRECT')
        ->and($app->agent_id)->toBeNull()
        ->and($app->customer_name)->toBe('Rahul Verma')
        ->and($app->customer_phone)->toBe('9876543210')
        ->and($app->customer_email)->toBe('rahul@example.com')
        ->and($app->service_name_fallback)->toBe('GST Registration')
        ->and($app->amount)->toBe('1499.00')
        ->and($app->idempotency_key)->toBe('order_pay_test_001')
        ->and($app->isWebsiteDirect())->toBeTrue()
        ->and($app->customer_whatsapp_url)->toBe('https://wa.me/919876543210');

    $clientDocs = $app->getMedia('client_documents');
    expect($clientDocs->count())->toBe(2);
});

it('guarantees idempotency on duplicate submissions', function () {
    $secret = config('services.easytax.external_secret');
    $service = Service::firstOrCreate(
        ['slug' => 'gst-registration'],
        ['name' => 'GST Registration', 'price' => 999, 'active' => true]
    );

    $payload = [
        'idempotency_key' => 'idempotency_retry_test_999',
        'service_slug' => 'gst-registration',
        'service_name' => 'GST Registration',
        'customer' => [
            'name' => 'Anita Roy',
            'phone' => '9123456780',
            'email' => 'anita@example.com',
        ],
        'payment' => [
            'reference' => 'PAY_RETRY_1',
            'amount' => 899.00,
        ],
    ];

    // First attempt creates
    $firstResponse = $this->withHeaders(['X-EasyTax-Secret' => $secret])
        ->postJson('/api/v1/applications/external-intake', $payload);

    $firstResponse->assertStatus(201)
        ->assertJson(['status' => 'created']);
    $firstAppId = $firstResponse->json('application_id');

    // Second attempt returns already_exists with same application_id
    $secondResponse = $this->withHeaders(['X-EasyTax-Secret' => $secret])
        ->postJson('/api/v1/applications/external-intake', $payload);

    $secondResponse->assertStatus(200)
        ->assertJson([
            'ok' => true,
            'status' => 'already_exists',
            'application_id' => $firstAppId,
        ]);

    expect(Application::where('idempotency_key', 'idempotency_retry_test_999')->count())->toBe(1);
});

it('falls back to default service for unmapped retail services while preserving fallback name', function () {
    $secret = config('services.easytax.external_secret');

    $payload = [
        'idempotency_key' => 'unmapped_service_order_777',
        'service_slug' => 'ngo-audit-compliance-super-rare',
        'service_name' => 'NGO Comprehensive Annual Audit & Compliance',
        'customer' => [
            'name' => 'Seva Foundation',
            'phone' => '9988776655',
            'email' => 'info@sevafoundation.org',
        ],
        'payment' => [
            'reference' => 'NGO_PAY_777',
            'amount' => 4999.00,
        ],
    ];

    $response = $this->withHeaders(['X-EasyTax-Secret' => $secret])
        ->postJson('/api/v1/applications/external-intake', $payload);

    $response->assertStatus(201);
    $appId = $response->json('application_id');

    $app = Application::find($appId);
    expect($app)->not->toBeNull()
        ->and($app->service_name_fallback)->toBe('NGO Comprehensive Annual Audit & Compliance')
        ->and($app->service_id)->not->toBeNull();
});

it('triggers outbound webhook to Drupal on completion of website direct order', function () {
    Http::fake();

    $app = Application::create([
        'source' => 'WEBSITE_DIRECT',
        'idempotency_key' => 'webhook_test_key_888',
        'service_id' => Service::first()->id,
        'service_name_fallback' => 'Custom Service',
        'customer_name' => 'Test Customer',
        'customer_phone' => '9876500000',
        'customer_email' => 'customer@test.com',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'amount' => 500.00,
        'commission_amount' => 0.00,
        'parent_margin' => 0.00,
    ]);

    $admin = User::factory()->create(['role' => 'ADMIN']);

    $this->actingAs($admin)
        ->patch(route('admin.applications.updateStatus', $app), [
            'status' => 'COMPLETED',
        ])
        ->assertRedirect();

    Http::assertSent(function ($request) use ($app) {
        return $request->url() === config('services.easytax.drupal_webhook_url')
            && $request['idempotency_key'] === $app->idempotency_key
            && $request['b2b_app_id'] === $app->id
            && array_key_exists('deliverable_url', $request->data())
            && $request['status'] === 'COMPLETED';
    });
});

it('lists website direct orders when filtering by tab=website', function () {
    $admin = User::factory()->create(['role' => 'ADMIN']);

    $retailApp = Application::create([
        'source' => 'WEBSITE_DIRECT',
        'idempotency_key' => 'retail_filter_tab_111',
        'service_id' => Service::first()->id,
        'service_name_fallback' => 'Custom Retail Service',
        'customer_name' => 'Retail Filter User',
        'customer_phone' => '9876543210',
        'customer_email' => 'filter@test.com',
        'status' => ApplicationStatus::SUBMITTED,
        'payment_status' => PaymentStatus::PAID,
        'amount' => 1250.00,
        'commission_amount' => 0.00,
        'parent_margin' => 0.00,
    ]);

    $response = $this->actingAs($admin)
        ->getJson('/admin/applications/data?tab=website');

    $response->assertStatus(200);
    $data = $response->json('data');

    expect(collect($data)->pluck('id')->all())->toContain($retailApp->id);
});

it('triggers outbound webhook to Drupal on completion via team dashboard', function () {
    Http::fake();

    $operator = User::factory()->create(['role' => 'TEAM']);

    $app = Application::create([
        'source' => 'WEBSITE_DIRECT',
        'idempotency_key' => 'operator_webhook_test_222',
        'service_id' => Service::first()->id,
        'service_name_fallback' => 'Filing Service',
        'customer_name' => 'Operator Retail Customer',
        'customer_phone' => '9876512345',
        'customer_email' => 'operator_client@test.com',
        'assigned_to' => $operator->id,
        'status' => ApplicationStatus::IN_PROGRESS,
        'payment_status' => PaymentStatus::PAID,
        'amount' => 750.00,
        'commission_amount' => 0.00,
        'parent_margin' => 0.00,
    ]);

    $this->actingAs($operator)
        ->post(route('team.applications.status', $app->id), [
            'status' => 'COMPLETED',
        ])
        ->assertRedirect();

    Http::assertSent(function ($request) use ($app) {
        return $request->url() === config('services.easytax.drupal_webhook_url')
            && $request['idempotency_key'] === $app->idempotency_key
            && $request['b2b_app_id'] === $app->id
            && array_key_exists('deliverable_url', $request->data())
            && $request['status'] === 'COMPLETED';
    });
});

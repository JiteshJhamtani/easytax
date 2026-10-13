<?php

use App\Enums\PaymentStatus;
use App\Models\AgentMarginLog;
use App\Models\Application;
use App\Models\PaymentLog;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;
use function Pest\Laravel\postJson;

// uses(RefreshDatabase::class);

it('cannot process the same razorpay webhook twice', function () {
    // 1. Arrange: Create a pending application
    $agent = User::factory()->create(['role' => 'AGENT']);
    $application = Application::factory()->create([
        'agent_id' => $agent->id,
        'payment_status' => PaymentStatus::PENDING,
        'payment_reference' => 'order_xyz123',
        'amount' => 1000,
        'commission_amount' => 100,
    ]);

    // Construct Razorpay payload
    $payload = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => 'pay_abc456',
                    'order_id' => 'order_xyz123',
                    'amount' => 90000, // (1000 - 100) * 100
                ],
            ],
        ],
    ];
    $jsonPayload = json_encode($payload);

    // Generate valid signature using Razorpay config secret
    $secret = config('razorpay.webhook_secret') ?: 'test_webhook_audit_secret_123';
    config(['razorpay.webhook_secret' => $secret]);
    $signature = hash_hmac('sha256', $jsonPayload, $secret);

    // 2. Act: Send the webhook TWICE concurrently (simulated)
    $response1 = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $signature,
    ]);

    $response2 = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $signature,
    ]);

    // 3. Assert: Both return 200, but only ONE payment log is created
    // and payment_status is PAID
    $response1->assertOk();
    $response2->assertOk();

    expect($application->fresh()->payment_status)->toBe(PaymentStatus::PAID);

    // Assert only one webhook_captured event exists for this payment
    $logsCount = PaymentLog::where('application_id', $application->id)
        ->where('event', 'webhook_captured')
        ->count();

    expect($logsCount)->toBe(1);
});

it('rejects razorpay webhook when secret is empty and blocks forged empty-key signatures', function () {
    // 1. Secret is null or empty
    config(['razorpay.webhook_secret' => null]);

    $payload = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => 'pay_hacker1',
                    'order_id' => 'order_fake99',
                    'amount' => 50000,
                ],
            ],
        ],
    ];
    $jsonPayload = json_encode($payload);

    // Attacker crafts signature using empty string HMAC
    $forgedSignature = hash_hmac('sha256', $jsonPayload, '');

    $response = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $forgedSignature,
    ]);

    // Must be rejected with 403 Forbidden!
    $response->assertStatus(403);
});

it('voids accrued margins and transitions application to REFUNDED on payment.refunded webhook', function () {
    $parent = User::factory()->create(['role' => 'AGENT', 'parent_id' => null]);
    $subAgent = User::factory()->create(['role' => 'AGENT', 'parent_id' => $parent->id]);

    $application = Application::factory()->create([
        'agent_id' => $parent->id,
        'sub_agent_id' => $subAgent->id,
        'payment_status' => PaymentStatus::PAID,
        'payment_reference' => 'order_refund_test_1',
        'parent_margin' => 300.00,
        'parent_margin_status' => 'ACCRUED',
        'amount' => 1500,
        'commission_amount' => 100,
    ]);

    $marginLog = AgentMarginLog::create([
        'parent_agent_id' => $parent->id,
        'sub_agent_id' => $subAgent->id,
        'application_id' => $application->id,
        'sub_agent_paid' => 1400.00,
        'company_retained' => 1100.00,
        'margin_amount' => 300.00,
        'tier_level' => 1,
        'status' => 'ACCRUED',
    ]);

    $secret = 'test_refund_secret_abc';
    config(['razorpay.webhook_secret' => $secret]);

    $payload = [
        'event' => 'payment.refunded',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => 'pay_refund_1',
                    'order_id' => 'order_refund_test_1',
                    'amount' => 140000,
                    'amount_refunded' => 140000,
                    'refund_status' => 'full',
                    'status' => 'refunded',
                ],
            ],
            'refund' => [
                'entity' => [
                    'id' => 'rfnd_123',
                    'payment_id' => 'pay_refund_1',
                    'amount' => 140000,
                    'status' => 'processed',
                ],
            ],
        ],
    ];
    $jsonPayload = json_encode($payload);
    $signature = hash_hmac('sha256', $jsonPayload, $secret);

    $response = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $signature,
    ]);

    $response->assertOk();

    $application->refresh();
    expect($application->payment_status)->toBe(PaymentStatus::REFUNDED)
        ->and($application->parent_margin_status)->toBe('CANCELLED');

    $marginLog->refresh();
    expect($marginLog->status)->toBe('CANCELLED')
        ->and($marginLog->notes)->toContain('refunded via Razorpay Webhook');
});

it('rejects confirmation of an already refunded payment on payment.success', function () {
    $agent = User::factory()->create(['role' => 'AGENT']);
    $application = Application::factory()->create([
        'agent_id' => $agent->id,
        'payment_status' => PaymentStatus::PENDING,
        'payment_reference' => 'order_already_refunded',
        'amount' => 1000,
        'commission_amount' => 100,
        'expected_amount_paise' => 90000,
    ]);

    $this->mock(RazorpayService::class, function ($mock) use ($application) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_refunded_earlier',
            'order_id' => $application->payment_reference,
            'amount' => 90000,
            'status' => 'refunded',
            'refund_status' => 'full',
        ]);
    });

    actingAs($agent)
        ->post(route('payment.success'), [
            'razorpay_payment_id' => 'pay_refunded_earlier',
            'razorpay_order_id' => $application->payment_reference,
            'razorpay_signature' => 'valid_sig',
        ])
        ->assertSessionHas('error');

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PENDING);
});

it('does not resurrect a refunded application during retryPayment', function () {
    $agent = User::factory()->create(['role' => 'AGENT']);
    $application = Application::factory()->create([
        'agent_id' => $agent->id,
        'payment_status' => PaymentStatus::REFUNDED,
        'payment_reference' => 'order_old_refunded_ref',
        'amount' => 1000,
        'commission_amount' => 100,
    ]);

    $this->mock(RazorpayService::class, function ($mock) {
        // Even if Razorpay order reports paid (stale state on refunded order)
        // retryPayment must NOT mark it paid again
        $mock->shouldNotReceive('markApplicationPaid');
        $mock->shouldReceive('createOrder')->once()->andReturn([
            'success' => true,
            'order_id' => 'order_fresh_retry_new',
            'amount' => 90000,
            'currency' => 'INR',
            'status' => 'created',
        ]);
    });

    actingAs($agent)
        ->post(route('applications.retryPayment', $application))
        ->assertSessionHas('razorpay_order');

    $application->refresh();
    expect($application->payment_status)->toBe(PaymentStatus::PENDING)
        ->and($application->payment_reference)->toBe('order_fresh_retry_new');
});

it('cannot escalate role via profile update mass assignment', function () {
    // 1. Arrange: Create a standard agent
    $agent = User::factory()->create([
        'role' => 'AGENT',
        'is_active' => true,
    ]);

    // 2. Act: Agent tries to update profile and sneak in a role change
    actingAs($agent);

    $response = patch(route('profile.update'), [
        'name' => 'Hacker Agent',
        'email' => $agent->email,
        'role' => 'ADMIN', // The malicious payload
        'is_active' => true,
    ]);

    // 3. Assert: Profile updated but role remains AGENT
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('profile.edit'));

    $agent->refresh();

    expect($agent->name)->toBe('Hacker Agent')
        ->and($agent->role->value ?? $agent->role)->toBe('AGENT');
});

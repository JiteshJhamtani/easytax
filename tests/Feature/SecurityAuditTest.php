<?php

use App\Models\User;
use App\Models\Application;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patch;

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
                ]
            ]
        ]
    ];
    $jsonPayload = json_encode($payload);

    // Generate valid signature using Razorpay config secret
    $secret = config('razorpay.webhook_secret');
    $signature = hash_hmac('sha256', $jsonPayload, $secret);

    // 2. Act: Send the webhook TWICE concurrently (simulated)
    $response1 = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $signature
    ]);

    $response2 = postJson(route('payment.webhook'), $payload, [
        'X-Razorpay-Signature' => $signature
    ]);

    // 3. Assert: Both return 200, but only ONE payment log is created 
    // and payment_status is PAID
    $response1->assertOk();
    $response2->assertOk();

    expect($application->fresh()->payment_status)->toBe(PaymentStatus::PAID);

    // Assert only one webhook_captured event exists for this payment
    $this->assertDatabaseCount('payment_logs', 1);
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

<?php

use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\PaymentLog;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Notification;

function pendingApplication(array $attributes = []): Application
{
    return Application::factory()->create(array_merge([
        'payment_status' => PaymentStatus::PENDING,
        'payment_reference' => 'order_'.uniqid(),
        'amount' => 500,
        'commission_amount' => 100,
        'expected_amount_paise' => 40000,
    ], $attributes));
}

test('amount is verified against the snapshot, not the live application fields', function () {
    Notification::fake();
    // Snapshot deliberately differs from amount - commission (30000 vs 40000):
    // simulates an admin editing the application while payment was in flight.
    $application = pendingApplication(['expected_amount_paise' => 30000]);

    $this->mock(RazorpayService::class, function ($mock) use ($application) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_snap',
            'order_id' => $application->payment_reference,
            'amount' => 30000,
            'status' => 'captured',
        ]);
    });

    $this->actingAs($application->agent)->post(route('payment.success'), [
        'razorpay_payment_id' => 'pay_snap',
        'razorpay_order_id' => $application->payment_reference,
        'razorpay_signature' => 'sig',
    ]);

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PAID);
});

test('modal dismiss with an authorized payment captures it instead of marking failed', function () {
    Notification::fake();
    $application = pendingApplication();

    $this->mock(RazorpayService::class, function ($mock) {
        $mock->shouldReceive('fetchOrderStatus')->once()->andReturn('attempted');
        $mock->shouldReceive('fetchOrderPayments')->once()->andReturn([
            ['id' => 'pay_auth', 'status' => 'authorized', 'amount' => 40000],
        ]);
        $mock->shouldReceive('capturePayment')->once()->with('pay_auth', 40000)->andReturn(true);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.failure'), ['razorpay_order_id' => $application->payment_reference])
        ->assertSessionHas('success');

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PAID);
});

test('modal dismiss with a payment still in flight leaves the application pending', function () {
    $application = pendingApplication();

    $this->mock(RazorpayService::class, function ($mock) {
        $mock->shouldReceive('fetchOrderStatus')->once()->andReturn('attempted');
        $mock->shouldReceive('fetchOrderPayments')->once()->andReturn([
            ['id' => 'pay_pending', 'status' => 'created', 'amount' => 40000],
        ]);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.failure'), ['razorpay_order_id' => $application->payment_reference])
        ->assertSessionHas('error');

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PENDING);
});

test('modal dismiss with no payment marks the application failed', function () {
    $application = pendingApplication();

    $this->mock(RazorpayService::class, function ($mock) {
        $mock->shouldReceive('fetchOrderStatus')->once()->andReturn('created');
        $mock->shouldReceive('fetchOrderPayments')->once()->andReturn([]);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.failure'), ['razorpay_order_id' => $application->payment_reference]);

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::FAILED);
});

test('retry recovers a payment that succeeded on the previous order instead of charging again', function () {
    Notification::fake();
    $application = pendingApplication();

    $this->mock(RazorpayService::class, function ($mock) use ($application) {
        $mock->shouldReceive('fetchOrderStatus')
            ->once()
            ->with($application->payment_reference)
            ->andReturn('paid');
        $mock->shouldNotReceive('createOrder');
    });

    $originalReference = $application->payment_reference;

    $this->actingAs($application->agent)
        ->post(route('applications.retryPayment', $application))
        ->assertSessionHas('success');

    $application->refresh();
    expect($application->payment_status)->toBe(PaymentStatus::PAID)
        ->and($application->payment_reference)->toBe($originalReference);
});

test('webhook resolves the application via payment logs after a retry replaced the order reference', function () {
    Notification::fake();
    $application = pendingApplication(['payment_reference' => 'order_new_ref']);

    PaymentLog::create([
        'application_id' => $application->id,
        'transaction_id' => 'APP_'.$application->id.'_1',
        'event' => 'order_created',
        'status' => 'created',
        'response' => ['success' => true, 'order_id' => 'order_old_ref', 'amount' => 40000],
    ]);

    $this->mock(RazorpayService::class, function ($mock) {
        $mock->shouldReceive('verifyWebhook')->once()->andReturn(true);
    });

    $this->postJson(route('payment.webhook'), [
        'event' => 'payment.captured',
        'payload' => ['payment' => ['entity' => [
            'id' => 'pay_late',
            'order_id' => 'order_old_ref',
            'amount' => 40000,
        ]]],
    ], ['X-Razorpay-Signature' => 'sig'])->assertOk();

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PAID);
});

test('webhook without a signature header is rejected with 403', function () {
    $this->postJson(route('payment.webhook'), ['event' => 'payment.captured'])
        ->assertStatus(403);
});

test('checkStatus does not leak payment logs of other agents', function () {
    $application = pendingApplication();

    PaymentLog::create([
        'application_id' => $application->id,
        'transaction_id' => 'TXN_PRIVATE',
        'event' => 'payment_success',
        'status' => 'captured',
    ]);

    $otherAgent = User::factory()->create(['role' => 'AGENT']);

    $this->actingAs($otherAgent)
        ->get(route('payment.status', 'TXN_PRIVATE'))
        ->assertJson(['status' => 'PENDING']);

    $this->actingAs($application->agent)
        ->get(route('payment.status', 'TXN_PRIVATE'))
        ->assertJson(['status' => 'SUCCESS']);
});

<?php

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\PaymentLog;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

/**
 * @return array{application: Application, expectedPaise: int}
 */
function pendingApplicationForPayment(): array
{
    $application = Application::factory()->create([
        'payment_status' => PaymentStatus::PENDING,
        'payment_reference' => 'order_'.uniqid(),
        'amount' => 500,
        'commission_amount' => 100,
    ]);

    return [
        'application' => $application,
        'expectedPaise' => 40000,
    ];
}

test('captured payment marks the application as paid', function () {
    Notification::fake();
    ['application' => $application, 'expectedPaise' => $expectedPaise] = pendingApplicationForPayment();

    $this->mock(RazorpayService::class, function ($mock) use ($application, $expectedPaise) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_captured1',
            'order_id' => $application->payment_reference,
            'amount' => $expectedPaise,
            'status' => 'captured',
        ]);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.success'), [
            'razorpay_payment_id' => 'pay_captured1',
            'razorpay_order_id' => $application->payment_reference,
            'razorpay_signature' => 'sig',
        ])
        ->assertRedirect(route('payment.result', ['txn' => $application->payment_reference]));

    $application->refresh();
    expect($application->payment_status)->toBe(PaymentStatus::PAID)
        ->and($application->status)->toBe(ApplicationStatus::SUBMITTED);
});

test('authorized payment is captured server-side before marking paid', function () {
    Notification::fake();
    ['application' => $application, 'expectedPaise' => $expectedPaise] = pendingApplicationForPayment();

    $this->mock(RazorpayService::class, function ($mock) use ($application, $expectedPaise) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_auth1',
            'order_id' => $application->payment_reference,
            'amount' => $expectedPaise,
            'status' => 'authorized',
        ]);
        $mock->shouldReceive('capturePayment')
            ->once()
            ->with('pay_auth1', $expectedPaise)
            ->andReturn(true);
    });

    $this->actingAs($application->agent)->post(route('payment.success'), [
        'razorpay_payment_id' => 'pay_auth1',
        'razorpay_order_id' => $application->payment_reference,
        'razorpay_signature' => 'sig',
    ]);

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PAID);
});

test('authorized payment that fails to capture is not marked paid', function () {
    Notification::fake();
    ['application' => $application, 'expectedPaise' => $expectedPaise] = pendingApplicationForPayment();

    $this->mock(RazorpayService::class, function ($mock) use ($application, $expectedPaise) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_auth_fail',
            'order_id' => $application->payment_reference,
            'amount' => $expectedPaise,
            'status' => 'authorized',
        ]);
        $mock->shouldReceive('capturePayment')->once()->andReturn(false);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.success'), [
            'razorpay_payment_id' => 'pay_auth_fail',
            'razorpay_order_id' => $application->payment_reference,
            'razorpay_signature' => 'sig',
        ])
        ->assertSessionHas('error');

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PENDING)
        ->and(PaymentLog::where('application_id', $application->id)
            ->where('event', 'payment_capture_failed')->exists())->toBeTrue();
});

test('payment with wrong amount is not marked paid', function () {
    ['application' => $application] = pendingApplicationForPayment();

    $this->mock(RazorpayService::class, function ($mock) use ($application) {
        $mock->shouldReceive('verifySignature')->once()->andReturn(true);
        $mock->shouldReceive('fetchPayment')->once()->andReturn([
            'id' => 'pay_tampered',
            'order_id' => $application->payment_reference,
            'amount' => 100,
            'status' => 'captured',
        ]);
    });

    $this->actingAs($application->agent)
        ->post(route('payment.success'), [
            'razorpay_payment_id' => 'pay_tampered',
            'razorpay_order_id' => $application->payment_reference,
            'razorpay_signature' => 'sig',
        ])
        ->assertSessionHas('error');

    expect($application->refresh()->payment_status)->toBe(PaymentStatus::PENDING);
});

test('broken agent retry route has been removed', function () {
    expect(Route::has('applications.retry'))->toBeFalse();
});

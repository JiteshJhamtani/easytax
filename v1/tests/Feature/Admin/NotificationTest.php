<?php

use App\Models\User;
use App\Models\Service;
use App\Models\Application;
use App\Enums\NotificationPreference;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Notifications\ApplicationSubmittedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
    $this->service = Service::factory()->create();
    $this->application = Application::factory()->create([
        'service_id' => $this->service->id,
        'payment_status' => PaymentStatus::PENDING,
        'status' => ApplicationStatus::DRAFT,
        'payment_reference' => 'TXN123'
    ]);

    // Setup mocked credentials
    config(['phonepe.webhook_username' => 'testuser']);
    config(['phonepe.webhook_password' => 'testpass']);
    config(['phonepe.salt_key' => 'testsalt']);
    config(['phonepe.salt_index' => '1']);

    $this->payloadString = json_encode([
        'data' => [
            'merchantTransactionId' => 'TXN123',
            'state' => 'COMPLETED'
        ]
    ]);

    $this->checksum = hash('sha256', $this->payloadString . 'testsalt') . '###1';

    $this->headers = [
        'PHP_AUTH_USER' => 'testuser',
        'PHP_AUTH_PW' => 'testpass',
        'HTTP_X_VERIFY' => $this->checksum,
        'CONTENT_TYPE' => 'application/json'
    ];
});

it('dispatches mail and database notifications when admin preference is ON', function () {
    Notification::fake();
    
    $this->admin->update(['notification_preference' => NotificationPreference::ON]);

    $response = $this->call('POST', route('payment.webhook'), [], [], [], $this->headers, $this->payloadString);

    $response->assertSuccessful();

    Notification::assertSentTo(
        [$this->admin], ApplicationSubmittedNotification::class,
        function ($notification, $channels) {
            return in_array('mail', $channels) && in_array('database', $channels);
        }
    );
});

it('dispatches only database notification when admin preference is SILENT', function () {
    Notification::fake();
    
    $this->admin->update(['notification_preference' => NotificationPreference::SILENT]);

    $response = $this->call('POST', route('payment.webhook'), [], [], [], $this->headers, $this->payloadString);

    $response->assertSuccessful();

    Notification::assertSentTo(
        [$this->admin], ApplicationSubmittedNotification::class,
        function ($notification, $channels) {
            return in_array('database', $channels) && !in_array('mail', $channels);
        }
    );
});

it('does not dispatch any notifications when admin preference is OFF', function () {
    Notification::fake();
    
    $this->admin->update(['notification_preference' => NotificationPreference::OFF]);

    $response = $this->call('POST', route('payment.webhook'), [], [], [], $this->headers, $this->payloadString);

    $response->assertSuccessful();

    Notification::assertNotSentTo(
        [$this->admin], ApplicationSubmittedNotification::class
    );
});

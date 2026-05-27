<?php

use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('securely generates a bank lead and updates the application', function () {
    $this->withoutMiddleware();
    // 1. ARRANGE: Create a fake agent and an application belonging to them
    $agent = User::factory()->create();
    $application = Application::factory()->create([
        'agent_id' => $agent->id,
        'bank_lead_reference' => null,
    ]);

    // Inject fake configuration credentials for the test environment
    config()->set('services.bank.api_url', 'https://fake-bank-api.com/v1/leads');
    config()->set('services.bank.client_id', 'TEST_CLIENT_ID');
    config()->set('services.bank.secret_key', 'TEST_SECRET_KEY');
    config()->set('services.bank.partner_code', 'TEST_PARTNER');

    // 2. MOCK: Intercept the API call and fake a successful response from the Bank
    $fakeTrackingId = 'BANK-TRK-'.rand(1000, 9999);

    Http::fake([
        'https://fake-bank-api.com/v1/leads' => Http::response([
            'status' => 'success',
            'tracking_id' => $fakeTrackingId,
        ], 200),
    ]);

    // 3. ACT: The Agent submits the form on the frontend
    $response = $this->actingAs($agent)->post(route('agent.bank-leads.store'), [
        'application_id' => $application->id,
        'bank_name' => 'Fake HDFC Bank',
    ]);

    // 4. ASSERT: Verify the UI redirected with a success message
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // ASSERT: Verify the database was successfully updated with the bank's tracking ID
    expect($application->fresh()->bank_lead_reference)->toBe($fakeTrackingId);

    // ASSERT: Verify exactly what was sent to the bank (Security Headers & Payload)
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        $payloadData = json_decode(base64_decode($request['payload']), true);

        return $request->hasHeader('X-Client-Id', 'TEST_CLIENT_ID')
            && $request->hasHeader('X-Checksum') // Proves checksum was generated
            && $payloadData['bank_name'] === 'Fake HDFC Bank'
            && $payloadData['partner_code'] === 'TEST_PARTNER';
    });
});

it('blocks access if a rogue agent tries to spoof the application id', function () {
    $this->withoutMiddleware();
    $rogueAgent = User::factory()->create();
    $trueOwner = User::factory()->create();
    $application = Application::factory()->create([
        'agent_id' => $trueOwner->id, // Owned by someone else
    ]);

    $response = $this->actingAs($rogueAgent)->post(route('agent.bank-leads.store'), [
        'application_id' => $application->id,
        'bank_name' => 'Fake HDFC Bank',
    ]);

    // Assert our Security Gate throws a 404 ModelNotFoundException to protect data
    $response->assertNotFound();
});

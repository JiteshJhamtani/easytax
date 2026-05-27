<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BankIntegrationService
{
    /**
     * Transform data, generate a security checksum, and dispatch the API request.
     *
     * @throws \Exception
     */
    public function generateLead(Application $application, string $bankName): ?string
    {
        // STEP 2: Extract strictly allowed parameters to prevent over-sharing PII
        $payloadData = [
            'business_name' => $application->business_name,
            'entity_type' => $application->entity_type,
            'date_of_incorporation' => $application->date_of_incorporation,
            'pan_number' => $application->pan_number,
            'gst_number' => $application->gst_number,
            'mobile_number' => $application->mobile_number,
            'email_address' => $application->email_address,
            'pincode' => $application->pincode,
            'partner_code' => config('services.bank.partner_code'),
            'lead_reference_id' => (string) Str::uuid(),
            'bank_name' => $bankName,
        ];

        // Format into a strict JSON string
        $jsonPayload = json_encode($payloadData);
        if ($jsonPayload === false) {
            throw new \Exception('Failed to encode payload to JSON.');
        }

        // Run the JSON payload through base64_encode
        $base64Payload = base64_encode($jsonPayload);

        // SECURITY GATE: Generate HMAC SHA256 Checksum for Data Integrity
        $secretKey = config('services.bank.secret_key');
        if (empty($secretKey)) {
            throw new \Exception('Bank API Secret Key is not configured.');
        }

        $checksum = hash_hmac('sha256', $base64Payload, $secretKey);

        // STEP 3: The API Dispatch
        $apiUrl = config('services.bank.api_url');
        $clientId = config('services.bank.client_id');

        // Send ONLY the Base64 encoded payload in the body.
        // We use the Http facade to securely POST to the bank's endpoint.
        $response = Http::withHeaders([
            'X-Client-Id' => $clientId,
            'X-Checksum' => $checksum,
            'Accept' => 'application/json',
        ])->post($apiUrl, [
            'payload' => $base64Payload,
        ]);

        if ($response->successful()) {
            // Retrieve the tracking ID from the bank's successful JSON response
            return $response->json('tracking_id');
        }

        // Log the failure for debugging without exposing PII
        Log::error('Bank API Request Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'lead_reference_id' => $payloadData['lead_reference_id'],
        ]);

        return null;
    }
}

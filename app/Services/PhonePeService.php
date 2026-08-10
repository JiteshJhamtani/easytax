<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhonePeService
{
    protected string $merchantId;

    protected string $saltKey;

    protected string $saltIndex;

    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('phonepe.merchant_id');
        $this->saltKey = config('phonepe.salt_key');
        $this->saltIndex = config('phonepe.salt_index');
        $this->baseUrl = config('phonepe.base_url');
    }

    public function createPayment(
        string $transactionId,
        int $amount,
        string $userId,
        string $redirectUrl,
        string $callbackUrl
    ) {
        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $transactionId,
            'merchantUserId' => $userId,
            'amount' => $amount,
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $callbackUrl,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $encoded = base64_encode(json_encode($payload));
        $endpoint = '/pg/v1/pay';
        $checksum = hash('sha256', $encoded.$endpoint.$this->saltKey).'###'.$this->saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->baseUrl.$endpoint, [
            'request' => $encoded,
        ]);

        return $response->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Poll payment status — used in place of webhooks
    |--------------------------------------------------------------------------
    */
    public function checkStatus(string $transactionId): array
    {
        $endpoint = "/pg/v1/status/{$this->merchantId}/{$transactionId}";
        $checksum = hash('sha256', $endpoint.$this->saltKey).'###'.$this->saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
            'X-MERCHANT-ID' => $this->merchantId,
        ])->get($this->baseUrl.$endpoint);

        return $response->json() ?? [];
    }

    public function verifyWebhook(string $payload, ?string $receivedChecksum): bool
    {
        if (! $receivedChecksum) {
            return false;
        }

        $expectedChecksum = hash('sha256', $payload.$this->saltKey).'###'.$this->saltIndex;

        return hash_equals($expectedChecksum, $receivedChecksum);
    }
}

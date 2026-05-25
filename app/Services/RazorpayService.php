<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayService
{
    protected Api $api;

    protected string $keyId;

    protected string $keySecret;

    public function __construct()
    {
        $this->keyId = config('razorpay.key_id');
        $this->keySecret = config('razorpay.key_secret');
        $this->api = new Api($this->keyId, $this->keySecret);
    }

    /**
     * Create a Razorpay order
     *
     * @param  string  $receiptId  Unique receipt/transaction ID
     * @param  int  $amountPaise  Amount in paise (₹1 = 100 paise)
     * @param  array  $notes  Additional metadata
     * @return array Order details
     */
    public function createOrder(string $receiptId, int $amountPaise, array $notes = []): array
    {
        try {
            $order = $this->api->order->create([
                'receipt' => $receiptId,
                'amount' => $amountPaise,
                'currency' => 'INR',
                'notes' => $notes,
                'partial_payment' => false,
            ]);

            return [
                'success' => true,
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'status' => $order->status,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'receipt' => $receiptId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment signature (for frontend callback)
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay signature verification failed', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch payment details
     */
    public function fetchPayment(string $paymentId): ?array
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);

            return [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'method' => $payment->method,
                'email' => $payment->email ?? null,
                'contact' => $payment->contact ?? null,
                'created_at' => $payment->created_at,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay payment fetch failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch order status
     */
    public function fetchOrderStatus(string $orderId): ?string
    {
        try {
            $order = $this->api->order->fetch($orderId);

            return $order->status ?? null;
        } catch (\Exception $e) {
            Log::error('Razorpay fetch order failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param  string  $payload  Raw request body
     * @param  string  $signature  X-Razorpay-Signature header
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            $webhookSecret = config('razorpay.webhook_secret');
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Razorpay webhook verification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

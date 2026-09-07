<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Collection;

class RazorpayService
{
    protected Api $api;

    protected string $keyId;

    protected string $keySecret;

    public function __construct(?Api $api = null)
    {
        $this->keyId = (string) config('razorpay.key_id');
        $this->keySecret = (string) config('razorpay.key_secret');
        $this->api = $api ?? new Api($this->keyId, $this->keySecret);
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
                'payment_capture' => 1,
            ]);

            return [
                'success' => true,
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'status' => $order->status,
            ];
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            Log::error('Razorpay payment fetch failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Capture an authorized payment
     *
     * @param  string  $paymentId  Razorpay payment ID
     * @param  int  $amountPaise  Must equal the authorized amount
     */
    public function capturePayment(string $paymentId, int $amountPaise): bool
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);
            $payment->capture([
                'amount' => $amountPaise,
                'currency' => 'INR',
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Razorpay payment capture failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return false;
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
        } catch (\Throwable $e) {
            Log::error('Razorpay fetch order failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch all payments attempted against an order
     *
     * @return array<int, array{id: string, status: string, amount: int}>
     */
    public function fetchOrderPayments(string $orderId): array
    {
        try {
            $payments = $this->api->order->fetch($orderId)->payments();

            if (! $payments) {
                return [];
            }

            // In Razorpay PHP SDK, if an order has 0 payments, $payments->items is hydrated
            // as an empty Razorpay\Api\Collection object (due to an SDK isAssocArray([]) bug)
            // rather than a standard PHP array.
            $rawItems = $payments->items ?? [];
            if (! is_array($rawItems)) {
                $rawItems = ($rawItems instanceof Collection && $rawItems->count() === 0)
                    ? []
                    : ($rawItems instanceof \Traversable ? iterator_to_array($rawItems) : []);
            }

            $result = [];
            foreach ($rawItems as $payment) {
                if (is_object($payment)) {
                    $result[] = [
                        'id' => (string) ($payment->id ?? ''),
                        'status' => (string) ($payment->status ?? ''),
                        'amount' => (int) ($payment->amount ?? 0),
                    ];
                } elseif (is_array($payment)) {
                    $result[] = [
                        'id' => (string) ($payment['id'] ?? ''),
                        'status' => (string) ($payment['status'] ?? ''),
                        'amount' => (int) ($payment['amount'] ?? 0),
                    ];
                }
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Razorpay fetch order payments failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [];
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
        } catch (\Throwable $e) {
            Log::error('Razorpay webhook verification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

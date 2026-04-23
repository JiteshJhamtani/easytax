<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Application;
use App\Services\FormValidator;
use App\Services\PhonePeService;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Services\ApplicationLogger;
use App\Models\PaymentLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\ApplicationDocumentService;

class ApplicationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store Application & Initiate Payment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $slug, FormValidator $validator, ApplicationDocumentService $applicationDocumentService)
    {
        $service = Service::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $validated = $validator->validate(
            $service->slug,
            $request->all()
        );

        // Create draft application
        $application = Application::create([
            'agent_id'       => auth()->id(),
            'service_id'     => $service->id,
            'form_data'      => $validated,
            'amount'         => $service->price,
            'status'         => ApplicationStatus::DRAFT,
            'payment_status' => PaymentStatus::PENDING,
        ]);


        $applicationDocumentService->handleUploads(
            $application,
            $request,
            $service->slug
        );

        ApplicationLogger::log(
            $application->id,
            'application_created'
        );
        $transactionId = 'TXN_' . Str::uuid();

        $application->update([
            'payment_reference' => $transactionId
        ]);

        $phonePe = new PhonePeService();

        $response = $phonePe->createPayment(
            $transactionId,
            (int) ($service->price * 100), // convert to paise
            (string) auth()->id(),
            route('payment.redirect'),
            route('payment.webhook')
        );

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $transactionId,
            'event'          => 'initiated',
            'status'         => null,
            'payload'        => $validated,
            'response'       => $response,
        ]);
        if (isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            return redirect()->away(
                $response['data']['instrumentResponse']['redirectInfo']['url']
            );
        }

        return back()->with('error', 'Payment initialization failed.');
    }

    /*
    |--------------------------------------------------------------------------
    | PhonePe Webhook (Authoritative Payment Confirmation)
    |--------------------------------------------------------------------------
    */

    public function webhook(Request $request)
    {

        \Log::info('WEBHOOK HIT', [
            'headers' => $request->headers->all(),
            'body'    => $request->getContent(),
        ]);
        $expectedUser = config('phonepe.webhook_username');
        $expectedPass = config('phonepe.webhook_password');

        if (
            $request->getUser() !== $expectedUser ||
            $request->getPassword() !== $expectedPass
        ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $payload  = $request->getContent();
        $checksum = $request->header('X-VERIFY');

        $phonePe = new PhonePeService();

        // Verify signature
        if (!$phonePe->verifyWebhook($payload, $checksum)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data = json_decode($payload, true);

        $transactionId = $data['data']['merchantTransactionId'] ?? null;
        $state         = $data['data']['state'] ?? null;

        if (!$transactionId) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $application = Application::where('payment_reference', $transactionId)->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        // Prevent double processing
        if ($application->payment_status === PaymentStatus::PAID) {
            return response()->json(['success' => true]);
        }

        if ($state === 'COMPLETED') {

            $service = $application->service;

            $commission = 0;

            if ($application->agent_id) {
                $commission = $service->calculateCommission(
                    (float) $application->amount
                );
            }

            $application->update(attributes: [
                'payment_status'    => PaymentStatus::PAID,
                'status'            => ApplicationStatus::SUBMITTED,
                'submitted_at'      => now(),
                'commission_amount' => $commission,
            ]);

            activity('application')
                ->performedOn($application)
                ->causedBy(auth()->user())
                ->log('Payment successful');

            $admins = \App\Models\User::where('role', 'ADMIN')->where('is_active', true)->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ApplicationSubmittedNotification($application));
        } else {

            $application->update([
                'payment_status' => PaymentStatus::FAILED,
            ]);
        }

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $transactionId,
            'event'          => 'webhook',
            'status'         => $state,
            'payload'        => $data,
        ]);
        ApplicationLogger::log(
            $application->id,
            'payment_success'
        );
        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect Handler (UX Only)
    |--------------------------------------------------------------------------
    */

    public function redirect(Request $request)
    {
        $transactionId = $request->merchantTransactionId ?? null;

        return redirect()->route('payment.result', [
            'txn' => $transactionId
        ]);
    }


    public function retryPayment(Application $application)
    {
        if ($application->agent_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment_status === PaymentStatus::PAID) {
            return back()->with('error', 'Payment already completed.');
        }

        $transactionId = 'TXN_' . Str::uuid();

        $application->update([
            'payment_reference' => $transactionId,
            'payment_status'    => PaymentStatus::PENDING,
        ]);

        $phonePe = new PhonePeService();

        $response = $phonePe->createPayment(
            $transactionId,
            (int) ($application->amount * 100),
            (string) auth()->id(),
            route('payment.redirect'),
            route('payment.webhook')
        );

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $transactionId,
            'event'          => 'retry',
            'response'       => $response,
        ]);

        if (isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            return redirect()->away(
                $response['data']['instrumentResponse']['redirectInfo']['url']
            );
        }

        return back()->with('error', 'Retry failed.');
    }

    public function result(Request $request)
    {
        $transactionId = $request->query('txn');

        if (!$transactionId) {
            return view('agent.payment-result', [
                'application' => null,
                'error'       => 'Invalid transaction reference.'
            ]);
        }

        $application = Application::where('payment_reference', $transactionId)
            ->where('agent_id', auth()->id())
            ->first();

        if (!$application) {
            return view('agent.payment-result', [
                'application' => null,
                'error'       => 'Application not found.'
            ]);
        }

        return view('agent.payment-result', compact('application'));
    }


    public function checkStatus($transactionId)
    {
        $application = Application::where('payment_reference', $transactionId)
            ->where('agent_id', auth()->id())
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'INVALID'
            ]);
        }

        return response()->json([
            'status' => $application->payment_status->value
        ]);
    }
}

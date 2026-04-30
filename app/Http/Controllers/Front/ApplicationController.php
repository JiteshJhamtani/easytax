<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\PaymentLog;
use App\Models\Service;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Services\ApplicationDocumentService;
use App\Services\ApplicationLogger;
use App\Services\FormValidator;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store Application & Initiate Razorpay Order new feature for controller 
    the applicaton whos stutas is draft , failded and canceled should not display on admin dashboard it should stay on agent dashboard but not on admins 
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        string $slug,
        FormValidator $validator,
        ApplicationDocumentService $applicationDocumentService
    ) {
        Log::info("Application submission started for slug: {$slug}");

        // ==========================================
        // THIS IS THE PART THAT ACCIDENTALLY GOT DELETED!
        // We have to grab the Service and Validate the form first.
        $service = Service::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $validated = $validator->validate($service->slug, $request->all());
        // ==========================================  

        Log::info("Form validation passed.");

// ==========================================
        // DYNAMIC PRICING OVERRIDE ENGINE (ITR & GST)
        // ==========================================
        $finalPrice = $service->price;
$commission = $service->calculateCommission((float) $service->price);
        if (in_array($service->slug, ['gst-return-filing', 'itr-filing', 'gst-annual-package'])) {
            
            if ($service->slug === 'gst-return-filing') {
                $col1 = $validated['gst_type'] ?? '';
                $col2 = $validated['annual_turnover_range'] ?? '';
                $col3 = $validated['frequency_of_return'] ?? '';
                $col4 = $validated['plan'] ?? ''; 

               
                
                $rule = \App\Models\ServicePricingRule::where('service_id', $service->id)
                    ->where(function($q) use ($col1) { $q->where('gst_type', $col1)->orWhereNull('gst_type')->orWhere('gst_type', 'Any'); })
                    ->where(function($q) use ($col2) { $q->where('turnover', $col2)->orWhereNull('turnover')->orWhere('turnover', 'Any'); })
                    ->where(function($q) use ($col3) { $q->where('frequency', $col3)->orWhereNull('frequency')->orWhere('frequency', 'Any'); })
                    ->where(function($q) use ($col4) { $q->where('plan', $col4)->orWhereNull('plan')->orWhere('plan', 'Any'); })
                    ->orderByRaw("(plan = 'Any' OR plan IS NULL) ASC")->first();

            } elseif ($service->slug === 'gst-annual-package') {
                // ✅ FIX 2: Added the specific database query for the new service
                $turnoverVal = $validated['turnover'] ?? 'Any';

                $rule = \App\Models\ServicePricingRule::where('service_id', $service->id)
                    ->where(function($q) use ($turnoverVal) { 
                        $q->where('turnover', $turnoverVal)->orWhereNull('turnover')->orWhere('turnover', 'Any'); 
                    })
                    ->orderByRaw("(turnover = 'Any' OR turnover IS NULL) ASC")
                    ->first();
                    }
                    else {
                // 🛑 ITR TRUE DATABASE MAPPING 🛑
                // No translators needed! The frontend keys match the database EXACTLY.
                $itrType     = $request->input('itr_type', 'Any'); 
                $turnover    = $request->input('turnover', $request->input('business_turnover', 'Any')); 
                $itrBusiness = $request->input('has_business', 'Any');      
                $itrCapGains = $request->input('has_capital_gains', 'Any'); 

                // Query the Matrix with our STRICT scoring system to prevent fall-through!
                $rule = \App\Models\ServicePricingRule::where('service_id', $service->id)
                    ->where(function($q) use ($itrType) { 
                        $q->where('itr_type', $itrType)->orWhere('itr_type', strtolower($itrType))
                          ->orWhereNull('itr_type')->orWhereIn('itr_type', ['Any', 'any']); 
                    })
                    ->where(function($q) use ($turnover) { 
                        $q->where('turnover', $turnover)->orWhere('turnover', strtolower($turnover))
                          ->orWhereNull('turnover')->orWhereIn('turnover', ['Any', 'any']); 
                    })
                    ->where(function($q) use ($itrBusiness) { 
                        $q->where('itr_business', $itrBusiness)->orWhere('itr_business', strtolower($itrBusiness))
                          ->orWhereNull('itr_business')->orWhereIn('itr_business', ['Any', 'any']); 
                    })
                    ->where(function($q) use ($itrCapGains) { 
                        $q->where('itr_capital_gains', $itrCapGains)->orWhere('itr_capital_gains', strtolower($itrCapGains))
                          ->orWhereNull('itr_capital_gains')->orWhereIn('itr_capital_gains', ['Any', 'any']); 
                    })
                    // Calculate specificity score so the most exact match ALWAYS wins.
                    ->orderByRaw("
                        (CASE WHEN itr_capital_gains IS NOT NULL AND LOWER(itr_capital_gains) != 'any' THEN 1 ELSE 0 END) +
                        (CASE WHEN turnover IS NOT NULL AND LOWER(turnover) != 'any' THEN 1 ELSE 0 END) +
                        (CASE WHEN itr_business IS NOT NULL AND LOWER(itr_business) != 'any' THEN 1 ELSE 0 END) +
                        (CASE WHEN itr_type IS NOT NULL AND LOWER(itr_type) != 'any' THEN 1 ELSE 0 END) DESC
                    ")
                    ->first();
            }

            if ($rule) {
                $finalPrice = $rule->base_price;
                $commission = $rule->commission_amount;
            }
        } else {
            $commission = $service->calculateCommission((float) $service->price);
        }

        // Amount to charge via Razorpay (Wholesale Agent Price)
        $amountToPay = max(0, $finalPrice - $commission);

        $application = Application::create([
            'agent_id'          => auth()->id(),
            'service_id'        => $service->id,
            'form_data'         => $validated,
            'amount'            => $finalPrice, 
            'commission_amount' => $commission, 
            'status'            => ApplicationStatus::DRAFT,
            'payment_status'    => PaymentStatus::PENDING,
        ]);

        Log::info("Draft application created with ID: {$application->id}");

        $applicationDocumentService->handleUploads($application, $request, $service->slug);
        ApplicationLogger::log($application->id, 'application_created');

        // Create Razorpay order
        $receiptId = 'APP_' . $application->id . '_' . time();
        $razorpay  = new RazorpayService();

        $orderResponse = $razorpay->createOrder(
            $receiptId,
            (int) ($amountToPay * 100), // Amount in paise
            [
                'application_id' => $application->id,
                'agent_id'       => auth()->id(),
                'service_name'   => $service->name,
            ]
        );

        Log::info("Razorpay order creation response for application {$application->id}", $orderResponse);

        if (!$orderResponse['success']) {
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }

        // Store order_id as payment_reference
        $application->update([
            'payment_reference' => $orderResponse['order_id'],
        ]);

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $receiptId,
            'event'          => 'order_created',
            'status'         => $orderResponse['status'] ?? null,
            'payload'        => $validated,
            'response'       => $orderResponse,
        ]);

        // Return to the form page with order details for Razorpay checkout
        return back()->with('razorpay_order', [
            'order_id'       => $orderResponse['order_id'],
            'amount'         => $orderResponse['amount'],
            'currency'       => $orderResponse['currency'],
            'application_id' => $application->id,
            'key_id'         => config('razorpay.key_id'),
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Payment Success Callback (from Razorpay frontend)
    |--------------------------------------------------------------------------
    */

    public function paymentSuccess(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $application = Application::where('payment_reference', $validated['razorpay_order_id'])
            ->where('agent_id', auth()->id())
            ->firstOrFail();

        // Verify signature
        $razorpay = new RazorpayService();
        $isValid  = $razorpay->verifySignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature']
        );

        if (!$isValid) {
            Log::error('Razorpay signature verification failed', $validated);
            return redirect()->route('payment.result', ['txn' => $application->payment_reference])
                ->with('error', 'Payment verification failed.');
        }

        // Fetch payment details
        $payment = $razorpay->fetchPayment($validated['razorpay_payment_id']);

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $validated['razorpay_payment_id'],
            'event'          => 'payment_success',
            'status'         => $payment['status'] ?? 'captured',
            'payload'        => null,
            'response'       => $payment,
        ]);

        // Update application
        $application->update([
            'payment_status' => PaymentStatus::PAID,
            'status'         => ApplicationStatus::SUBMITTED,
            'submitted_at'   => now(),
        ]);

        activity('application')
            ->performedOn($application)
            ->causedBy($application->agent)
            ->log('Payment confirmed via Razorpay');

        // Notify admins
        $admins = \App\Models\User::where('role', 'ADMIN')
            ->where('is_active', true)
            ->get();

        \Illuminate\Support\Facades\Notification::send(
            $admins,
            new \App\Notifications\ApplicationSubmittedNotification($application)
        );

        ApplicationLogger::log($application->id, 'payment_success');

        return redirect()->route('payment.result', ['txn' => $application->payment_reference]);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Failure Callback
    |--------------------------------------------------------------------------
    */

    public function paymentFailure(Request $request)
    {
        $orderId = $request->input('razorpay_order_id');

        if (!$orderId) {
            return redirect()->route('services.index')
                ->with('error', 'Invalid payment reference.');
        }

        $application = Application::where('payment_reference', $orderId)
            ->where('agent_id', auth()->id())
            ->first();

        if ($application) {
            $application->update(['payment_status' => PaymentStatus::FAILED]);

            PaymentLog::create([
                'application_id' => $application->id,
                'transaction_id' => $orderId,
                'event'          => 'payment_failed',
                'status'         => 'failed',
                'payload'        => $request->all(),
                'response'       => null,
            ]);

            ApplicationLogger::log($application->id, 'payment_failed');
        }

        return redirect()->route('payment.result', ['txn' => $orderId]);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Result Page
    |--------------------------------------------------------------------------
    */

    public function result(Request $request)
    {
        $transactionId = $request->query('txn');

        if (!$transactionId) {
            return view('agent.payment-result', [
                'application' => null,
                'error'       => 'Invalid transaction reference.',
            ]);
        }

        $application = Application::where('payment_reference', $transactionId)
            ->where('agent_id', auth()->id())
            ->first();

        if (!$application) {
            return view('agent.payment-result', [
                'application' => null,
                'error'       => 'Application not found.',
            ]);
        }

        return view('agent.payment-result', compact('application'));
    }

    /*
    |--------------------------------------------------------------------------
    | Retry Payment
    |--------------------------------------------------------------------------
    */

    public function retryPayment(Application $application)
    {
        if ($application->agent_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment_status === PaymentStatus::PAID) {
            return back()->with('error', 'Payment already completed.');
        }

        // Re-use stored commission
        $amountToPay = max(0, $application->amount - $application->commission_amount);
        $receiptId   = 'APP_' . $application->id . '_RETRY_' . time();

        $razorpay      = new RazorpayService();
        $orderResponse = $razorpay->createOrder(
            $receiptId,
            (int) ($amountToPay * 100),
            [
                'application_id' => $application->id,
                'agent_id'       => auth()->id(),
                'retry'          => true,
            ]
        );

        if (!$orderResponse['success']) {
            return back()->with('error', 'Retry failed. Please try again.');
        }

        $application->update([
            'payment_reference' => $orderResponse['order_id'],
            'payment_status'    => PaymentStatus::PENDING,
        ]);

        PaymentLog::create([
            'application_id' => $application->id,
            'transaction_id' => $receiptId,
            'event'          => 'retry_order_created',
            'response'       => $orderResponse,
        ]);

        return back()->with('razorpay_order', [
            'order_id'       => $orderResponse['order_id'],
            'amount'         => $orderResponse['amount'],
            'currency'       => $orderResponse['currency'],
            'application_id' => $application->id,
            'key_id'         => config('razorpay.key_id'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook Handler (for server-side confirmation)
    |--------------------------------------------------------------------------
    */

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        $razorpay = new RazorpayService();

        if (!$razorpay->verifyWebhook($payload, $signature)) {
            Log::error('Razorpay webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data  = json_decode($payload, true);
        $event = $data['event'] ?? null;

        Log::info('Razorpay webhook received', ['event' => $event, 'data' => $data]);

        // Handle payment.captured event
        if ($event === 'payment.captured') {
            $paymentId = $data['payload']['payment']['entity']['id'] ?? null;
            $orderId   = $data['payload']['payment']['entity']['order_id'] ?? null;

            if (!$orderId) {
                return response()->json(['error' => 'Missing order_id'], 400);
            }

            $application = Application::where('payment_reference', $orderId)->first();

            if (!$application) {
                Log::warning('Webhook: application not found for order', ['order_id' => $orderId]);
                return response()->json(['success' => true]); // Acknowledge anyway
            }

            // Only update if not already paid
            if ($application->payment_status !== PaymentStatus::PAID) {
                $application->update([
                    'payment_status' => PaymentStatus::PAID,
                    'status'         => ApplicationStatus::SUBMITTED,
                    'submitted_at'   => now(),
                ]);

                PaymentLog::create([
                    'application_id' => $application->id,
                    'transaction_id' => $paymentId,
                    'event'          => 'webhook_captured',
                    'status'         => 'captured',
                    'payload'        => $data,
                    'response'       => null,
                ]);

                ApplicationLogger::log($application->id, 'payment_success_webhook');
            }
        }

        // Handle payment.failed event
        if ($event === 'payment.failed') {
            $orderId = $data['payload']['payment']['entity']['order_id'] ?? null;

            if ($orderId) {
                $application = Application::where('payment_reference', $orderId)->first();

                if ($application && $application->payment_status !== PaymentStatus::PAID) {
                    $application->update(['payment_status' => PaymentStatus::FAILED]);

                    PaymentLog::create([
                        'application_id' => $application->id,
                        'transaction_id' => $data['payload']['payment']['entity']['id'] ?? null,
                        'event'          => 'webhook_failed',
                        'status'         => 'failed',
                        'payload'        => $data,
                        'response'       => null,
                    ]);

                    ApplicationLogger::log($application->id, 'payment_failed_webhook');
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function checkStatus($transactionId)
    {
        // 1. Try finding application via Razorpay order_id
        $application = Application::where('payment_reference', $transactionId)
            ->first();

        if ($application) {
            $paymentStatus = strtolower($application->payment_status->value ?? 'pending');

            // ✅ Primary source of truth
            return response()->json([
                'status' => match ($paymentStatus) {
                    'paid', 'success', 'completed' => 'SUCCESS',
                    'failed', 'error' => 'FAILED',
                    default => 'PENDING'
                }
            ]);
        }

        // 2. Fallback: check Payment Logs (rare cases)
        $log = PaymentLog::where('transaction_id', $transactionId)
            ->latest()
            ->first();

        if ($log) {
            $logStatus = strtolower($log->status ?? 'pending');

            return response()->json([
                'status' => match ($logStatus) {
                    'paid', 'captured', 'success' => 'SUCCESS',
                    'failed', 'error' => 'FAILED',
                    default => 'PENDING'
                }
            ]);
        }

        // 3. If nothing found → still return PENDING (not FAILED)
        return response()->json([
            'status' => 'PENDING'
           
        ]);
    }

  
  /**
     * --- SECURE CLIENT TRACKING PAGE ---
     */
    public function track(\Illuminate\Http\Request $request, \App\Models\Application $application)
    {
        // 1. Verify the cryptographic signature so hackers can't guess URLs
        if (! $request->hasValidSignature()) {
            abort(403, 'This tracking link has expired or is invalid.');
        }

        // 2. Load the related data needed for the view
        $application->load(['service', 'agent']);

        // 3. Send them to the public tracking view
        return view('front.pages.applications.track', compact('application'));
    }
}   
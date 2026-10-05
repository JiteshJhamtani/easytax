<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\Service;
use App\Services\SessionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalIntakeController extends Controller
{
    /**
     * Intake an online retail order and compliance documents from external sources (e.g., Drupal easytax.live).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idempotency_key' => 'required|string|max:128',
            'service_slug' => 'required|string|max:128',
            'service_name' => 'required|string|max:255',
            'customer.name' => 'required|string|max:255',
            'customer.phone' => 'required|string|max:32',
            'customer.email' => 'nullable|email|max:255',
            'payment.reference' => 'required|string|max:128',
            'payment.amount' => 'required|numeric|min:0',
            'payment.status' => 'nullable|string|max:64',
            'form_data' => 'nullable|array',
            'documents' => 'nullable|array',
            'documents.*.document_label' => 'required|string|max:255',
            'documents.*.file_name' => 'required|string|max:255',
            'documents.*.file_base64' => 'required|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // 1. Idempotency Check: Prevent duplicate applications on network retries
            $existing = Application::where('idempotency_key', $validated['idempotency_key'])->first();
            if ($existing) {
                // Attach any newly uploaded documents and return existing ID
                $this->attachDocuments($existing, $request->input('documents', []));

                return response()->json([
                    'ok' => true,
                    'status' => 'already_exists',
                    'application_id' => $existing->id,
                ]);
            }

            // 2. Resolve Service: Handle all 60+ services with Tiered Fallback
            $service = Service::where('slug', $validated['service_slug'])->first();
            $defaultService = Service::first();

            // 3. Create Retail Application Record
            $application = new Application;
            $application->source = 'WEBSITE_DIRECT';
            $application->agent_id = null; // Retail: 100% retained by company, zero agent commission
            $application->sub_agent_id = null;
            $application->service_id = $service ? $service->id : ($defaultService ? $defaultService->id : 1);
            $application->service_name_fallback = $validated['service_name'];
            $application->customer_name = $validated['customer']['name'];
            $application->customer_phone = $validated['customer']['phone'];
            $application->customer_email = $validated['customer']['email'] ?? null;
            $application->status = ApplicationStatus::SUBMITTED;
            $incomingPaymentStatus = strtoupper($validated['payment']['status'] ?? 'PAID');
            $application->payment_status = PaymentStatus::tryFrom($incomingPaymentStatus) ?? PaymentStatus::PAID;
            $application->payment_reference = $validated['payment']['reference'];
            $application->amount = $validated['payment']['amount'];
            $application->commission_amount = 0.00;
            $application->parent_margin = 0.00;
            $application->form_data = $validated['form_data'] ?? [];
            $application->idempotency_key = $validated['idempotency_key'];
            $application->session_label = class_exists(SessionResolver::class) ? (SessionResolver::current()['label'] ?? '2025-26 S1') : '2025-26 S1';
            $application->submitted_at = now();
            $application->save();

            // 4. Attach Documents with Dynamic Checklist Labels into Media Library
            $this->attachDocuments($application, $request->input('documents', []));

            // 5. Audit Log Entry
            ApplicationLog::create([
                'application_id' => $application->id,
                'user_id' => null,
                'event' => 'CREATED_VIA_WEBSITE',
                'meta' => [
                    'description' => "Online retail order received from {$application->customer_name} ({$application->customer_phone}) for {$validated['service_name']}. Total paid: ₹{$application->amount}",
                ],
            ]);

            return response()->json([
                'ok' => true,
                'status' => 'created',
                'application_id' => $application->id,
            ], 201);
        });
    }

    /**
     * Attach incoming base64 documents into the private client_documents collection.
     */
    protected function attachDocuments(Application $application, array $documents): void
    {
        foreach ($documents as $doc) {
            try {
                $base64Data = $doc['file_base64'] ?? '';
                if (str_contains($base64Data, ';base64,')) {
                    [, $base64Data] = explode(';base64,', $base64Data);
                }
                $binary = base64_decode($base64Data);
                if (! $binary) {
                    continue;
                }

                $originalName = $doc['file_name'] ?? 'document.pdf';
                $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $tempPath = tempnam(sys_get_temp_dir(), 'et_doc_').'_'.$safeName;
                file_put_contents($tempPath, $binary);

                $label = $doc['document_label'] ?? $originalName;

                $application->addMedia($tempPath)
                    ->usingName($label)
                    ->usingFileName($safeName)
                    ->withCustomProperties(['label' => $label, 'document_label' => $label])
                    ->toMediaCollection('client_documents', 'private');

                @unlink($tempPath);
            } catch (\Exception $e) {
                Log::error("Failed to attach document {$doc['file_name']}: ".$e->getMessage());
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\Service;
use App\Services\EasyTaxToolBridge;
use App\Services\SessionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $privatePath = storage_path('app/private');
        if (! File::isDirectory($privatePath)) {
            File::makeDirectory($privatePath, 0755, true, true);
        }

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
                $label = $doc['document_label'] ?? $originalName;

                // De-duplication check: if a file with the same label/name is already attached
                $existingDoc = $application->getMedia('client_documents')
                    ->first(function ($media) use ($label, $safeName) {
                        return ($media->getCustomProperty('label') === $label || $media->name === $label)
                            || $media->file_name === $safeName;
                    });

                if ($existingDoc) {
                    if ($existingDoc->file_name === $safeName && (int) $existingDoc->size === strlen($binary)) {
                        continue; // Identical file already attached, skip duplicate
                    }
                    // Replaced or updated file: remove old version before attaching new one
                    $existingDoc->delete();
                }

                $tempPath = tempnam(sys_get_temp_dir(), 'et_doc_').'_'.$safeName;
                file_put_contents($tempPath, $binary);

                $application->addMedia($tempPath)
                    ->usingName($label)
                    ->usingFileName($safeName)
                    ->withCustomProperties(['label' => $label, 'document_label' => $label])
                    ->toMediaCollection('client_documents', 'private');

                Log::info("Attached client document '{$label}' ({$safeName}) to Application #{$application->id}");

                @unlink($tempPath);
            } catch (\Exception $e) {
                Log::error("Failed to attach document {$doc['file_name']}: ".$e->getMessage());
            }
        }
    }

    /**
     * Download application document via secure HMAC bridge link.
     */
    public function downloadBridgeDocument(Request $request, int|string $media)
    {
        $mediaId = (int) $media;
        $mediaItem = Media::findOrFail($mediaId);
        $appId = (int) $request->query('app_id', 0);
        $expires = (int) $request->query('expires', 0);
        $sig = (string) $request->query('sig', '');

        if ($expires < time()) {
            return response()->json(['error' => 'Link expired'], 403);
        }

        $secret = config('services.easytax.external_secret') ?: env('EASYTAX_EXTERNAL_SECRET', EasyTaxToolBridge::DEFAULT_SECRET);
        $expectedSig = hash_hmac('sha256', "media_id={$mediaId}&app_id={$appId}&expires={$expires}", $secret);

        if (! hash_equals($expectedSig, $sig)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        if ((int) $mediaItem->model_id !== $appId || $mediaItem->model_type !== Application::class) {
            return response()->json(['error' => 'Application mismatch'], 403);
        }

        $path = $mediaItem->getPath();
        if (! file_exists($path)) {
            return response()->json(['error' => 'File not found on disk'], 404);
        }

        return response()->download($path, $mediaItem->file_name, [
            'Content-Type' => $mediaItem->mime_type ?: 'application/octet-stream',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        ]);
    }
}

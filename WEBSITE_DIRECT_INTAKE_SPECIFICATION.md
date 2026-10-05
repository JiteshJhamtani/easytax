# EasyTax B2B Dashboard: Website Direct Intake Specification
> **Architecture & Implementation Blueprint for `/var/www/uat.easytax.live`**  
> **Target:** Ingestion of Retail Customer Orders & Dynamic Compliance Documents from `easytax.live` (Drupal 10)  
> **Source Platform:** EasyTax Enterprise Laravel 12 B2B Compliance Suite  

---

## 1. Executive Summary & Objective

EasyTax operates two interconnected platforms:
1. **Public Website (`easytax.live` - Drupal 10):** Where direct retail customers browse 60+ legal, tax, and compliance services, pay online via Razorpay, and upload compliance documents dynamically extracted from each service's landing page checklist.
2. **Backoffice B2B Platform (`b2b.easytax.live` - Laravel 12):** Where Super Admins, Franchise Agents, and Backoffice Operators (Chartered Accountants) process applications, inspect documents, assign operators, and file government deliverables.

### Core Objective
Implement an asynchronous, idempotent intake pipeline that:
- Receives direct customer orders from `easytax.live` with customer contact information, payment reference, and pre-labeled compliance files.
- Isolates retail orders in a dedicated **"🌐 Website Direct Orders"** tab with priority SLA (distinct from wholesale Agent submissions).
- Prevents duplicate applications on payment retries using unique idempotency keys.
- Stores uploaded files directly in the private Media Library vault under `client_documents`.
- Triggers a completion webhook back to Drupal when the CA operator finishes government e-filing.

---

## 2. Database Schema Migration

Create migration: `database/migrations/2026_10_02_000001_add_website_direct_fields_to_applications_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('applications', function (Blueprint $table) {
            // Channel identification: 'AGENT', 'WEBSITE_DIRECT', 'VLE'
            if (!Schema::hasColumn('applications', 'source')) {
                $table->string('source', 50)->default('AGENT')->after('id')->index();
            }
            // Retail customer contact details
            if (!Schema::hasColumn('applications', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('source');
            }
            if (!Schema::hasColumn('applications', 'customer_phone')) {
                $table->string('customer_phone', 32)->nullable()->after('customer_name')->index();
            }
            if (!Schema::hasColumn('applications', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
            // Fallback for any of the 60+ services not explicitly in the services table
            if (!Schema::hasColumn('applications', 'service_name_fallback')) {
                $table->string('service_name_fallback')->nullable()->after('service_id');
            }
            // Idempotency key (stores Drupal order ID or Razorpay payment ID) to prevent duplicates
            if (!Schema::hasColumn('applications', 'idempotency_key')) {
                $table->string('idempotency_key', 128)->nullable()->unique()->after('payment_reference');
            }
        });
    }

    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'customer_name',
                'customer_phone',
                'customer_email',
                'service_name_fallback',
                'idempotency_key',
            ]);
        });
    }
};
```

Run migration:
```bash
php artisan migrate
```

---

## 3. Configuration & Security Middleware

### 3.1 Services Configuration
In `config/services.php`, add:
```php
'easytax' => [
    'external_secret' => env('EASYTAX_EXTERNAL_SECRET', 'et_live_sec_89347519283741928347'),
    'drupal_webhook_url' => env('DRUPAL_WEBHOOK_URL', 'https://easytax.live/api/v1/filing/status-sync'),
],
```
Add to your `.env`:
```env
EASYTAX_EXTERNAL_SECRET=et_live_sec_89347519283741928347
DRUPAL_WEBHOOK_URL=https://easytax.live/api/v1/filing/status-sync
```

### 3.2 Security Middleware
Create `app/Http/Middleware/VerifyExternalIntakeSecret.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyExternalIntakeSecret {
    public function handle(Request $request, Closure $next): Response {
        $secret = config('services.easytax.external_secret');
        $provided = $request->header('X-EasyTax-Secret');

        if (!$provided || !hash_equals($secret, $provided)) {
            return response()->json([
                'ok' => false,
                'error' => 'Unauthorized: Invalid or missing X-EasyTax-Secret header.',
            ], 401);
        }

        return $next($request);
    }
}
```

### 3.3 Register in `bootstrap/app.php` (Laravel 11/12)
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'external.intake' => \App\Http\Middleware\VerifyExternalIntakeSecret::class,
    ]);
    $middleware->validateCsrfTokens(except: [
        'api/v1/applications/external-intake',
    ]);
})
```

---

## 4. Inbound API Controller & Route

### 4.1 Route Definition
In `routes/api.php`, add:
```php
use App\Http\Controllers\Api\ExternalIntakeController;

Route::middleware(['external.intake'])->post('/applications/external-intake', [ExternalIntakeController::class, 'store']);
```

### 4.2 Controller Implementation
Create `app/Http/Controllers/Api/ExternalIntakeController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\Service;
use App\Services\SessionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalIntakeController extends Controller {

    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'idempotency_key' => 'required|string|max:128',
            'service_slug' => 'required|string|max:128',
            'service_name' => 'required|string|max:255',
            'customer.name' => 'required|string|max:255',
            'customer.phone' => 'required|string|max:32',
            'customer.email' => 'nullable|email|max:255',
            'payment.reference' => 'required|string|max:128',
            'payment.amount' => 'required|numeric|min:0',
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
            $application = new Application();
            $application->source = 'WEBSITE_DIRECT';
            $application->agent_id = null; // Retail: 100% retained by company, zero agent commission
            $application->sub_agent_id = null;
            $application->service_id = $service ? $service->id : ($defaultService ? $defaultService->id : 1);
            $application->service_name_fallback = $validated['service_name'];
            $application->customer_name = $validated['customer']['name'];
            $application->customer_phone = $validated['customer']['phone'];
            $application->customer_email = $validated['customer']['email'] ?? null;
            $application->status = 'SUBMITTED';
            $application->payment_status = 'PAID';
            $application->payment_reference = $validated['payment']['reference'];
            $application->amount = $validated['payment']['amount'];
            $application->commission_amount = 0.00;
            $application->parent_margin = 0.00;
            $application->form_data = $validated['form_data'] ?? [];
            $application->idempotency_key = $validated['idempotency_key'];
            $application->session_label = class_exists(SessionResolver::class) ? SessionResolver::current() : '2025-26 S1';
            $application->submitted_at = now();
            $application->save();

            // 4. Attach Documents with Dynamic Checklist Labels into Media Library
            $this->attachDocuments($application, $request->input('documents', []));

            // 5. Audit Log Entry
            ApplicationLog::create([
                'application_id' => $application->id,
                'user_id' => null,
                'action' => 'CREATED_VIA_WEBSITE',
                'description' => "Online retail order received from {$application->customer_name} ({$application->customer_phone}) for {$validated['service_name']}. Total paid: ₹{$application->amount}",
            ]);

            return response()->json([
                'ok' => true,
                'status' => 'created',
                'application_id' => $application->id,
            ], 201);
        });
    }

    protected function attachDocuments(Application $application, array $documents): void {
        foreach ($documents as $doc) {
            try {
                $base64Data = $doc['file_base64'];
                if (str_contains($base64Data, ';base64,')) {
                    [, $base64Data] = explode(';base64,', $base64Data);
                }
                $binary = base64_decode($base64Data);
                if (!$binary) continue;

                $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $doc['file_name']);
                $tempPath = tempnam(sys_get_temp_dir(), 'et_doc_') . '_' . $safeName;
                file_put_contents($tempPath, $binary);

                $application->addMedia($tempPath)
                    ->usingName($doc['document_label'])
                    ->withCustomProperties(['document_label' => $doc['document_label']])
                    ->toMediaCollection('client_documents', 'private');

                @unlink($tempPath);
            } catch (\Exception $e) {
                Log::error("Failed to attach document {$doc['file_name']}: " . $e->getMessage());
            }
        }
    }
}
```

---

## 5. UI: Dedicated "Website Direct Orders" Tab

### 5.1 Update Controller Query
In `App\Http\Controllers\Admin\ApplicationController.php`:
When building the query for the applications index:
```php
if ($request->get('tab') === 'website') {
    $query->where('source', 'WEBSITE_DIRECT');
} elseif ($request->get('tab') === 'agents') {
    $query->where('source', 'AGENT');
}
```

### 5.2 Add Tab to Blade View
In `resources/views/admin/applications/index.blade.php`:
Add a primary tab:
```html
<a href="{{ route('admin.applications.index', ['tab' => 'website']) }}" 
   class="px-4 py-2 text-sm font-semibold rounded-lg {{ request('tab') === 'website' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
    🌐 Website Direct Orders
</a>
```

### 5.3 Dedicated Table Columns for Website Orders
When `request('tab') === 'website'`, render:
- **App ID:** `#ET-{{ $app->id }}`
- **Customer:** Name + Clickable WhatsApp link (`https://wa.me/91{{ preg_replace('/\D/', '', $app->customer_phone) }}`)
- **Service:** `{{ $app->service_name_fallback ?: ($app->service ? $app->service->name : 'Tax Service') }}`
- **Amount Paid:** `₹{{ number_format($app->amount, 0) }}` with a green `PAID` pill
- **Documents:** Badge showing count of attached files: `📁 {{ $app->getMedia('client_documents')->count() }} Files`
- **Assigned CA:** Inline `<select>` dropdown for instant operator assignment
- **Status:** Standard stepper pill (`SUBMITTED`, `IN_PROGRESS`, `COMPLETED`)
- **Actions:** View Details / Process

---

## 6. Outbound Completion Webhook (Delivery to Customer)

When an operator marks the status as `COMPLETED` and uploads the final deliverable (GST Certificate / ITR Ack) in `App\Http\Controllers\Team\DashboardController.php`:

```php
if ($application->source === 'WEBSITE_DIRECT') {
    // 1. Dispatch Webhook to Drupal
    try {
        $drupalUrl = config('services.easytax.drupal_webhook_url');
        Http::withHeaders([
            'X-EasyTax-Secret' => config('services.easytax.external_secret'),
        ])->post($drupalUrl, [
            'idempotency_key' => $application->idempotency_key,
            'status' => 'COMPLETED',
            'arn_number' => $application->arn_number ?? null,
            'completed_at' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        Log::error("Failed to sync completion webhook to Drupal: " . $e->getMessage());
    }

    // 2. Dispatch Automated WhatsApp Notification
    // (Trigger your WhatsApp provider template: "Your filing for {service} is complete!")
}
```

---

## 7. Verification & Testing

Test the endpoint locally via curl:
```bash
curl -X POST http://127.0.0.1:8000/api/v1/applications/external-intake \
  -H "Content-Type: application/json" \
  -H "X-EasyTax-Secret: et_live_sec_89347519283741928347" \
  -d '{
    "idempotency_key": "test_pay_12345",
    "service_slug": "ngo-audit",
    "service_name": "Full NGO Audit",
    "customer": {
      "name": "Rajesh Sharma",
      "phone": "9876543210",
      "email": "rajesh@example.com"
    },
    "payment": {
      "reference": "test_pay_12345",
      "amount": 4999.00,
      "status": "PAID"
    },
    "form_data": {
      "notes": "Testing direct website intake"
    },
    "documents": [
      {
        "document_label": "Trust Deed / Registration Certificate",
        "file_name": "sample.pdf",
        "file_base64": "JVBERi0xLjQKJcfs..."
      }
    ]
  }'
```
Expected Response:
```json
{
  "ok": true,
  "status": "created",
  "application_id": 1
}
```

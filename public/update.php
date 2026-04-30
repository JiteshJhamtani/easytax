<?php

/**
 * EASYTAX SYSTEM UPDATER
 * Place this file in the 'public' folder.
 * Usage: https://your-domain.com/update.php?key=easytax_admin_2026
 */

// 1. SECURITY LOCK (Do not remove this!)
$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('<h2 style="color:red; font-family:sans-serif;">403 Forbidden: Invalid Security Key</h2>');
}

// 2. BOOTSTRAP LARAVEL
// This allows us to use Laravel's Database Schema Builder outside the normal app
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Boot the framework
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto;'>";
echo "<h1>🚀 EasyTax Database Updater</h1>";
echo "<ul style='line-height: 1.8; font-size: 16px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;'>";

/*
|--------------------------------------------------------------------------
| HOOK 1: Primary Data Field
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasColumn('services', 'primary_data_field')) {
        Schema::table('services', function (Blueprint $table) {
            $table->string('primary_data_field')->nullable()->after('slug');
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>primary_data_field</code> to the <b>services</b> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>primary_data_field</code> already exists.</li>";
    }
} catch (\Exception $e) {
    echo "<li>❌ <strong style='color:red;'>ERROR:</strong> Failed on Hook 1 - " . $e->getMessage() . "</li>";
}

/*
|--------------------------------------------------------------------------
| HOOK 2: WhatsApp Number Field
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasColumn('services', 'whatsapp_number_field')) {
        Schema::table('services', function (Blueprint $table) {
            $table->string('whatsapp_number_field')->nullable()->after('primary_data_field');
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>whatsapp_number_field</code> to the <b>services</b> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>whatsapp_number_field</code> already exists.</li>";
    }
} catch (\Exception $e) {
    echo "<li>❌ <strong style='color:red;'>ERROR:</strong> Failed on Hook 2 - " . $e->getMessage() . "</li>";
}

/*
|--------------------------------------------------------------------------
| HOOK 3: Applicant Email Field
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasColumn('services', 'applicant_email_field')) {
        Schema::table('services', function (Blueprint $table) {
            $table->string('applicant_email_field')->nullable()->after('whatsapp_number_field');
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>applicant_email_field</code> to the <b>services</b> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>applicant_email_field</code> already exists.</li>";
    }
} catch (\Exception $e) {
    echo "<li>❌ <strong style='color:red;'>ERROR:</strong> Failed on Hook 3 - " . $e->getMessage() . "</li>";
}

/*
|--------------------------------------------------------------------------
| HOOK 4: Service Sort Order
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasColumn('services', 'sort_order')) {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('price');
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>sort_order</code></li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>sort_order</code> already exists.</li>";
    }
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 4:</strong> " . $e->getMessage() . "</li>"; 
}

/*
|--------------------------------------------------------------------------
| HOOK 5: Create Leads Table (CRM)
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasTable('leads')) {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('service_interested')->nullable();
            $table->string('source')->nullable(); // Facebook, Direct, Referral, etc.
            $table->string('status')->default('NEW'); // NEW, CONTACTED, IN_DISCUSSION, CONVERTED, LOST
            $table->text('notes')->nullable();
            
            // Link to the marketer who generated this lead
            $table->foreignId('marketer_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Created <code>leads</code> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>leads</code> table already exists.</li>";
    }
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 5:</strong> " . $e->getMessage() . "</li>"; 
}


/* |--------------------------------------------------------------------------
| HOOK 6: Securely Expand 'role' ENUM for Marketers
|--------------------------------------------------------------------------
*/
try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'AGENT', 'MARKETER') DEFAULT 'AGENT'");
    echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Upgraded the Users 'role' ENUM to securely accept Marketers.</li>";
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 6:</strong> " . $e->getMessage() . "</li>"; 
}


/*
|--------------------------------------------------------------------------
| HOOK 7: Create Service Pricing Rules Table (Dynamic Pricing Matrix)
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasTable('service_pricing_rules')) {
        Schema::create('service_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('gst_type')->nullable(); 
            $table->string('turnover')->nullable(); 
            $table->string('frequency')->nullable(); 
            $table->string('plan')->nullable(); 
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->timestamps();
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Created <code>service_pricing_rules</code> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>service_pricing_rules</code> table already exists.</li>";
    }
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 7:</strong> " . $e->getMessage() . "</li>"; 
}


/*
|--------------------------------------------------------------------------
| HOOK 8: Add ITR Dynamic Pricing Columns
|--------------------------------------------------------------------------
*/
try {
    Schema::table('service_pricing_rules', function (Blueprint $table) {
        $added = false;

        if (!Schema::hasColumn('service_pricing_rules', 'itr_type')) {
            $table->string('itr_type')->nullable()->after('plan');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>itr_type</code> to pricing rules.</li>";
            $added = true;
        }
        if (!Schema::hasColumn('service_pricing_rules', 'itr_salary')) {
            $table->string('itr_salary')->nullable()->after('itr_type');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>itr_salary</code> to pricing rules.</li>";
            $added = true;
        }
        if (!Schema::hasColumn('service_pricing_rules', 'itr_business')) {
            $table->string('itr_business')->nullable()->after('itr_salary');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>itr_business</code> to pricing rules.</li>";
            $added = true;
        }
        if (!Schema::hasColumn('service_pricing_rules', 'itr_capital_gains')) {
            $table->string('itr_capital_gains')->nullable()->after('itr_business');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>itr_capital_gains</code> to pricing rules.</li>";
            $added = true;
        }
        if (!Schema::hasColumn('service_pricing_rules', 'itr_50l')) {
            $table->string('itr_50l')->nullable()->after('itr_capital_gains');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>itr_50l</code> to pricing rules.</li>";
            $added = true;
        }
        if (!Schema::hasColumn('service_pricing_rules', 'user_type')) {
            $table->string('user_type')->nullable()->after('itr_50l');
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>user_type</code> to pricing rules.</li>";
            $added = true;
        }

        if (!$added) {
            echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> ITR Pricing columns already exist.</li>";
        }
    });
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 8:</strong> " . $e->getMessage() . "</li>"; 
}

/*
|--------------------------------------------------------------------------
| HOOK 9: Add B2B Tracking Columns to Applications (NEW)
|--------------------------------------------------------------------------
*/
try {
    if (!Schema::hasColumn('applications', 'source_server')) {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('source_server')->nullable()->after('id')->index();
            $table->unsignedBigInteger('original_id')->nullable()->after('source_server')->index();
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>source_server</code> and <code>original_id</code> to the <b>applications</b> table.</li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> Tracking columns already exist in applications.</li>";
    }
} catch (\Exception $e) { 
    echo "<li>❌ <strong style='color:red;'>ERROR 9:</strong> " . $e->getMessage() . "</li>"; 
}



try {
    $b2bSecretKey = 'EasyTax_Super_Secret_Key_2026!'; // The password on UAT
    $childServers = [
        'uat' => 'https://uat.easytax.live', // Pulling UAT data
    ];

    // 🚨 TURN OFF STRICT MYSQL RULES TEMPORARILY
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    foreach ($childServers as $name => $url) {
        echo "<li>🔄 Fetching data from <b>{$url}</b>...</li>";

        // Find the highest ID we've already downloaded so we don't duplicate
        $lastId = \App\Models\Application::where('source_server', $name)->max('original_id') ?? 0;

        $response = \Illuminate\Support\Facades\Http::withToken($b2bSecretKey)
            ->timeout(30)
            ->get("{$url}/b2b/export-applications", ['last_id' => $lastId]);

        if ($response->successful()) {
            $applications = $response->json('data');
            $count = 0;

            foreach ($applications as $appData) {
                // Save them into the local Database
                \App\Models\Application::updateOrCreate(
                    ['source_server' => $name, 'original_id' => $appData['id']],
                    [
                        'agent_id'          => $appData['agent_id'],
                        'service_id'        => $appData['service_id'],
                        'form_data'         => is_array($appData['form_data']) ? json_encode($appData['form_data']) : $appData['form_data'],
                        'amount'            => $appData['amount'],
                        'commission_amount' => $appData['commission_amount'],
                        'status'            => $appData['status'],
                        'payment_status'    => $appData['payment_status'],
                        'submitted_at'      => $appData['submitted_at'],
                        'created_at'        => $appData['created_at'],
                        'updated_at'        => now(),
                    ]
                );
                $count++;
            }
            echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Pulled {$count} applications from {$name}.</li>";
        } else {
            echo "<li>❌ <strong style='color:red;'>FAILED:</strong> Connection to {$name} failed with status " . $response->status() . "</li>";
        }
    }

    // 🚨 TURN STRICT MYSQL RULES BACK ON SO THE DATABASE STAYS SAFE
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

} catch (\Exception $e) { 
    // Safety catch: Always turn rules back on even if there is an error
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "<li>❌ <strong style='color:red;'>ERROR 10:</strong> Data Sync Failed - " . $e->getMessage() . "</li>"; 
}

echo "</ul>";
echo "<p style='color: #666;'><strong>Done!</strong> Your server is now fully up to date and synced. You can safely close this page.</p>";
echo "<div style='background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin-top: 20px;'>
        <strong style='color: #d32f2f;'>⚠️ IMPORTANT SECURITY REMINDER:</strong><br>
        Please delete this <code>update.php</code> file from your server now to prevent unauthorized access.
      </div>";
echo "</div>";
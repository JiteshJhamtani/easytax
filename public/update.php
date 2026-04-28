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
    // We removed the duplicate lowercase words. MySQL will automatically have to update 
    // accept both 'MARKETER' and 'marketer' with this single clean definition.
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
| HOOK 8: Add ITR Dynamic Pricing Columns (NEW)
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


echo "</ul>";
echo "<p style='color: #666;'><strong>Done!</strong> Your server is now fully up to date. You can safely close this page.</p>";
echo "<div style='background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin-top: 20px;'>
        <strong style='color: #d32f2f;'>⚠️ IMPORTANT SECURITY REMINDER:</strong><br>
        Please delete this <code>update.php</code> file from your server now to prevent unauthorized access.
      </div>";
echo "</div>";
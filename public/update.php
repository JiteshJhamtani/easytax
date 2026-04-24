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

// HOOK 4: Service Sort Order
try {
    if (!Schema::hasColumn('services', 'sort_order')) {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('price');
        });
        echo "<li>✅ <strong style='color:green;'>SUCCESS:</strong> Added <code>sort_order</code></li>";
    } else {
        echo "<li>⏭️ <strong style='color:gray;'>SKIPPED:</strong> <code>sort_order</code> already exists.</li>";
    }
} catch (\Exception $e) { echo "<li>❌ <strong style='color:red;'>ERROR 4:</strong> " . $e->getMessage() . "</li>"; }
// Add future hooks down here!

echo "</ul>";
echo "<p style='color: #666;'><strong>Done!</strong> Your server is now fully up to date. You can safely close this page.</p>";
echo "</div>";
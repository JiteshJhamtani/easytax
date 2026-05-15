<?php

// 1. Security check
if (!isset($_GET['token']) || $_GET['token'] !== 'superadmin123') {
    die('Unauthorized access.');
}

// 2. Boot up Laravel's engine
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;'>";
echo "<h2>🚀 Operator Payment System Upgrade...</h2>";

try {
    // --- STEP 1: Add 'pending_reason' to applications --- 
    echo "<h3>Step 1: Applications Table</h3>";
    if (!Schema::hasColumn('applications', 'pending_reason')) {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('pending_reason')->nullable()->after('status');
        });
        echo "<p style='color: green;'>✅ Successfully added 'pending_reason' column.</p>";
    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'pending_reason' column already exists.</p>";
    }

    // --- STEP 2: Create 'operator_service_rates' table ---
    echo "<h3>Step 2: Operator Service Rates Table</h3>";
    if (!Schema::hasTable('operator_service_rates')) {
        Schema::create('operator_service_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['operator_id', 'service_id']);
        });
        echo "<p style='color: green;'>✅ Successfully created 'operator_service_rates' table.</p>";
    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'operator_service_rates' table already exists.</p>";
    }

    // --- STEP 3: Create 'operator_payouts' table ---
    echo "<h3>Step 3: Operator Payouts Table</h3>";
    if (!Schema::hasTable('operator_payouts')) {
        Schema::create('operator_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->decimal('amount', 10, 2);
            $table->text('payment_note')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
        echo "<p style='color: green;'>✅ Successfully created 'operator_payouts' table.</p>";
    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'operator_payouts' table already exists.</p>";
    }

    // --- SUCCESS MESSAGE ---
    echo "<br><div style='background: #e6f4ea; border: 1px solid #ceead6; padding: 15px; color: #137333; border-radius: 8px;'>";
    echo "<strong>🎉 Database Upgrade Complete!</strong><br>";
    echo "The database is now ready for the new Operator Payment System.";
    echo "</div>";

    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "Please delete this file from your server after running it.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
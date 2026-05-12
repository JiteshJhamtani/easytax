<?php

// 1. Security check
if (!isset($_GET['token']) || $_GET['token'] !== 'superadmin123') {
    die('Unauthorized access.');
}
//https://uat.easytax.live/create-coupon-table.php?token=superadmin123
// 2. Boot up Laravel's engine
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: 0 auto;'>";
echo "<h2>🏗️ Upgrading Coupon System Database...</h2>";

try {
    // TASK 1: Create the new 'coupons' table (For fresh server installs)
    if (!Schema::hasTable('coupons')) {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('bonus_amount', 10, 2);
            $table->unsignedBigInteger('service_id')->nullable();
            
            // ── NEW PRO FEATURES ──
            $table->text('target_agents')->nullable(); // Stores multiple agents as JSON array e.g., [1, 5, 12]
            $table->integer('global_max_uses')->nullable(); // Total campaign limit (null = infinite)
            $table->integer('total_used')->default(0); // Live tracking counter
            // ──────────────────────
            
            $table->integer('max_uses_per_agent')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        echo "<p style='color: green;'>✅ Successfully created 'coupons' table.</p>";
        
        DB::table('coupons')->insert([
            ['code' => 'ITR50', 'bonus_amount' => 50.00, 'max_uses_per_agent' => 9999, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ITR30', 'bonus_amount' => 30.00, 'max_uses_per_agent' => 9999, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
        ]);
        echo "<p style='color: green;'>✅ Successfully inserted default codes.</p>";

    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'coupons' table already exists. Running upgrade checks...</p>";
        
        // TASK 1.5: Upgrade EXISTING table (For your UAT server)
        Schema::table('coupons', function (Blueprint $table) {
            
            // Added the new multiple agents JSON column
            if (!Schema::hasColumn('coupons', 'target_agents')) {
                $table->text('target_agents')->nullable()->after('service_id');
                echo "<p style='color: green;'>✅ Added 'target_agents' column for multiple targeted agents.</p>";
            }
            
            // Keeping agent_id check just in case you ran the previous script, so it doesn't crash
            if (!Schema::hasColumn('coupons', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('service_id');
            }
            
            if (!Schema::hasColumn('coupons', 'global_max_uses')) {
                $table->integer('global_max_uses')->nullable()->after('max_uses_per_agent');
                echo "<p style='color: green;'>✅ Added 'global_max_uses' column for campaign limits.</p>";
            }
            if (!Schema::hasColumn('coupons', 'total_used')) {
                $table->integer('total_used')->default(0)->after('global_max_uses');
                echo "<p style='color: green;'>✅ Added 'total_used' tracking counter.</p>";
            }
        });
    }

    // TASK 2: Update the existing 'applications' table
    if (Schema::hasTable('applications')) {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('agent_id');
                echo "<p style='color: green;'>✅ Added 'coupon_id' column to applications table.</p>";
            }
            if (!Schema::hasColumn('applications', 'coupon_bonus')) {
                $table->decimal('coupon_bonus', 10, 2)->default(0.00)->after('commission_amount');
                echo "<p style='color: green;'>✅ Added 'coupon_bonus' column to applications table.</p>";
            }
        });
    }

    echo "<br><div style='background: #e6f4ea; border: 1px solid #ceead6; padding: 15px; color: #137333; border-radius: 8px;'>";
    echo "<strong>🎉 Database Upgraded!</strong><br>";
    echo "Your database is now fully prepared to handle an array of multiple agents.";
    echo "</div>";

    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "You must now delete this <code>create-coupon-table.php</code> file from your server.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
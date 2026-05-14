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
echo "<h2>🚀 Unified Database Upgrade...</h2>";

try {
    // --- STEP 1: Add 'assigned_to' to applications ---
    echo "<h3>Step 1: Applications Table</h3>";
    if (!Schema::hasColumn('applications', 'assigned_to')) {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('agent_id');
        });
        echo "<p style='color: green;'>✅ Successfully added 'assigned_to' column to applications table.</p>";
    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'assigned_to' column already exists.</p>";
    }

    // --- STEP 2: Fix 'role' ENUM in users ---
    echo "<h3>Step 2: Users Table (Role Security Fix)</h3>";
    // We change this from an ENUM to a standard VARCHAR so it accepts ANY new roles you create in the future!
    DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'AGENT'");
    echo "<p style='color: green;'>✅ Successfully updated 'role' column to safely accept 'team' operators.</p>";

    // --- SUCCESS MESSAGE ---
    echo "<br><div style='background: #e6f4ea; border: 1px solid #ceead6; padding: 15px; color: #137333; border-radius: 8px;'>";
    echo "<strong>🎉 All Database Upgrades Complete!</strong><br>";
    echo "You can now assign applications AND create Team Operators without any database errors.";
    echo "</div>";

    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "Please delete this <code>upgrade-team-role.php</code> file from your server after running it.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
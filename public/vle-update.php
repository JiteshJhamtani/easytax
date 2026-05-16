<?php

// 1. Security check
if (!isset($_GET['token']) || $_GET['token'] !== 'addvle123') {
    die('Unauthorized access.');
}

// 2. Boot up Laravel's engine
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;'>";
echo "<h2>🚀 Updating Database for VLE Feature...</h2>";

try {
    echo "<h3>Step 1: Checking Leads Table</h3>";
    
    // Check if column exists, if not, add it
    if (!Schema::hasColumn('leads', 'amount')) {
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('status');
        });
        echo "<p style='color: green;'>✅ Successfully added 'amount' column to the leads table.</p>";
    } else {
        echo "<p style='color: #0056b3;'>ℹ️ 'amount' column already exists.</p>";
    }

    // --- SUCCESS MESSAGE ---
    echo "<br><div style='background: #e6f4ea; border: 1px solid #ceead6; padding: 15px; color: #137333; border-radius: 8px;'>";
    echo "<strong>🎉 VLE Database Upgrade Complete!</strong><br>";
    echo "Your database is now ready to accept VLE customers with payment amounts.";
    echo "</div>";

    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "Please delete the <code>vle-update.php</code> file from your server after running it.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
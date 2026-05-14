<?php

// 1. Security check
if (!isset($_GET['token']) || $_GET['token'] !== 'superadmin123') {
    die('Unauthorized access.');
}

// 2. Boot up Laravel's engine
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;'>";

try {
    // 3. RAW DATABASE DELETION new task : 
    // Targeting Agent IDs 430 AND 462, and ONLY deleting Draft or Failed applications
    $deletedCount = \Illuminate\Support\Facades\DB::table('applications')
        ->whereIn('agent_id', [430, 462]) 
        ->where(function ($query) {
            // Checks for both lowercase and uppercase to be safe with Enums
            $query->whereIn('status', ['draft', 'DRAFT'])
                  ->orWhereIn('payment_status', ['failed', 'FAILED']);
        })
        ->delete();              

    // 4. Print the Success Message
    echo "<h2 style='color: #1e9c5d;'>✅ Target Applications Deleted</h2>";
    echo "<p>System securely connected to Agent IDs: <strong>430</strong> and <strong>462</strong>.</p>";
    
    if ($deletedCount > 0) {
        echo "<p>Successfully deleted <strong>{$deletedCount}</strong> Draft/Failed application records from the database.</p>";
        echo "<p style='color: #b06000;'><em>Note: Paid or Submitted applications were protected. Physical documents were safely left on your server's hard drive.</em></p>";
    } else {
        echo "<p>No Draft or Failed applications were found for these agents. Nothing was deleted.</p>";
    }
    
    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "You must now delete this <code>cleanup-agent.php</code> file from your server's public folder.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<p>Something went wrong:</p>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
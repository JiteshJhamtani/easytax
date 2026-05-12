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
    // 3. ULTRA-SAFE DELETION QUERY
    // This locks strictly onto Agent ID 430 and only touches Draft/Failed statuses.
    $deletedCount = \Illuminate\Support\Facades\DB::table('applications')
        ->where('agent_id', 430) // <--- STRICT LOCK ON RAHUL SHARMA'S ID
        ->whereIn('status', ['DRAFT', 'FAILED', 'draft', 'failed'])
        ->delete();

    // 4. Print the Success Message
    echo "<h2 style='color: #1e9c5d;'>✅ Cleanup Successful</h2>";
    echo "<p>System securely connected to Agent ID: <strong>430</strong> (AGT-000002 | rahulg7725@gmail.com).</p>";
    
    if ($deletedCount > 0) {
        echo "<p>Successfully and permanently deleted <strong>{$deletedCount}</strong> Draft/Failed applications exclusively for this agent.</p>";
        echo "<p><em>Note: All Completed, Pending, or In Progress applications were kept safe.</em></p>";
    } else {
        echo "<p>No Draft or Failed applications were found for this agent. Nothing was deleted.</p>";
    }
    
    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong>🚨 CRITICAL SECURITY STEP:</strong><br>";
    echo "You must now delete this <code>cleanup-agent.php</code> file from your server's public folder to prevent unauthorized access.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'>❌ Error Occurred</h2>";
    echo "<p>Something went wrong:</p>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
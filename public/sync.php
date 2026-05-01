<?php
/**
 * EASYTAX MASTER SYNC (Data Puller)
 * Place this file ONLY on the B2B Server.
 */
$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) die('403 Forbidden');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<div style='font-family: sans-serif; padding: 20px;'><h1>🔄 B2B Master Data Sync</h1><ul>";

try {
    $b2bSecretKey = 'EasyTax_Super_Secret_Key_2026!'; // The password on UAT
    // NOTE: You can add more servers here later (e.g., 'bihar' => 'https://bihar.easytax.live')
    $childServers = [
        'uat' => 'https://uat.easytax.live', 
    ];

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    foreach ($childServers as $name => $url) {
        // ... (PASTE ALL THE FETCH AGENTS AND FETCH APPLICATIONS CODE FROM HOOK 11 HERE) ...
        // (Keep the exact same updateOrCreate logic you already wrote, it was perfect).
    }

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "</ul><p>✅ Data Sync Complete!</p></div>";

} catch (\Exception $e) { 
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "<li>❌ <strong style='color:red;'>ERROR:</strong> Data Sync Failed - " . $e->getMessage() . "</li></ul></div>"; 
}
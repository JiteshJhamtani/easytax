<?php

// =========================================================================
// 1. SECURITY CHECK (Do not remove this!)
// =========================================================================
$secretToken = 'easytax_secure_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
    http_response_code(403);
    die('<h2 style="color:red; font-family:sans-serif;">Access Denied. Invalid token.</h2>');
}

// =========================================================================
// 2. BOOTSTRAP LARAVEL
// =========================================================================
// This connects the raw PHP file to your Laravel application
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// =========================================================================
// 3. RUN MIGRATION
// =========================================================================
echo "<div style='font-family: sans-serif; max-width: 800px; margin: 40px auto;'>";
echo "<h1>Database Migration Status</h1>";

try {
    // The --force flag is required because UAT acts like a production environment
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    
    echo "<h2 style='color: #16a34a;'>✅ Migration Successful!</h2>";
    echo "<pre style='background: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;'>" 
         . \Illuminate\Support\Facades\Artisan::output() . 
         "</pre>";
         
} catch (\Exception $e) {
    echo "<h2 style='color: #dc2626;'>❌ Migration Failed</h2>";
    echo "<pre style='background: #fef2f2; padding: 20px; border: 1px solid #fecaca; border-radius: 8px; font-size: 14px;'>" 
         . $e->getMessage() . 
         "</pre>";
}

echo "<p style='margin-top: 30px; font-weight: bold; color: #dc2626;'>CRITICAL: Delete this file (run-migration.php) from your server immediately now that it is finished.</p>";
echo "</div>";
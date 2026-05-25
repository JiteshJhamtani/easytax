<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// FIXED: Hardcoded secure key since we cannot edit the server .env file
if (($_GET['key'] ?? '') !== 'easytax_admin_2026') {
    die('<h1 style="color:red; font-family:sans-serif; text-align:center; margin-top:50px;">Unauthorized access.</h1>');
}

$output1 = "No migrations run.";
$output2 = "No seeders run.";
$output3 = "Cache not cleared.";
$output4 = "Views not cleared.";
$globalError = null;

try {
    // 1. Run Migrations (Critical)
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output1 = \Illuminate\Support\Facades\Artisan::output();

    // 2. Run Seeder (Critical)
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'RolesAndPermissionsSeeder',
        '--force' => true,
    ]);
    $output2 = \Illuminate\Support\Facades\Artisan::output();

    // 3. Clear Spatie Cache (Non-Critical)
    try {
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        $output3 = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        $output3 = "Warning: Permission cache failed to clear - " . $e->getMessage();
    }

    // 4. FIXED: Clear Blade/UI Caches (Non-Critical)
    try {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output4 = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        $output4 = "Warning: View/Optimize cache failed to clear - " . $e->getMessage();
    }

    // Beautiful Green Success Output
    echo '<div style="font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background-color: #dcfce7; border: 1px solid #166534; border-radius: 8px; color: #166534;">';
    echo '<h1 style="margin-top: 0;">✅ RBAC Upgrade Successful</h1>';
    echo '<h3>Migration Output:</h3><pre style="background:#fff; padding:10px; border-radius:4px;">' . htmlspecialchars($output1) . '</pre>';
    echo '<h3>Seeder Output:</h3><pre style="background:#fff; padding:10px; border-radius:4px;">' . htmlspecialchars($output2) . '</pre>';
    echo '<h3>Cache Output:</h3><pre style="background:#fff; padding:10px; border-radius:4px;">' . htmlspecialchars($output3) . "\n" . htmlspecialchars($output4) . '</pre>';
    echo '</div>';

} catch (\Exception $e) {
    // Red Error Output
    echo '<div style="font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background-color: #fee2e2; border: 1px solid #991b1b; border-radius: 8px; color: #991b1b;">';
    echo '<h1 style="margin-top: 0;">❌ Deployment Error</h1>';
    echo '<pre style="background:#fff; padding:10px; border-radius:4px; overflow-x:auto;">' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '</div>';
}

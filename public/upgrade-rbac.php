<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (($_GET['key'] ?? '') !== env('DEPLOYMENT_KEY', 'easytax123')) {
    die('Unauthorized access to deployment script.');
}

try {
    // 1. Run the new Spatie permission migrations
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output1 = \Illuminate\Support\Facades\Artisan::output();

    // 2. Execute the Seeder safely
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'RolesAndPermissionsSeeder',
        '--force' => true,
    ]);
    $output2 = \Illuminate\Support\Facades\Artisan::output();

    // 3. Clear permission cache across the server
    \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
    $output3 = \Illuminate\Support\Facades\Artisan::output();

    echo '<h1>RBAC Upgrade Successful</h1>';
    echo "<pre>Migration Output:\n$output1</pre>";
    echo "<pre>Seeder Output:\n$output2</pre>";
    echo "<pre>Cache Output:\n$output3</pre>";

} catch (\Exception $e) {
    echo '<h1>Error</h1>';
    echo '<pre>'.$e->getMessage().'</pre>';
}

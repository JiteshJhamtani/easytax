<?php

/**
 * Temporary utility script to run the database seeders on the UAT server.
 * CAUTION: Delete this file after running it successfully!
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Illuminate\Database\Eloquent\Model::unguard();

    $user = \App\Models\User::updateOrCreate(
        ['email' => 'subadmin@gmail.com'],
        [
            'name' => 'Sub Admin',
            'password' => bcrypt('password123'),
            'role' => 'SUB-ADMIN',
            'is_active' => true
        ]
    );

    \Illuminate\Database\Eloquent\Model::reguard();

    echo "<h1>Initialization Complete</h1>";
    echo "<p>User 'subadmin@gmail.com' successfully created or updated with SUB-ADMIN role.</p>";
} catch (\Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
echo "<hr>";
echo "<p><strong>CRITICAL:</strong> Delete this file (init-uat.php) immediately from your repository and UAT server after verifying.</p>";

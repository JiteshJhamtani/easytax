<?php

/**
 * Temporary utility script to run the database seeders on the UAT server.
 * CAUTION: Delete this file after running it successfully!
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call('db:seed', ['--force' => true]);

echo "<h1>Database Seeding Complete</h1>";
echo "<p>Artisan Output:</p>";
echo "<pre>" . htmlentities($kernel->output()) . "</pre>";
echo "<hr>";
echo "<p><strong>CRITICAL:</strong> Delete this file (init-uat.php) immediately from your repository and UAT server after verifying.</p>";

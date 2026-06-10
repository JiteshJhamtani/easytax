<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (!Schema::hasColumn('users', 'deleted_at')) {
        DB::statement('ALTER TABLE users ADD deleted_at TIMESTAMP NULL DEFAULT NULL');
        echo "<h3>Success: Column 'deleted_at' added to users table successfully.</h3>";
    } else {
        echo "<h3>Info: Column 'deleted_at' already exists in the users table.</h3>";
    }
} catch (\Exception $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}

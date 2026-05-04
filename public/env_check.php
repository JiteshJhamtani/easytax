<?php
if (!isset($_GET['key']) || $_GET['key'] !== 'easytax_admin_2026') die('403');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// ✅ env() only works AFTER bootstrap
echo "APP_URL: <b>" . env('APP_URL') . "</b><br>";
echo "DB_DATABASE: <b>" . env('DB_DATABASE') . "</b><br>";
echo "DB_MASTER_DATABASE: <b>" . env('DB_MASTER_DATABASE') . "</b><br>";
echo "DB_MASTER_HOST: <b>" . env('DB_MASTER_HOST') . "</b><br>";
echo "DB_MASTER_USERNAME: <b>" . env('DB_MASTER_USERNAME') . "</b><br>";
?>
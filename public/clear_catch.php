<?php
if (!isset($_GET['key']) || $_GET['key'] !== 'easytax_admin_2026') die('403');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Artisan;

echo "<div style='font-family:sans-serif; padding:20px;'>";
echo "<h2>🧹 EasyTax Cache Cleaner</h2><ul>";

try {
    Artisan::call('config:clear');
    echo "<li>✅ Config cache cleared.</li>";
} catch (\Exception $e) {
    echo "<li>❌ Config cache error: " . $e->getMessage() . "</li>";
}

try {
    Artisan::call('cache:clear');
    echo "<li>✅ App cache cleared.</li>";
} catch (\Exception $e) {
    echo "<li>❌ App cache error: " . $e->getMessage() . "</li>";
}

try {
    Artisan::call('view:clear');
    echo "<li>✅ View cache cleared.</li>";
} catch (\Exception $e) {
    echo "<li>❌ View cache error: " . $e->getMessage() . "</li>";
}

try {
    Artisan::call('route:clear');
    echo "<li>✅ Route cache cleared.</li>";
} catch (\Exception $e) {
    echo "<li>❌ Route cache error: " . $e->getMessage() . "</li>";
}

// Show current env values to confirm master connection
echo "</ul>";
echo "<h2>🔍 Current ENV Values</h2>";
echo "<ul>";
echo "<li>APP_URL: <b>" . env('APP_URL') . "</b></li>";
echo "<li>DB_DATABASE: <b>" . env('DB_DATABASE') . "</b></li>";
echo "<li>DB_MASTER_DATABASE: <b>" . env('DB_MASTER_DATABASE') . "</b></li>";
echo "<li>DB_MASTER_HOST: <b>" . env('DB_MASTER_HOST') . "</b></li>";
echo "<li>DB_MASTER_USERNAME: <b>" . env('DB_MASTER_USERNAME') . "</b></li>";
echo "</ul>";

echo "<div style='background:#ffebee; border-left:4px solid #f44336; padding:15px; margin-top:20px;'>";
echo "<strong>⚠️ SECURITY REMINDER:</strong> Delete this file after use.";
echo "</div></div>";
?>
<?php
/**
 * ONE-TIME DATABASE CLEANUP SCRIPT
 */
$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) die('403 Forbidden');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h1>🛠 Database Cleanup Tool</h1>";

try {
    // 1. Update the missing application source servers
    $appsFixed = DB::update("UPDATE applications SET source_server = 'uat' WHERE source_server IS NULL");
    echo "<p>✅ Successfully linked <strong>{$appsFixed}</strong> ghost applications to UAT.</p>";

    // 2. Update the missing user source servers
    $usersFixed = DB::update("UPDATE users SET source_server = 'uat' WHERE source_server IS NULL");
    echo "<p>✅ Successfully linked <strong>{$usersFixed}</strong> ghost users to UAT.</p>";

    echo "<div style='padding: 15px; background: #ffeeba; color: #856404; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>🚨 CRITICAL: DELETE THIS FILE NOW</h3>";
    echo "Your database is fixed. Please delete fix_db.php from your server immediately for security reasons.</div>";

} catch (\Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";
?>
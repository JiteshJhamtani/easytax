<?php
/**
 * B2B SAFE BACKUP SCRIPT (Timeout & Memory Safe)
 * Usage: https://b2b-server.com/b2b-backup.php?key=admin_2026
 */
$secret = 'admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) die('Unauthorized');

// Bypass execution limits where possible
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$tablesToBackup = ['users', 'applications', 'services', 'pages'];
$backupDir = storage_path('app/backups');
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

$timestamp = date('Y-m-d_H-i-s');
echo "<div style='font-family:sans-serif; padding:20px;'><h2>🛡️ B2B Database Backup</h2><ul>";

foreach ($tablesToBackup as $table) {
    try {
        $file = "{$backupDir}/{$table}_{$timestamp}.json";
        $total = DB::table($table)->count();
        $chunkSize = 1000;
        
        file_put_contents($file, "["); // Start JSON array
        
        DB::table($table)->orderBy('id')->chunk($chunkSize, function($records) use ($file) {
            $data = [];
            foreach ($records as $record) {
                $data[] = json_encode($record);
            }
            file_put_contents($file, implode(",\n", $data) . ",\n", FILE_APPEND);
        });
        
        file_put_contents($file, "{}]", FILE_APPEND); // Close JSON array safely
        echo "<li>✅ Backed up <b>{$table}</b> ({$total} rows) to Storage folder.</li>";
    } catch (\Exception $e) {
        echo "<li style='color:red;'>❌ Failed backing up {$table}: " . $e->getMessage() . "</li>";
    }
}
echo "</ul><p>Backup Complete. Proceed to Phase 2.</p></div>";
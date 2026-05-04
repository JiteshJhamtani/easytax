<?php
if (!isset($_GET['key']) || $_GET['key'] !== 'easytax_admin_2026') die('403');
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;

echo "<div style='font-family:sans-serif; padding:20px;'>";

// ─── 1. BACKFILL null created_at from source ────────────────────────────────
echo "<h2>1. Backfilling null created_at from source...</h2>";

$synced = DB::table('applications')
    ->whereNotNull('source_server')
    ->whereNull('created_at')
    ->get(['id', 'original_id', 'source_server']);

$backfilled = 0;
foreach ($synced as $app) {
    $source = DB::connection('db_uat')
        ->table('applications')
        ->where('id', $app->original_id)
        ->value('created_at');

    if ($source) {
        DB::table('applications')
            ->where('id', $app->id)
            ->update(['created_at' => $source]);
        $backfilled++;
    }
}
echo "<p>✅ Backfilled <b>{$backfilled}</b> records with created_at from source.</p>";

// ─── 2. DELETE DUPLICATES ────────────────────────────────────────────────────
echo "<h2>2. Cleaning up duplicate synced records...</h2>";

DB::statement("
    DELETE a1 FROM applications a1
    INNER JOIN applications a2
    ON a1.source_server = a2.source_server
    AND a1.original_id = a2.original_id
    AND a1.id > a2.id
    WHERE a1.source_server IS NOT NULL
");
echo "<p>✅ Duplicates removed.</p>";

// ─── 3. VERIFICATION ─────────────────────────────────────────────────────────
echo "<h2>3. Verification</h2>";

$total = DB::table('applications')->count();
echo "Total applications: <b>{$total}</b><br>";

$nullDates = DB::table('applications')->whereNull('created_at')->count();
echo "Records with null created_at: <b>{$nullDates}</b><br>";

$dupes = DB::select("
    SELECT original_id, source_server, COUNT(*) as cnt
    FROM applications
    WHERE source_server IS NOT NULL
    GROUP BY original_id, source_server
    HAVING cnt > 1
    LIMIT 20
");
echo "Duplicate groups: <b>" . count($dupes) . "</b><br>";

// ─── 4. SAMPLE CHECK ─────────────────────────────────────────────────────────
echo "<h2>4. Sample synced records</h2>";
$sample = DB::table('applications')
    ->whereNotNull('source_server')
    ->limit(5)
    ->get(['id', 'original_id', 'source_server', 'status', 'created_at']);
echo "<pre>" . print_r($sample->toArray(), true) . "</pre>";

echo "</div>";
?>
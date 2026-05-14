<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) die('403 Forbidden');

// ✅ Bootstrap FIRST
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// ✅ THEN declare use statements 
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Application;

echo "<div style='font-family: sans-serif; padding: 20px; background:#f4f4f9; border-radius:10px;'>";
echo "<h1>🔄 EasyTax Master Data Sync</h1><ul>";

$childDbs = [
     'marketing' => 'db_marketing',
     'upwest'=> 'db_upwest',
];

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($childDbs as $name => $connectionName) {
    echo "<li><strong>Syncing from {$name} via direct DB connection...</strong></li><ul>";

    // Only read native records — skip already-synced copies to prevent infinite loop
    $uatUsers = DB::connection($connectionName)
        ->table('users')
        ->whereIn('role', ['AGENT', 'agent', 'MARKETER', 'marketer'])
        ->whereNull('source_server')
        ->get();

    $uatApplications = DB::connection($connectionName)
        ->table('applications')
        ->whereNull('source_server')
        ->orderBy('id')
        ->get();

    $agentIdMap = [];
    $userCount  = 0;
    $appCount   = 0;

    // ─── STEP 1: SYNC AGENTS ────────────────────────────────────────────────
    foreach ($uatUsers as $old_user) {
        if (empty($old_user->email)) continue;

        // ✅ FIX: Guarantee agent_code is unique on this destination server  
        // If the code from source is already taken by a DIFFERENT user, generate a new one
        $agentCode = $old_user->agent_code ?? null;

        if ($agentCode) {
            $takenByOther = User::where('agent_code', $agentCode)
                                ->where('email', '!=', $old_user->email)
                                ->exists();
            if ($takenByOther) {
                do {
                    $agentCode = 'AGT-' . strtoupper(substr(uniqid(), -6));
                } while (User::where('agent_code', $agentCode)->exists());
            }
        } else {
            // No agent_code at all — generate a guaranteed unique one
            do {
                $agentCode = 'AGT-' . strtoupper(substr(uniqid(), -6));
            } while (User::where('agent_code', $agentCode)->exists());
        }

        $b2bUser = User::updateOrCreate(
            ['email' => $old_user->email],
            [
                'name'          => $old_user->name,
                'role'          => $old_user->role ?? 'AGENT',
                'agent_code'    => $agentCode,
                'password'      => $old_user->password,
                'source_server' => $name,
                'original_id'   => $old_user->id,
            ]
        );

        $agentIdMap[$old_user->id] = $b2bUser->id;
        if ($b2bUser->wasRecentlyCreated) $userCount++;
    }

    // ─── STEP 2: SYNC APPLICATIONS ──────────────────────────────────────────
    foreach ($uatApplications as $old_app) {

        $b2bUserId = $agentIdMap[$old_app->agent_id]
            ?? User::where('original_id', $old_app->agent_id)
                   ->where('source_server', $name)
                   ->value('id');

        if (!$b2bUserId) continue;

        // Decode potentially double-encoded JSON
        $formData = $old_app->form_data;
        while (is_string($formData)) {
            $decoded = json_decode($formData, true);
            if (json_last_error() === JSON_ERROR_NONE) $formData = $decoded;
            else break;
        }
        if (!is_array($formData)) $formData = [];

        $appModel = Application::updateOrCreate(
            [
                'original_id'   => $old_app->id,
                'source_server' => $name,
            ],
            [
                'agent_id'           => $b2bUserId,
                'service_id'         => $old_app->service_id ?? null,
                'status'             => $old_app->status ?? 'pending',
                'payment_status'     => $old_app->payment_status ?? 'pending',
                'form_data'          => $formData,
                'amount'             => $old_app->amount ?? 0,
                'commission_amount'  => $old_app->commission_amount ?? 0,
                'created_at'         => $old_app->created_at,
                'submitted_at'       => $old_app->submitted_at ?? null,
                'completed_at'       => $old_app->completed_at ?? null,
            ]
        );

        if ($appModel->wasRecentlyCreated) $appCount++;
    }

    echo "<li>✅ <strong>{$userCount}</strong> new agents, <strong>{$appCount}</strong> new applications synced from <strong>{$name}</strong>.</li>";
    echo "<li style='color:gray;'>ℹ️ 0 new records on re-run = idempotency working correctly.</li>";
    echo "</ul>";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "</ul>";
echo "<div style='padding:15px; background:#d4edda; color:#155724; border-radius:5px; margin-top:15px;'>";
echo "<h3>✅ Sync Complete!</h3>";
echo "Re-running this script should always show <strong>0 new agents, 0 new applications</strong> if no new data was added on the child server.";
echo "</div></div>";
?>
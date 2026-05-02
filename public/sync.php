<?php
/**
 * EASYTAX MASTER SYNC (API Version)
 * Place this file ONLY on the B2B Server's public folder.
 */
$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) die('403 Forbidden');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Import Laravel Models and Tools
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

echo "<div style='font-family: sans-serif; padding: 20px; background:#f4f4f9; border-radius:10px;'>";
echo "<h1 style='color:#333;'>🔄 B2B Master Data Sync (API)</h1><ul>";

try {
    // This must match the B2B_SYNC_SECRET in your child server's .env
    $b2bSecretKey = 'EasyTax_Super_Secret_Key_2026!'; 
    
    // List all your active satellite servers here
    $childServers = [
        'uat' => 'https://uat.easytax.live', 
        // 'upwest' => 'https://upwest.easytax.live',
    ];

    // Turn off foreign key alarms during massive imports
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    foreach ($childServers as $name => $url) {
        echo "<li><strong>Connecting to {$name} API ({$url})...</strong></li>";
        
        // 🧠 THE FIX: Force Laravel to treat original_id as a strictly numerical integer!
        $lastAgentId = User::where('source_server', $name)->max(DB::raw('CAST(original_id AS UNSIGNED)')) ?? 0;
        $lastAppId = Application::where('source_server', $name)->max(DB::raw('CAST(original_id AS UNSIGNED)')) ?? 0;

        // 1. Fetch Agents via API
        $agentResponse = Http::withToken($b2bSecretKey)->timeout(60)->get($url . '/b2b/export-agents?last_id=' . $lastAgentId);
        
        // 2. Fetch Applications via API
        $appResponse = Http::withToken($b2bSecretKey)->timeout(60)->get($url . '/b2b/export-applications?last_id=' . $lastAppId);
        
        if (!$agentResponse->successful() || !$appResponse->successful()) {
            echo "<ul><li><span style='color:red;'>❌ Failed to connect to {$name} API.</span></li>";
            if (!$agentResponse->successful()) { echo "<li><strong style='color:red;'>Agents API Error (Status " . $agentResponse->status() . "):</strong> <div style='background:#fff; border:1px solid #ddd; padding:10px; overflow:auto; max-height:200px;'><pre>" . htmlspecialchars(substr($agentResponse->body(), 0, 800)) . "</pre></div></li>"; }
            if (!$appResponse->successful()) { echo "<li><strong style='color:red;'>Apps API Error (Status " . $appResponse->status() . "):</strong> <div style='background:#fff; border:1px solid #ddd; padding:10px; overflow:auto; max-height:200px;'><pre>" . htmlspecialchars(substr($appResponse->body(), 0, 800)) . "</pre></div></li>"; }
            echo "</ul>";
            continue;
        }

        // Extract the data arrays from the JSON response
        $uatUsers = $agentResponse->json()['data'] ?? [];
        $uatApplications = $appResponse->json()['data'] ?? [];

        $userCount = 0;
        $appCount = 0;

        // Create a memory map to link UAT IDs to B2B IDs instantly
        $agentIdMap = []; 

        echo "<ul>";
        
        // 🚦 STEP 1: SYNC AGENTS
        foreach ($uatUsers as $old_user) {
            
            if (empty($old_user['email'])) continue;

            $b2bUser = User::firstOrNew(['email' => $old_user['email']]);
            $agentCode = $old_user['agent_code'] ?? null;
            
            if (empty($agentCode)) {
                if ($b2bUser->exists && !empty($b2bUser->agent_code)) {
                    $agentCode = $b2bUser->agent_code;
                } else {
                    $agentCode = 'AGT-' . strtoupper(substr(uniqid(), -6));
                }
            }

            $b2bUser->name = $old_user['name'];
            $b2bUser->phone = $old_user['phone'] ?? null;
            $b2bUser->role = $old_user['role'] ?? 'agent';
            $b2bUser->agent_code = $agentCode;
            
            // Only update these if it's their FIRST time syncing to prevent multi-server overwrites
            if (!$b2bUser->exists) {
                $b2bUser->source_server = $name;
                $b2bUser->original_id = $old_user['id'];
            }

            if (!empty($old_user['password'])) {
                $b2bUser->password = $old_user['password'];
            } elseif (!$b2bUser->exists && empty($b2bUser->password)) {
                $b2bUser->password = bcrypt(uniqid()); 
            }
            
            $b2bUser->save();

            // Store the ID relationship in our fast RAM dictionary
            $agentIdMap[$old_user['id']] = $b2bUser->id;

            if ($b2bUser->wasRecentlyCreated || $b2bUser->wasChanged()) {
                $userCount++;
            }
        }

        // 🔗 STEP 2: RELINK APPLICATIONS
        foreach ($uatApplications as $old_app) {
            
            // FAST LOOKUP: Check our dictionary first! 
            $b2bUserId = $agentIdMap[$old_app['agent_id']] ?? null;

            // Fallback to database query ONLY if they synced in a previous batch
            if (!$b2bUserId) {
                $fallbackUser = User::where('original_id', $old_app['agent_id'])->where('source_server', $name)->first();
                if (!$fallbackUser) continue; // Skip if agent absolutely cannot be found
                $b2bUserId = $fallbackUser->id;
            }
                
            $formData = $old_app['form_data'] ?? [];
            // Decode potentially double-encoded JSON strings to ensure it's a pure array
            while (is_string($formData)) {
                $decoded = json_decode($formData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $formData = $decoded;
                } else {
                    break;
                }
            }
            
            if (!is_array($formData)) {
                $formData = [];
            }

            $appModel = Application::updateOrCreate(
                [
                    'original_id'   => $old_app['id'], 
                    'source_server' => $name 
                ],
                [
                    'agent_id'       => $b2bUserId, // Using our ultra-fast mapped ID
                    'service_id'     => $old_app['service_id'] ?? null,
                    'status'         => $old_app['status'] ?? 'pending',
                    'payment_status' => $old_app['payment_status'] ?? 'pending',
                    'form_data'      => $formData,
                    'amount'         => $old_app['amount'] ?? 0,
                    'commission_amount' => $old_app['commission_amount'] ?? 0,
                    'created_at'     => $old_app['created_at'] ?? null,
                    'submitted_at'   => $old_app['submitted_at'] ?? null,
                    'completed_at'   => $old_app['completed_at'] ?? null,
                ]
            );
            
            if ($appModel->wasRecentlyCreated || $appModel->wasChanged()) {
                $appCount++;
            }
        }
        
        echo "<li>✅ Successfully merged <strong>{$userCount}</strong> New/Updated Agents and <strong>{$appCount}</strong> New/Updated Applications from {$name}.</li>";
        echo "</ul>";
    }

    // Turn alarms back on
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "</ul><br><div style='padding:15px; background:#d4edda; color:#155724; border-radius:5px;'>";
    echo "<h3>✅ All API Data Syncs Complete!</h3>";
    echo "Check your B2B Dashboard. The missing agents and overlapping applications should now be perfectly merged.</div></div>";

} catch (\Exception $e) { 
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "<li>❌ <strong style='color:red;'>CRITICAL ERROR:</strong> Data Sync Failed - " . $e->getMessage() . "</li></ul></div>"; 
}
?>
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
    ];

    // Turn off foreign key alarms during massive imports
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    foreach ($childServers as $name => $url) {
        echo "<li><strong>Connecting to {$name} API ({$url})...</strong></li>";
        
        // 1. Fetch Agents via API
        $agentResponse = Http::withToken($b2bSecretKey)->timeout(60)->get($url . '/b2b/export-agents');
        
        // 2. Fetch Applications via API
        $appResponse = Http::withToken($b2bSecretKey)->timeout(60)->get($url . '/b2b/export-applications');
        
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

        echo "<ul>";
        
        // 🚦 STEP 1: SYNC AGENTS (The Traffic Cop) we have to build a another tab below other application 
        foreach ($uatUsers as $old_user) {
            
            // Skip if email is missing to prevent database errors 
            if (empty($old_user['email'])) continue;

            // Prepare user data safely. Check if the API hid the password!
            $userData = [
                'name'          => $old_user['name'],
                'phone'         => $old_user['phone'] ?? null,
                'role'          => $old_user['role'] ?? 'agent',
                'source_server' => $name,
                'original_id'   => $old_user['id'],
            ];
            
            // Only update password if provided. If missing and user is new, give a secure random fallback to avoid crashes.
            if (!empty($old_user['password'])) {
                $userData['password'] = $old_user['password'];
            } elseif (!User::where('email', $old_user['email'])->exists()) {
                $userData['password'] = bcrypt(uniqid()); 
            }

            // Update existing or create new. No duplicates!
            $b2bUser = User::updateOrCreate(
                ['email' => $old_user['email']], 
                $userData
            );
            $userCount++;

            // 🔗 STEP 2: RELINK APPLICATIONS FOR THIS AGENT
            // Find all applications belonging to this agent's old ID
            $agentApps = array_filter($uatApplications, function($app) use ($old_user) {
                return $app['agent_id'] == $old_user['id'];
            });

            foreach ($agentApps as $old_app) {
                
                // Format the JSON data safely just in case it comes over as an array
                $formData = is_array($old_app['form_data']) ? json_encode($old_app['form_data']) : $old_app['form_data'];

                Application::updateOrCreate(
                    [
                        'original_id'   => $old_app['id'], 
                        'source_server' => $name 
                    ],
                    [
                        'agent_id'    => $b2bUser->id, // MAGIC: Attach to the verified B2B User ID!
                        'service_id' => $old_app['service_id'] ?? null,
                        'status'     => $old_app['status'] ?? 'pending',
                        'payment_status' => $old_app['payment_status'] ?? 'pending',
                        'form_data'  => $formData,
                        'amount'     => $old_app['amount'] ?? 0,
                        'commission_amount' => $old_app['commission_amount'] ?? 0,
                        'created_at' => $old_app['created_at'] ?? null,
                        'submitted_at' => $old_app['submitted_at'] ?? null,
                        'completed_at' => $old_app['completed_at'] ?? null,
                    ]
                );
                $appCount++;
            }
        }
        
        echo "<li>✅ Successfully merged <strong>{$userCount}</strong> Agents and <strong>{$appCount}</strong> Applications from {$name}.</li>";
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
<?php
// 1. SECURITY LOCK
$secretKey = 'easytax_admin_2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Unauthorized');
}

// 2. BOOTSTRAP LARAVEL
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 3. TARGET CONFIGURATION
$b2bSecretKey = 'EasyTax_Super_Secret_Key_2026!';
$childServers = ['uat' => 'https://uat.easytax.live']; // Later, you will add Bihar, UP, Assam here!

\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($childServers as $name => $url) {
    
    // --- SYNC AGENTS ---
    $lastAgentId = \App\Models\User::where('source_server', $name)->max('original_id') ?? 0;
    $agentResp = \Illuminate\Support\Facades\Http::withToken($b2bSecretKey)->timeout(30)->get("{$url}/b2b/export-agents", ['last_id' => $lastAgentId]);
    
    if ($agentResp->successful()) {
        foreach ($agentResp->json('data') as $agentData) {
            try {
                \App\Models\User::updateOrCreate(
                    ['source_server' => $name, 'original_id' => $agentData['id']],
                    [
                        'name' => $agentData['name'], 'email' => $agentData['email'], 'password' => $agentData['password'], 
                        'phone' => $agentData['phone'] ?? null, 'role' => $agentData['role'], 'is_active' => $agentData['is_active'] ?? 1,
                        'agent_code' => $agentData['agent_code'] ?? null, 'created_at' => $agentData['created_at'], 'updated_at' => now(),
                    ]
                );
            } catch (\Exception $e) {} // Skip duplicates
        }
    }

    // --- SYNC APPLICATIONS ---
    $lastAppId = \App\Models\Application::where('source_server', $name)->max('original_id') ?? 0;
    $appResp = \Illuminate\Support\Facades\Http::withToken($b2bSecretKey)->timeout(30)->get("{$url}/b2b/export-applications", ['last_id' => $lastAppId]);

    if ($appResp->successful()) {
        foreach ($appResp->json('data') as $appData) {
            \App\Models\Application::updateOrCreate(
                ['source_server' => $name, 'original_id' => $appData['id']],
                [
                    'agent_id' => $appData['agent_id'], 'service_id' => $appData['service_id'],
                    'form_data' => is_array($appData['form_data']) ? json_encode($appData['form_data']) : $appData['form_data'],
                    'amount' => $appData['amount'], 'commission_amount' => $appData['commission_amount'],
                    'status' => $appData['status'], 'payment_status' => $appData['payment_status'],
                    'submitted_at' => $appData['submitted_at'], 'created_at' => $appData['created_at'], 'updated_at' => now(),
                ]
            );
        }
    }
}

\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Sync Complete.";
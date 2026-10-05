<?php
/**
 * EasyTax - Multi-Tier Agent Network Production Deployer & Schema Runner
 *
 * Runs schema updates, ancestry backfill, and cache clearance directly without CLI.
 *
 * URL:
 * https://your-domain.com/deploy_multi_tier_network.php?token=easytax_secure_deploy_2026
 *
 * IMPORTANT: Keep token secret and delete/protect file after production verification.
 */

// 1. Full error reporting so errors are visible instead of 500 white screens
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

@set_time_limit(300);
@ini_set('memory_limit', '512M');

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

define('LARAVEL_START', microtime(true));

// 2. Safe Laravel Bootstrapping
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Kernel::class);
    $kernel->bootstrap();
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Bootstrap Error</title><style>body{font-family:sans-serif;padding:40px;background:#0b1329;color:#fff;}</style></head><body>';
    echo '<h2 style="color:#f87171;">Laravel Bootstrap Error</h2>';
    echo '<p style="font-size:1.1rem;"><strong>'.htmlspecialchars($e->getMessage()).'</strong></p>';
    echo '<pre style="background:#131e3a;padding:15px;border-radius:8px;color:#93c5fd;overflow:auto;font-size:0.85rem;">'.htmlspecialchars($e->getTraceAsString()).'</pre>';
    echo '</body></html>';
    exit(1);
}

$expectedToken = 'easytax_secure_deploy_2026';
$providedToken = $_GET['token'] ?? $_POST['token'] ?? null;
$autoRun = ($providedToken === $expectedToken);
$shouldRun = $autoRun || (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['execute_update']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTax - Multi-Tier Agent Network Deployer</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; background: #0b1329; color: #f8fafc; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 28px 32px; border-radius: 16px 16px 0 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .header h1 { font-size: 1.5rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px; }
        .header p { color: #bae6fd; font-size: 0.9rem; margin-top: 6px; }
        .panel { background: #131e3a; border-radius: 0 0 16px 16px; padding: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 1px solid #1e293b; border-top: none; }
        
        .box { background: #0b1329; border: 1px solid #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .box-title { font-size: 0.95rem; font-weight: 700; color: #38bdf8; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; }
        
        .log-entry { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; align-items: flex-start; gap: 10px; }
        .log-entry:last-child { border-bottom: none; }
        .log-ok { color: #4ade80; }
        .log-info { color: #93c5fd; }
        .log-warn { color: #facc15; }
        .log-err { color: #f87171; }
        
        .btn-run { background: #0284c7; color: #ffffff; padding: 14px 28px; border-radius: 10px; font-weight: 700; font-size: 1rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-run:hover { background: #0369a1; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(2,132,199,0.3); }
        .alert-auth { background: #451a1a; border: 1px solid #b91c1c; color: #fca5a5; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-live { background: rgba(74, 222, 128, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.4); }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>
            <span>🌐</span> EasyTax Multi-Tier Agent Network Deployer
            <span class="status-badge badge-live">Production Engine</span>
        </h1>
        <p>Applies database schema updates, backfills ancestry lineage paths, and clears framework caches without SSH terminal.</p>
    </div>

    <div class="panel">
        <?php if (! $shouldRun) { ?>
            <?php if ($providedToken && $providedToken !== $expectedToken) { ?>
                <div class="alert-auth">
                    <strong>Authentication Failed:</strong> The deploy token provided is invalid.
                </div>
            <?php } ?>

            <div class="box">
                <div class="box-title">Authentication Required</div>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 20px;">
                    Provide the deployment token to execute database schema updates and lineage backfill.
                </p>
                <form method="POST">
                    <input type="password" name="token" placeholder="Enter Deployment Token" style="background:#1e293b; border:1px solid #334155; padding:12px 16px; border-radius:8px; color:#fff; width:100%; max-width:400px; margin-bottom:16px; font-family:monospace; display:block;">
                    <button type="submit" name="execute_update" value="1" class="btn-run">
                        Execute Multi-Tier Network Deployment
                    </button>
                </form>
            </div>
        <?php } else { ?>
            <div class="box">
                <div class="box-title">Execution Log</div>
                <?php
                $logs = [];
            $log = function ($msg, $type = 'info') use (&$logs) {
                $icons = ['ok' => '✔', 'info' => 'ℹ', 'warn' => '⚠', 'err' => '✖'];
                $color = ['ok' => 'log-ok', 'info' => 'log-info', 'warn' => 'log-warn', 'err' => 'log-err'][$type] ?? 'log-info';
                echo '<div class="log-entry '.$color.'"><span>'.($icons[$type] ?? '•').'</span> <span>'.htmlspecialchars($msg).'</span></div>';
                flush();
            };

            try {
                $log('Starting EasyTax Production Deployment...', 'info');

                // ========================================================
                // 0. ARTISAN MIGRATIONS (Standard Runner)
                // ========================================================
                $log('Running database migrations via artisan migrate --force...', 'info');
                try {
                    Artisan::call('migrate', ['--force' => true]);
                    $migrateOutput = trim(Artisan::output());
                    $log('Artisan migrate: '.($migrateOutput ?: 'No pending migrations.'), 'ok');
                } catch (Throwable $e) {
                    $log('Artisan migrate warning: '.$e->getMessage(), 'warn');
                }

                // ========================================================
                // 1. FAIL-SAFE SCHEMA UPDATES: applications table (Website Direct Intake)
                // ========================================================
                $log('Checking applications table schema...', 'info');
                if (Schema::hasTable('applications')) {
                    Schema::table('applications', function (Blueprint $table) use ($log) {
                        if (! Schema::hasColumn('applications', 'source')) {
                            $table->string('source', 50)->default('AGENT')->after('id')->index();
                            $log('Created column applications.source (VARCHAR 50, Default AGENT, Indexed)', 'ok');
                        } else {
                            $log('Column applications.source already exists.', 'info');
                        }

                        if (! Schema::hasColumn('applications', 'customer_name')) {
                            $table->string('customer_name')->nullable()->after('sub_agent_id');
                            $log('Created column applications.customer_name', 'ok');
                        }

                        if (! Schema::hasColumn('applications', 'customer_phone')) {
                            $table->string('customer_phone', 50)->nullable()->after('customer_name')->index();
                            $log('Created column applications.customer_phone (Indexed)', 'ok');
                        }

                        if (! Schema::hasColumn('applications', 'customer_email')) {
                            $table->string('customer_email')->nullable()->after('customer_phone');
                            $log('Created column applications.customer_email', 'ok');
                        }

                        if (! Schema::hasColumn('applications', 'service_name_fallback')) {
                            $table->string('service_name_fallback')->nullable()->after('customer_email');
                            $log('Created column applications.service_name_fallback', 'ok');
                        }

                        if (! Schema::hasColumn('applications', 'idempotency_key')) {
                            $table->string('idempotency_key')->nullable()->unique()->after('service_name_fallback');
                            $log('Created column applications.idempotency_key (Unique)', 'ok');
                        }
                    });
                }

                // ========================================================
                // 2. SCHEMA UPDATES: users table
                // ========================================================
                $log('Checking users table schema...', 'info');

                Schema::table('users', function (Blueprint $table) use ($log) {
                    if (! Schema::hasColumn('users', 'ancestry_path')) {
                        $table->string('ancestry_path', 500)->nullable()->after('parent_id')->index();
                        $log('Created column users.ancestry_path (VARCHAR 500, Indexed)', 'ok');
                    } else {
                        $log('Column users.ancestry_path already exists.', 'info');
                    }

                    if (! Schema::hasColumn('users', 'depth')) {
                        $table->unsignedInteger('depth')->default(1)->after('ancestry_path')->index();
                        $log('Created column users.depth (INT, Default 1, Indexed)', 'ok');
                    } else {
                        $log('Column users.depth already exists.', 'info');
                    }

                    if (! Schema::hasColumn('users', 'can_recruit')) {
                        $table->boolean('can_recruit')->default(true)->after('depth');
                        $log('Created column users.can_recruit (BOOLEAN, Default TRUE)', 'ok');
                    } else {
                        $log('Column users.can_recruit already exists.', 'info');
                    }
                });

                // ========================================================
                // 2. SCHEMA UPDATES: agent_margin_logs table
                // ========================================================
                if (Schema::hasTable('agent_margin_logs')) {
                    Schema::table('agent_margin_logs', function (Blueprint $table) use ($log) {
                        if (! Schema::hasColumn('agent_margin_logs', 'tier_level')) {
                            $table->unsignedInteger('tier_level')->default(1)->after('sub_agent_id')->index();
                            $log('Created column agent_margin_logs.tier_level (INT, Default 1, Indexed)', 'ok');
                        } else {
                            $log('Column agent_margin_logs.tier_level already exists.', 'info');
                        }
                    });

                    $marginIndexes = collect(Schema::getIndexes('agent_margin_logs'))->pluck('name')->all();

                    Schema::table('agent_margin_logs', function (Blueprint $table) use ($log, $marginIndexes) {
                        if (! in_array('agent_margin_logs_application_id_index', $marginIndexes)) {
                            $table->index('application_id', 'agent_margin_logs_application_id_index');
                            $log('Created standard index on agent_margin_logs.application_id', 'ok');
                        } else {
                            $log('Standard index on agent_margin_logs.application_id already exists.', 'info');
                        }

                        if (in_array('agent_margin_logs_application_id_unique', $marginIndexes)) {
                            $table->dropUnique('agent_margin_logs_application_id_unique');
                            $log('Dropped old single-application unique constraint on agent_margin_logs', 'ok');
                        } else {
                            $log('Old single-application unique constraint already dropped.', 'info');
                        }

                        if (! in_array('agent_margin_logs_app_parent_unique', $marginIndexes)) {
                            $table->unique(['application_id', 'parent_agent_id'], 'agent_margin_logs_app_parent_unique');
                            $log('Created composite unique constraint [application_id, parent_agent_id] on agent_margin_logs', 'ok');
                        } else {
                            $log('Composite unique constraint [application_id, parent_agent_id] already exists.', 'info');
                        }
                    });
                }

                // ========================================================
                // 3. ANCESTRY PATH & DEPTH BACKFILL FOR EXISTING AGENTS
                // ========================================================
                $log('Backfilling lineage paths and depth for existing agents...', 'info');

                DB::transaction(function () use ($log) {
                    // Pass 1: Set Root Agents (depth = 1, ancestry_path = "/{id}/")
                    $rootCount = DB::table('users')
                        ->where('role', 'AGENT')
                        ->whereNull('parent_id')
                        ->update([
                            'depth' => 1,
                            'ancestry_path' => DB::raw("CONCAT('/', id, '/')"),
                        ]);
                    $log("Backfilled {$rootCount} root master agents (depth: 1, path: /{id}/)", 'ok');

                    // Pass 2: Iteratively update child agents level by level
                    $maxIterations = 10;
                    $iteration = 0;
                    $totalChildrenBackfilled = 0;

                    do {
                        $iteration++;
                        // Find child agents whose parent already has an ancestry_path
                        $children = DB::table('users as c')
                            ->join('users as p', 'c.parent_id', '=', 'p.id')
                            ->where('c.role', 'AGENT')
                            ->whereNotNull('c.parent_id')
                            ->whereNotNull('p.ancestry_path')
                            ->where(function ($q) {
                                $q->whereNull('c.ancestry_path')
                                    ->orWhere('c.ancestry_path', '');
                            })
                            ->select('c.id', 'p.ancestry_path as parent_path', 'p.depth as parent_depth')
                            ->limit(500)
                            ->get();

                        if ($children->isEmpty()) {
                            break;
                        }

                        foreach ($children as $child) {
                            $childPath = $child->parent_path.$child->id.'/';
                            $childDepth = ((int) $child->parent_depth) + 1;

                            DB::table('users')
                                ->where('id', $child->id)
                                ->update([
                                    'ancestry_path' => $childPath,
                                    'depth' => $childDepth,
                                ]);
                            $totalChildrenBackfilled++;
                        }
                    } while ($iteration < $maxIterations);

                    $log("Backfilled {$totalChildrenBackfilled} sub-agents with hierarchical paths and depth.", 'ok');
                });

                // ========================================================
                // 4. FRAMEWORK CACHE REFRESH
                // ========================================================
                $log('Clearing Laravel framework caches...', 'info');
                Artisan::call('optimize:clear');
                $log('Ran artisan optimize:clear successfully.', 'ok');

                try {
                    Artisan::call('config:cache');
                    Artisan::call('route:cache');
                    Artisan::call('view:cache');
                    $log('Rebuilt config, route, and view caches.', 'ok');
                } catch (Throwable $e) {
                    $log('Cache rebuild notice: '.$e->getMessage(), 'info');
                }

                $log('EasyTax Production Deployment Completed Successfully!', 'ok');

            } catch (Throwable $e) {
                $log('FATAL ERROR: '.$e->getMessage(), 'err');
                $log('File: '.$e->getFile().':'.$e->getLine(), 'err');
            }
            ?>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="/agent/dashboard" class="btn-run" style="text-decoration:none;">
                    Go to Agent Dashboard &rarr;
                </a>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>

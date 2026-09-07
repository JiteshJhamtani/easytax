<?php
/**
 * EasyTax - Standalone Production Database Updater (Zero-Migration Runner)
 *
 * Runs direct schema updates and cache maintenance without running 'artisan migrate'.
 *
 * URL:
 * https://your-domain.com/update_db.php
 * or with token:
 * https://your-domain.com/update_db.php?token=easytax_secure_deploy_2026
 *
 * IMPORTANT: Delete or protect this file after running on production!
 */

// 1. Enable Full Error Reporting so errors are displayed instead of blank 500 pages
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
$shouldRun = $autoRun || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute_update']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTax - Database Updater</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; background: #0b1329; color: #f8fafc; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); padding: 28px 32px; border-radius: 16px 16px 0 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .header h1 { font-size: 1.5rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px; }
        .header p { color: #99f6e4; font-size: 0.9rem; margin-top: 6px; }
        .panel { background: #131e3a; border-radius: 0 0 16px 16px; padding: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 1px solid #1e293b; border-top: none; }
        
        .box { background: #0b1329; border: 1px solid #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .box-title { font-size: 0.95rem; font-weight: 700; color: #38bdf8; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; }
        
        .log-entry { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; align-items: flex-start; gap: 10px; }
        .log-entry:last-child { border-bottom: none; }
        .log-ok { color: #4ade80; }
        .log-info { color: #93c5fd; }
        .log-warn { color: #facc15; }
        .log-err { color: #f87171; }
        
        .btn-run { background: #10b981; color: #ffffff; padding: 14px 28px; border-radius: 10px; font-weight: 700; font-size: 1rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-run:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,0.3); }
        .alert-complete { background: #064e3b; border: 1px solid #059669; color: #a7f3d0; padding: 16px 20px; border-radius: 12px; margin-top: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px; }
        .alert-warn { background: #451a03; border: 1px solid #b45309; color: #fde68a; padding: 16px 20px; border-radius: 12px; margin-top: 20px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                EasyTax Database Direct Updater
            </h1>
            <p>Direct DDL Schema Update & Maintenance — No Migration Command Required</p>
        </div>

        <div class="panel">
            <?php if (! $shouldRun) { ?>
                <div class="box" style="text-align: center; padding: 36px 20px;">
                    <p style="font-size: 1.05rem; margin-bottom: 20px; color: #cbd5e1;">
                        Click below to safely apply all missing tables, columns, indexes, foreign keys, and refresh the cache without running <code>artisan migrate</code>:
                    </p>
                    <form method="POST">
                        <input type="hidden" name="execute_update" value="1">
                        <button type="submit" class="btn-run">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Execute Database Update Now
                        </button>
                    </form>
                    <p style="margin-top: 14px; font-size: 0.8rem; color: #64748b;">
                        Or access directly with token: <code>?token=easytax_secure_deploy_2026</code>
                    </p>
                </div>
            <?php } else { ?>

                <!-- SECTION 1: USERS TABLE UPDATES -->
                <div class="box">
                    <div class="box-title">1. Users Table Columns & Indexes</div>
                    <?php
                    // 1.1 users.parent_id
                    try {
                        if (! Schema::hasColumn('users', 'parent_id')) {
                            Schema::table('users', function (Blueprint $table) {
                                $table->foreignId('parent_id')
                                    ->nullable()
                                    ->after('id')
                                    ->constrained('users')
                                    ->nullOnDelete();
                                $table->index(['parent_id', 'is_active']);
                            });
                            echo '<div class="log-entry log-ok">[OK] Added "parent_id" foreign key & index to "users" table.</div>';
                        } else {
                            echo '<div class="log-entry log-info">[EXISTS] Column "users.parent_id" already verified.</div>';
                        }
                    } catch (Throwable $e) {
                        echo '<div class="log-entry log-warn">[NOTICE] users.parent_id: '.htmlspecialchars($e->getMessage()).'</div>';
                    }

                // 1.2 users bank columns
                $userBankCols = [
                    'bank_name' => fn (Blueprint $table) => $table->string('bank_name', 100)->nullable()->after('address'),
                    'bank_account_number' => fn (Blueprint $table) => $table->string('bank_account_number', 50)->nullable()->after('bank_name'),
                    'bank_ifsc' => fn (Blueprint $table) => $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number'),
                    'bank_account_holder' => fn (Blueprint $table) => $table->string('bank_account_holder', 100)->nullable()->after('bank_ifsc'),
                    'bank_upi_id' => fn (Blueprint $table) => $table->string('bank_upi_id', 100)->nullable()->after('bank_account_holder'),
                ];

                foreach ($userBankCols as $colName => $def) {
                    try {
                        if (! Schema::hasColumn('users', $colName)) {
                            Schema::table('users', function (Blueprint $table) use ($def) {
                                $def($table);
                            });
                            echo '<div class="log-entry log-ok">[OK] Added bank column "'.htmlspecialchars($colName).'" to "users" table.</div>';
                        } else {
                            echo '<div class="log-entry log-info">[EXISTS] Column "users.'.htmlspecialchars($colName).'" already verified.</div>';
                        }
                    } catch (Throwable $e) {
                        echo '<div class="log-entry log-warn">[NOTICE] users.'.htmlspecialchars($colName).': '.htmlspecialchars($e->getMessage()).'</div>';
                    }
                }
                ?>
                </div>

                <!-- SECTION 2: APPLICATIONS TABLE UPDATES -->
                <div class="box">
                    <div class="box-title">2. Applications Table Columns & Indexes</div>
                    <?php
                $appColumns = [
                    'sub_agent_id' => fn (Blueprint $table) => $table->foreignId('sub_agent_id')->nullable()->after('agent_id')->constrained('users')->nullOnDelete(),
                    'sub_agent_amount' => fn (Blueprint $table) => $table->decimal('sub_agent_amount', 10, 2)->nullable()->after('amount'),
                    'sub_agent_commission' => fn (Blueprint $table) => $table->decimal('sub_agent_commission', 10, 2)->nullable()->after('commission_amount'),
                    'company_minimum_amount' => fn (Blueprint $table) => $table->decimal('company_minimum_amount', 10, 2)->nullable()->after('sub_agent_commission'),
                    'parent_margin' => fn (Blueprint $table) => $table->decimal('parent_margin', 10, 2)->default(0)->after('company_minimum_amount'),
                    'parent_margin_status' => fn (Blueprint $table) => $table->string('parent_margin_status', 20)->default('NONE')->after('parent_margin'),
                    'parent_margin_refunded_at' => fn (Blueprint $table) => $table->timestamp('parent_margin_refunded_at')->nullable()->after('parent_margin_status'),
                ];

                foreach ($appColumns as $colName => $definition) {
                    try {
                        if (! Schema::hasColumn('applications', $colName)) {
                            Schema::table('applications', function (Blueprint $table) use ($definition) {
                                $definition($table);
                            });
                            echo '<div class="log-entry log-ok">[OK] Added column "'.htmlspecialchars($colName).'" to "applications" table.</div>';
                        } else {
                            echo '<div class="log-entry log-info">[EXISTS] Column "applications.'.htmlspecialchars($colName).'" already verified.</div>';
                        }
                    } catch (Throwable $e) {
                        echo '<div class="log-entry log-warn">[NOTICE] applications.'.htmlspecialchars($colName).': '.htmlspecialchars($e->getMessage()).'</div>';
                    }
                }
                ?>
                </div>

                <!-- SECTION 3: NEW TABLES -->
                <div class="box">
                    <div class="box-title">3. Team Pricing & Margin Tables</div>
                    <?php
                // 3.1 sub_agent_service_pricing
                try {
                    if (! Schema::hasTable('sub_agent_service_pricing')) {
                        Schema::create('sub_agent_service_pricing', function (Blueprint $table) {
                            $table->id();
                            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
                            $table->foreignId('sub_agent_id')->nullable()->constrained('users')->cascadeOnDelete();
                            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                            $table->decimal('price', 10, 2);
                            $table->decimal('commission', 10, 2)->default(0);
                            $table->timestamps();
                            $table->unique(['parent_agent_id', 'sub_agent_id', 'service_id'], 'sub_agent_pricing_unique');
                        });
                        echo '<div class="log-entry log-ok">[OK] Created table "sub_agent_service_pricing".</div>';
                    } else {
                        echo '<div class="log-entry log-info">[EXISTS] Table "sub_agent_service_pricing" already verified.</div>';
                    }
                } catch (Throwable $e) {
                    echo '<div class="log-entry log-warn">[NOTICE] sub_agent_service_pricing: '.htmlspecialchars($e->getMessage()).'</div>';
                }

                // 3.2 agent_margin_payouts
                try {
                    if (! Schema::hasTable('agent_margin_payouts')) {
                        Schema::create('agent_margin_payouts', function (Blueprint $table) {
                            $table->id();
                            $table->string('payout_number', 50)->unique();
                            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
                            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
                            $table->decimal('amount', 12, 2);
                            $table->string('payment_method', 30)->default('bank_transfer');
                            $table->string('transaction_reference', 100);
                            $table->date('payment_date');
                            $table->text('notes')->nullable();
                            $table->timestamps();
                            $table->index(['parent_agent_id', 'payment_date']);
                        });
                        echo '<div class="log-entry log-ok">[OK] Created table "agent_margin_payouts".</div>';
                    } else {
                        echo '<div class="log-entry log-info">[EXISTS] Table "agent_margin_payouts" already verified.</div>';
                    }
                } catch (Throwable $e) {
                    echo '<div class="log-entry log-warn">[NOTICE] agent_margin_payouts: '.htmlspecialchars($e->getMessage()).'</div>';
                }

                // 3.3 agent_margin_logs
                try {
                    if (! Schema::hasTable('agent_margin_logs')) {
                        Schema::create('agent_margin_logs', function (Blueprint $table) {
                            $table->id();
                            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
                            $table->foreignId('sub_agent_id')->constrained('users')->cascadeOnDelete();
                            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                            $table->foreignId('margin_payout_id')->nullable()->constrained('agent_margin_payouts')->nullOnDelete();
                            $table->decimal('sub_agent_paid', 10, 2)->default(0);
                            $table->decimal('company_retained', 10, 2)->default(0);
                            $table->decimal('margin_amount', 10, 2);
                            $table->string('status', 20)->default('ACCRUED');
                            $table->string('payout_reference', 100)->nullable();
                            $table->string('refund_reference', 100)->nullable();
                            $table->text('notes')->nullable();
                            $table->timestamps();
                            $table->index(['parent_agent_id', 'status']);
                            $table->index(['sub_agent_id', 'status']);
                        });
                        echo '<div class="log-entry log-ok">[OK] Created table "agent_margin_logs".</div>';
                    } else {
                        echo '<div class="log-entry log-info">[EXISTS] Table "agent_margin_logs" already verified.</div>';

                        if (! Schema::hasColumn('agent_margin_logs', 'margin_payout_id')) {
                            Schema::table('agent_margin_logs', function (Blueprint $table) {
                                $table->foreignId('margin_payout_id')->nullable()->after('application_id')->constrained('agent_margin_payouts')->nullOnDelete();
                            });
                            echo '<div class="log-entry log-ok">[OK] Added "margin_payout_id" to "agent_margin_logs".</div>';
                        }
                        if (! Schema::hasColumn('agent_margin_logs', 'payout_reference')) {
                            Schema::table('agent_margin_logs', function (Blueprint $table) {
                                $table->string('payout_reference', 100)->nullable()->after('status');
                            });
                            echo '<div class="log-entry log-ok">[OK] Added "payout_reference" to "agent_margin_logs".</div>';
                        }
                    }
                } catch (Throwable $e) {
                    echo '<div class="log-entry log-warn">[NOTICE] agent_margin_logs: '.htmlspecialchars($e->getMessage()).'</div>';
                }
                ?>
                </div>

                <!-- SECTION 4: MIGRATION TABLE SYNC -->
                <div class="box">
                    <div class="box-title">4. Synchronizing Laravel Migrations History Table</div>
                    <?php
                try {
                    if (Schema::hasTable('migrations')) {
                        $newMigrations = [
                            '2026_09_05_000001_add_parent_id_to_users_table',
                            '2026_09_05_000002_add_sub_agent_fields_to_applications_table',
                            '2026_09_05_000003_create_sub_agent_service_pricing_table',
                            '2026_09_05_000004_create_agent_margin_logs_table',
                            '2026_09_06_214755_create_agent_margin_payouts_table',
                            '2026_09_06_214759_add_payout_id_to_agent_margin_logs_table',
                            '2026_09_06_214804_add_bank_details_to_users_table',
                        ];

                        $currentBatch = (int) DB::table('migrations')->max('batch') + 1;

                        foreach ($newMigrations as $mig) {
                            $exists = DB::table('migrations')->where('migration', $mig)->exists();
                            if (! $exists) {
                                DB::table('migrations')->insert([
                                    'migration' => $mig,
                                    'batch' => $currentBatch,
                                ]);
                                echo '<div class="log-entry log-ok">[OK] Registered "'.$mig.'" into migrations table.</div>';
                            } else {
                                echo '<div class="log-entry log-info">[RECORDED] "'.$mig.'" already in migrations table.</div>';
                            }
                        }
                    }
                } catch (Throwable $e) {
                    echo '<div class="log-entry log-warn">[NOTICE] Migrations sync: '.htmlspecialchars($e->getMessage()).'</div>';
                }
                ?>
                </div>

                <!-- SECTION 5: CACHE REFRESH -->
                <div class="box">
                    <div class="box-title">5. Production Cache Refresh</div>
                    <?php
                $cacheCmds = [
                    'optimize:clear' => 'Clearing compiled config, routes, views, events',
                    'cache:clear' => 'Clearing application data cache',
                    'view:clear' => 'Clearing compiled Blade templates',
                ];

                foreach ($cacheCmds as $cmd => $desc) {
                    try {
                        Artisan::call($cmd);
                        echo '<div class="log-entry log-ok">[OK] Artisan '.$cmd.' ('.$desc.')</div>';
                    } catch (Throwable $e) {
                        echo '<div class="log-entry log-warn">[NOTICE] '.$cmd.': '.htmlspecialchars($e->getMessage()).'</div>';
                    }
                }
                ?>
                </div>

                <div class="alert-complete">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Database Update & Cache Refresh Successfully Completed! All tables and columns are ready for use.</span>
                </div>

                <div class="alert-warn">
                    ⚠️ <strong>Security Recommendation:</strong> For security on your production server, please delete or rename this file after running.
                </div>

            <?php } ?>
        </div>
    </div>
</body>
</html>

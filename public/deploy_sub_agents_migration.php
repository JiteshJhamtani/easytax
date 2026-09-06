<?php

/**
 * EasyTax - Production Web Migration & Cache Maintenance Runner
 *
 * IMPORTANT: Run this file by visiting:
 * https://your-domain.com/deploy_sub_agents_migration.php?token=easytax_secure_deploy_2026
 *
 * DELETE THIS FILE FROM YOUR SERVER IMMEDIATELY AFTER RUNNING!
 */
$securityToken = 'easytax_secure_deploy_2026';

// 1. Security Check
if (! isset($_GET['token']) || $_GET['token'] !== $securityToken) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>403 Forbidden</title><style>body{font-family:sans-serif;padding:50px;background:#f8fafc;color:#1e293b;}</style></head><body>';
    echo '<h2>403 Forbidden: Invalid or Missing Security Token</h2>';
    echo '<p>Pass the security token in the URL: <code>?token='.htmlspecialchars($securityToken).'</code></p>';
    echo '</body></html>';
    exit;
}

// 2. Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTax - Production Database Migration & Maintenance</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; }
        .container { max-width: 860px; margin: 0 auto; background: #1e293b; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); overflow: hidden; border: 1px solid #334155; }
        .header { background: #0f766e; padding: 24px 30px; }
        .header h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #fff; }
        .header p { margin: 6px 0 0; font-size: 0.9rem; color: #99f6e4; }
        .content { padding: 30px; }
        .step-box { background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px; }
        .step-title { font-size: 1rem; font-weight: 600; color: #38bdf8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #14532d; color: #4ade80; border: 1px solid #22c55e; }
        .badge-info { background: #1e3a8a; color: #93c5fd; border: 1px solid #3b82f6; }
        .badge-danger { background: #7f1d1d; color: #fca5a5; border: 1px solid #ef4444; }
        .log-item { font-family: monospace; font-size: 0.85rem; margin: 6px 0; color: #cbd5e1; }
        .log-item.ok { color: #4ade80; }
        .log-item.info { color: #93c5fd; }
        .log-item.err { color: #f87171; }
        .alert-warning { background: #451a03; border: 1px solid #d97706; color: #fde68a; padding: 16px 20px; border-radius: 8px; margin-top: 25px; }
        .alert-warning strong { color: #fbbf24; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>EasyTax Production Deployment Migration Tool</h1>
        <p>Database schema synchronization & cache optimization for Sub-Agent & Agency Team Management.</p>
    </div>

    <div class="content">

        <!-- STEP 1: ARTISAN MIGRATE -->
        <div class="step-box">
            <div class="step-title">
                <span>1. Standard Laravel Migration Runner (php artisan migrate --force)</span>
            </div>
            <?php
            try {
                Artisan::call('migrate', ['--force' => true]);
                $migrateOutput = Artisan::output();
                echo '<div class="log-item ok">[SUCCESS] Artisan Migrate Executed:</div>';
                echo '<pre style="background:#020617; padding:12px; border-radius:6px; font-size:0.8rem; overflow-x:auto; color:#a5f3fc;">'.htmlspecialchars($migrateOutput ?: 'No new migrations were run or already up-to-date.').'</pre>';
            } catch (Throwable $e) {
                echo '<div class="log-item err">[NOTICE] Artisan migrate reported: '.htmlspecialchars($e->getMessage()).'</div>';
                echo '<div class="log-item info">Proceeding with direct schema verification safeguards below...</div>';
            }
?>
        </div>

        <!-- STEP 2: SCHEMA VERIFICATION & SAFEGUARD APPLIER -->
        <div class="step-box">
            <div class="step-title">
                <span>2. Direct Schema Verification & Column Safeguards</span>
            </div>
            <?php
// 2.1 Check users.parent_id
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
        echo '<div class="log-item ok">[OK] Added "parent_id" column with foreign key & index to "users" table.</div>';
    } else {
        echo '<div class="log-item info">[EXISTS] Column "parent_id" already verified in "users" table.</div>';
    }
} catch (Throwable $e) {
    echo '<div class="log-item err">[ERROR] users.parent_id: '.htmlspecialchars($e->getMessage()).'</div>';
}

// 2.2 Check applications table columns
$appColumns = [
    'sub_agent_id' => function (Blueprint $table) {
        $table->foreignId('sub_agent_id')->nullable()->after('agent_id')->constrained('users')->nullOnDelete();
    },
    'sub_agent_amount' => function (Blueprint $table) {
        $table->decimal('sub_agent_amount', 10, 2)->nullable()->after('amount');
    },
    'sub_agent_commission' => function (Blueprint $table) {
        $table->decimal('sub_agent_commission', 10, 2)->nullable()->after('commission_amount');
    },
    'company_minimum_amount' => function (Blueprint $table) {
        $table->decimal('company_minimum_amount', 10, 2)->nullable()->after('sub_agent_commission');
    },
    'parent_margin' => function (Blueprint $table) {
        $table->decimal('parent_margin', 10, 2)->default(0)->after('company_minimum_amount');
    },
    'parent_margin_status' => function (Blueprint $table) {
        $table->string('parent_margin_status', 20)->default('NONE')->after('parent_margin');
    },
    'parent_margin_refunded_at' => function (Blueprint $table) {
        $table->timestamp('parent_margin_refunded_at')->nullable()->after('parent_margin_status');
    },
];

foreach ($appColumns as $colName => $definition) {
    try {
        if (! Schema::hasColumn('applications', $colName)) {
            Schema::table('applications', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
            echo '<div class="log-item ok">[OK] Added "'.htmlspecialchars($colName).'" column to "applications" table.</div>';
        } else {
            echo '<div class="log-item info">[EXISTS] Column "'.htmlspecialchars($colName).'" already verified in "applications" table.</div>';
        }
    } catch (Throwable $e) {
        echo '<div class="log-item err">[ERROR] applications.'.htmlspecialchars($colName).': '.htmlspecialchars($e->getMessage()).'</div>';
    }
}

// 2.3 Check sub_agent_service_pricing table
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
        echo '<div class="log-item ok">[OK] Created table "sub_agent_service_pricing" with foreign keys & unique index.</div>';
    } else {
        echo '<div class="log-item info">[EXISTS] Table "sub_agent_service_pricing" already verified.</div>';
    }
} catch (Throwable $e) {
    echo '<div class="log-item err">[ERROR] sub_agent_service_pricing: '.htmlspecialchars($e->getMessage()).'</div>';
}

// 2.4 Check agent_margin_logs table
try {
    if (! Schema::hasTable('agent_margin_logs')) {
        Schema::create('agent_margin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sub_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->decimal('sub_agent_paid', 10, 2);
            $table->decimal('company_retained', 10, 2);
            $table->decimal('margin_amount', 10, 2);
            $table->string('status', 20)->default('CONFIRMED');
            $table->string('refund_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['parent_agent_id', 'status']);
            $table->index(['sub_agent_id', 'status']);
        });
        echo '<div class="log-item ok">[OK] Created table "agent_margin_logs" with foreign keys & indexes.</div>';
    } else {
        echo '<div class="log-item info">[EXISTS] Table "agent_margin_logs" already verified.</div>';
    }
} catch (Throwable $e) {
    echo '<div class="log-item err">[ERROR] agent_margin_logs: '.htmlspecialchars($e->getMessage()).'</div>';
}

// 2.5 Check agent_margin_payouts table
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
        echo '<div class="log-item ok">[OK] Created table "agent_margin_payouts" with foreign keys & indexes.</div>';
    } else {
        echo '<div class="log-item info">[EXISTS] Table "agent_margin_payouts" already verified.</div>';
    }
} catch (Throwable $e) {
    echo '<div class="log-item err">[ERROR] agent_margin_payouts: '.htmlspecialchars($e->getMessage()).'</div>';
}

// 2.6 Check agent_margin_logs payout columns
try {
    if (! Schema::hasColumn('agent_margin_logs', 'margin_payout_id')) {
        Schema::table('agent_margin_logs', function (Blueprint $table) {
            $table->foreignId('margin_payout_id')
                ->nullable()
                ->after('application_id')
                ->constrained('agent_margin_payouts')
                ->nullOnDelete();
        });
        echo '<div class="log-item ok">[OK] Added "margin_payout_id" foreign key to "agent_margin_logs".</div>';
    }
    if (! Schema::hasColumn('agent_margin_logs', 'payout_reference')) {
        Schema::table('agent_margin_logs', function (Blueprint $table) {
            $table->string('payout_reference', 100)->nullable()->after('status');
        });
        echo '<div class="log-item ok">[OK] Added "payout_reference" column to "agent_margin_logs".</div>';
    }
} catch (Throwable $e) {
    echo '<div class="log-item err">[ERROR] agent_margin_logs payout columns: '.htmlspecialchars($e->getMessage()).'</div>';
}

// 2.7 Check users bank details columns
$userBankCols = [
    'bank_name' => fn (Blueprint $table) => $table->string('bank_name', 100)->nullable()->after('address'),
    'bank_account_number' => fn (Blueprint $table) => $table->string('bank_account_number', 50)->nullable()->after('bank_name'),
    'bank_ifsc' => fn (Blueprint $table) => $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number'),
    'bank_account_holder' => fn (Blueprint $table) => $table->string('bank_account_holder', 100)->nullable()->after('bank_ifsc'),
    'bank_upi_id' => fn (Blueprint $table) => $table->string('bank_upi_id', 100)->nullable()->after('bank_account_holder'),
];

foreach ($userBankCols as $col => $def) {
    try {
        if (! Schema::hasColumn('users', $col)) {
            Schema::table('users', function (Blueprint $table) use ($def) {
                $def($table);
            });
            echo '<div class="log-item ok">[OK] Added bank column "'.htmlspecialchars($col).'" to "users" table.</div>';
        } else {
            echo '<div class="log-item info">[EXISTS] Column "'.htmlspecialchars($col).'" verified in "users" table.</div>';
        }
    } catch (Throwable $e) {
        echo '<div class="log-item err">[ERROR] users.'.htmlspecialchars($col).': '.htmlspecialchars($e->getMessage()).'</div>';
    }
}
?>
        </div>

        <!-- STEP 3: CACHE REFRESH -->
        <div class="step-box">
            <div class="step-title">
                <span>3. Production Cache Refresh</span>
            </div>
            <?php
$cacheCommands = [
    'optimize:clear' => 'Clearing compiled config, routes, views, events',
    'cache:clear' => 'Clearing application cache',
    'view:clear' => 'Clearing compiled Blade templates',
];

foreach ($cacheCommands as $cmd => $desc) {
    try {
        Artisan::call($cmd);
        echo '<div class="log-item ok">[OK] Artisan '.htmlspecialchars($cmd).' ('.htmlspecialchars($desc).')</div>';
    } catch (Throwable $e) {
        echo '<div class="log-item err">[NOTICE] Artisan '.htmlspecialchars($cmd).': '.htmlspecialchars($e->getMessage()).'</div>';
    }
}
?>
        </div>

        <div class="alert-warning">
            <strong>CRITICAL SECURITY ACTION:</strong><br>
            Please delete this file (<code>public/deploy_sub_agents_migration.php</code>) from your production server immediately now that execution is complete. Leaving migration/maintenance runners publicly accessible poses a security risk.
        </div>

    </div>
</div>
</body>
</html>

<?php

// 1. Security check
if (!isset($_GET['token']) || $_GET['token'] !== 'superadmin123') {
    die('Unauthorized access.');
}

// 2. Boot up Laravel's engine
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;'>";
echo "<h2>🚀 Operator Payment System Upgrade...</h2>";

try {
    // --- STEP 1: Add 'pending_reason' to applications --- 
    echo "<h3>Step 1: Applications Table</h3>";
    try {
        if (Schema::hasTable('applications')) {
            if (!Schema::hasColumn('applications', 'pending_reason')) {
                Schema::table('applications', function (Blueprint $table) {
                    $table->text('pending_reason')->nullable()->after('status');
                });
                echo "<p style='color: green;'>✅ Successfully added 'pending_reason' column.</p>";
            } else {
                echo "<p style='color: #0056b3;'>ℹ️ 'pending_reason' column already exists.</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ 'applications' table is missing, skipping column add.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 2: Create 'operator_service_rates' table ---
    echo "<h3>Step 2: Operator Service Rates Table</h3>";
    try {
        if (!Schema::hasTable('operator_service_rates')) {
            Schema::create('operator_service_rates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operator_id');
                $table->unsignedBigInteger('service_id');
                $table->decimal('price', 10, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['operator_id', 'service_id']);
            });
            echo "<p style='color: green;'>✅ Successfully created 'operator_service_rates' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'operator_service_rates' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 3: Create 'operator_payouts' table ---
    echo "<h3>Step 3: Operator Payouts Table</h3>";
    try {
        if (!Schema::hasTable('operator_payouts')) {
            Schema::create('operator_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operator_id');
                $table->decimal('amount', 10, 2);
                $table->text('payment_note')->nullable();
                $table->timestamp('paid_at')->useCurrent();
                $table->timestamps();
            });
            echo "<p style='color: green;'>✅ Successfully created 'operator_payouts' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'operator_payouts' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 4: Add 'deleted_at' (Soft Deletes) to applications ---
    echo "<h3>Step 4: Add Soft Deletes to Applications</h3>";
    try {
        if (Schema::hasTable('applications')) {
            if (!Schema::hasColumn('applications', 'deleted_at')) {
                Schema::table('applications', function (Blueprint $table) {
                    $table->softDeletes();
                });
                echo "<p style='color: green;'>✅ Successfully added 'deleted_at' column to applications table.</p>";
            } else {
                echo "<p style='color: #0056b3;'>ℹ️ 'deleted_at' column already exists.</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ 'applications' table is missing, skipping column add.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 5: Add 'marketer_id' to users ---
    echo "<h3>Step 5: Add 'marketer_id' to Users</h3>";
    try {
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'marketer_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('marketer_id')->nullable()->constrained('users')->onDelete('set null');
                });
                echo "<p style='color: green;'>✅ Successfully added 'marketer_id' column to users table.</p>";
            } else {
                echo "<p style='color: #0056b3;'>ℹ️ 'marketer_id' column already exists.</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ 'users' table is missing, skipping column add.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 6: Add 'target_services' to coupons ---
    echo "<h3>Step 6: Add 'target_services' to Coupons</h3>";
    try {
        if (Schema::hasTable('coupons')) {
            if (!Schema::hasColumn('coupons', 'target_services')) {
                Schema::table('coupons', function (Blueprint $table) {
                    $table->json('target_services')->nullable()->after('target_agents');
                });
                echo "<p style='color: green;'>✅ Successfully added 'target_services' column to coupons table.</p>";
            } else {
                echo "<p style='color: #0056b3;'>ℹ️ 'target_services' column already exists.</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ 'coupons' table is missing, skipping column add.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 7: Create 'gifts' tables ---
    echo "<h3>Step 7: Create Gifts Tables</h3>";
    try {
        if (!Schema::hasTable('gifts')) {
            Schema::create('gifts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('period_type');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            echo "<p style='color: green;'>✅ Successfully created 'gifts' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'gifts' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    try {
        if (!Schema::hasTable('gift_condition_groups')) {
            Schema::create('gift_condition_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gift_id')->constrained()->cascadeOnDelete();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
            echo "<p style='color: green;'>✅ Successfully created 'gift_condition_groups' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'gift_condition_groups' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    try {
        if (!Schema::hasTable('gift_conditions')) {
            Schema::create('gift_conditions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gift_condition_group_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('min_count');
                $table->timestamps();
            });
            echo "<p style='color: green;'>✅ Successfully created 'gift_conditions' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'gift_conditions' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 8: Create 'leads' table ---
    echo "<h3>Step 8: Create Leads Table</h3>";
    try {
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone');
                $table->string('service_interested')->nullable();
                $table->string('source')->nullable();
                $table->string('status')->default('NEW');
                $table->text('notes')->nullable();
                $table->foreignId('marketer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
            echo "<p style='color: green;'>✅ Successfully created 'leads' table.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ 'leads' table already exists.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- STEP 9: Create Permission Tables ---
    echo "<h3>Step 9: Create Permission Tables</h3>";
    try {
        $tableNames = config('permission.table_names') ?: [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ];
        $columnNames = config('permission.column_names') ?: [
            'role_pivot_key' => 'role_id',
            'permission_pivot_key' => 'permission_id',
            'model_morph_key' => 'model_id',
            'team_foreign_key' => 'team_id',
        ];
        $teams = config('permission.teams');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (!Schema::hasTable($tableNames['permissions'])) {
            Schema::create($tableNames['permissions'], static function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });

            Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
                $table->id();
                if ($teams || config('permission.testing')) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                    $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
                }
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                if ($teams || config('permission.testing')) {
                    $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                } else {
                    $table->unique(['name', 'guard_name']);
                }
            });

            Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
                $table->unsignedBigInteger($pivotPermission);
                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
                
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key']);
                    $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
                    $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
                } else {
                    $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
                }
            });

            Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
                $table->unsignedBigInteger($pivotRole);
                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
                
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key']);
                    $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');
                    $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
                } else {
                    $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
                }
            });

            Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
                $table->unsignedBigInteger($pivotPermission);
                $table->unsignedBigInteger($pivotRole);
                $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
                $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
                $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
            });
            echo "<p style='color: green;'>✅ Successfully created permission tables.</p>";
        } else {
            echo "<p style='color: #0056b3;'>ℹ️ Permission tables already exist.</p>";
        }
    } catch (\Exception $e) { echo "<p style='color: red;'>❌ Error: ".$e->getMessage()."</p>"; }

    // --- SUCCESS MESSAGE ---
    echo "<br><div style='background: #e6f4ea; border: 1px solid #ceead6; padding: 15px; color: #137333; border-radius: 8px;'>";
    echo "<strong>🎉 Database Upgrade Complete!</strong><br>";
    echo "The database is now ready for the new Operator Payment System.";
    echo "</div>";

    echo "<br><div style='background: #fff3f3; border: 1px solid #fce8e6; padding: 15px; color: #c5221f; border-radius: 8px;'>";
    echo "<strong> CRITICAL SECURITY STEP:</strong><br>";
    echo "Please delete this file from your server after running it.";
    echo "</div>";

} catch (\Exception $e) {
    echo "<h2 style='color: #c5221f;'> Critical Error Occurred</h2>";
    echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'ancestry_path')) {
                $table->string('ancestry_path', 500)->nullable()->after('parent_id')->index();
            }
            if (! Schema::hasColumn('users', 'depth')) {
                $table->unsignedInteger('depth')->default(1)->after('ancestry_path')->index();
            }
            if (! Schema::hasColumn('users', 'can_recruit')) {
                $table->boolean('can_recruit')->default(true)->after('depth');
            }
        });

        Schema::table('agent_margin_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('agent_margin_logs', 'tier_level')) {
                $table->unsignedInteger('tier_level')->default(1)->after('sub_agent_id')->index();
            }
        });

        $indexes = collect(DB::select('SHOW INDEX FROM agent_margin_logs'))->pluck('Key_name')->all();

        Schema::table('agent_margin_logs', function (Blueprint $table) use ($indexes) {
            if (in_array('agent_margin_logs_application_id_unique', $indexes)) {
                $table->dropUnique('agent_margin_logs_application_id_unique');
            }
            if (! in_array('agent_margin_logs_app_parent_unique', $indexes)) {
                $table->unique(['application_id', 'parent_agent_id'], 'agent_margin_logs_app_parent_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'can_recruit')) {
                $table->dropColumn('can_recruit');
            }
            if (Schema::hasColumn('users', 'depth')) {
                $table->dropColumn('depth');
            }
            if (Schema::hasColumn('users', 'ancestry_path')) {
                $table->dropColumn('ancestry_path');
            }
        });

        Schema::table('agent_margin_logs', function (Blueprint $table) {
            $table->dropUnique('agent_margin_logs_app_parent_unique');
            $table->unique('application_id', 'agent_margin_logs_application_id_unique');

            if (Schema::hasColumn('agent_margin_logs', 'tier_level')) {
                $table->dropColumn('tier_level');
            }
        });
    }
};

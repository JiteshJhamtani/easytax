<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'sub_agent_id')) {
                $table->foreignId('sub_agent_id')
                    ->nullable()
                    ->after('agent_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('applications', 'sub_agent_amount')) {
                $table->decimal('sub_agent_amount', 10, 2)->nullable()->after('amount');
            }

            if (! Schema::hasColumn('applications', 'sub_agent_commission')) {
                $table->decimal('sub_agent_commission', 10, 2)->nullable()->after('commission_amount');
            }

            if (! Schema::hasColumn('applications', 'company_minimum_amount')) {
                $table->decimal('company_minimum_amount', 10, 2)->nullable()->after('sub_agent_commission');
            }

            if (! Schema::hasColumn('applications', 'parent_margin')) {
                $table->decimal('parent_margin', 10, 2)->default(0)->after('company_minimum_amount');
            }

            if (! Schema::hasColumn('applications', 'parent_margin_status')) {
                $table->string('parent_margin_status', 20)->default('NONE')->after('parent_margin');
            }

            if (! Schema::hasColumn('applications', 'parent_margin_refunded_at')) {
                $table->timestamp('parent_margin_refunded_at')->nullable()->after('parent_margin_status');
            }

            $table->index(['agent_id', 'sub_agent_id']);
            $table->index(['sub_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['agent_id', 'sub_agent_id']);
            $table->dropIndex(['sub_agent_id', 'status']);

            if (Schema::hasColumn('applications', 'sub_agent_id')) {
                $table->dropForeign(['sub_agent_id']);
                $table->dropColumn([
                    'sub_agent_id',
                    'sub_agent_amount',
                    'sub_agent_commission',
                    'company_minimum_amount',
                    'parent_margin',
                    'parent_margin_status',
                    'parent_margin_refunded_at',
                ]);
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agent_margin_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('agent_margin_logs', 'margin_payout_id')) {
                $table->foreignId('margin_payout_id')
                    ->nullable()
                    ->after('application_id')
                    ->constrained('agent_margin_payouts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('agent_margin_logs', 'payout_reference')) {
                $table->string('payout_reference', 100)->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_margin_logs', function (Blueprint $table) {
            if (Schema::hasColumn('agent_margin_logs', 'margin_payout_id')) {
                $table->dropConstrainedForeignId('margin_payout_id');
            }
            if (Schema::hasColumn('agent_margin_logs', 'payout_reference')) {
                $table->dropColumn('payout_reference');
            }
        });
    }
};

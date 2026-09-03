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
        Schema::table('applications', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('submitted_at');
            $table->index('completed_at');
            $table->index('deleted_at');
            $table->index(['agent_id', 'status']);
            $table->index(['service_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['submitted_at']);
            $table->dropIndex(['completed_at']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['agent_id', 'status']);
            $table->dropIndex(['service_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
        });
    }
};

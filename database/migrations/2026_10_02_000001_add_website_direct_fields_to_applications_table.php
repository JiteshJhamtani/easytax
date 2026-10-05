<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Allow agent_id to be nullable for direct website retail orders
            $table->unsignedBigInteger('agent_id')->nullable()->change();

            // Channel identification: 'AGENT', 'WEBSITE_DIRECT', 'VLE'
            if (! Schema::hasColumn('applications', 'source')) {
                $table->string('source', 50)->default('AGENT')->after('id')->index();
            }

            // Retail customer contact details
            if (! Schema::hasColumn('applications', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('source');
            }
            if (! Schema::hasColumn('applications', 'customer_phone')) {
                $table->string('customer_phone', 32)->nullable()->after('customer_name')->index();
            }
            if (! Schema::hasColumn('applications', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }

            // Fallback for any of the 60+ services not explicitly in the services table
            if (! Schema::hasColumn('applications', 'service_name_fallback')) {
                $table->string('service_name_fallback')->nullable()->after('service_id');
            }

            // Idempotency key (stores Drupal order ID or Razorpay payment ID) to prevent duplicates
            if (! Schema::hasColumn('applications', 'idempotency_key')) {
                $table->string('idempotency_key', 128)->nullable()->unique()->after('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'customer_name',
                'customer_phone',
                'customer_email',
                'service_name_fallback',
                'idempotency_key',
            ]);
        });
    }
};

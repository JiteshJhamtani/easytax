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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('address');
            }
            if (! Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number', 50)->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('users', 'bank_ifsc')) {
                $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number');
            }
            if (! Schema::hasColumn('users', 'bank_account_holder')) {
                $table->string('bank_account_holder', 100)->nullable()->after('bank_ifsc');
            }
            if (! Schema::hasColumn('users', 'bank_upi_id')) {
                $table->string('bank_upi_id', 100)->nullable()->after('bank_account_holder');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_holder', 'bank_upi_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('coupons', 'target_services')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->json('target_services')->nullable()->after('target_agents');
            });
        }
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('target_services');
        });
    }
};

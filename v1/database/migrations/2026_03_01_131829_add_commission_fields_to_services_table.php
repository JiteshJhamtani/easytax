<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->enum('commission_type', ['flat', 'percentage'])
                ->default('flat')
                ->after('price');

            $table->decimal('commission_value', 10, 2)
                ->default(0)
                ->after('commission_type');

        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->dropColumn(['commission_type', 'commission_value']);

        });
    }
};

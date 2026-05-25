<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_gift_conditions_table.php
    public function up(): void
    {
        if (!Schema::hasTable('gift_conditions')) {
            try {
                Schema::create('gift_conditions', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('gift_condition_group_id')->constrained()->cascadeOnDelete();
                    $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                    $table->unsignedInteger('min_count');
                    $table->timestamps();
                });
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_conditions');
    }
};

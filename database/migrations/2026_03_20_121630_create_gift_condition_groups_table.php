<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_gift_condition_groups_table.php
    public function up(): void
    {
        if (!Schema::hasTable('gift_condition_groups')) {
            try {
                Schema::create('gift_condition_groups', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('gift_id')->constrained()->cascadeOnDelete();
                    $table->integer('sort_order')->default(0);
                    $table->timestamps();
                });
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) throw $e;
            }
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_condition_groups');
    }
};

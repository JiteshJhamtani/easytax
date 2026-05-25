<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_gifts_table.php
    public function up(): void
    {
        if (!Schema::hasTable('gifts')) {
            try {
                Schema::create('gifts', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->string('period_type'); // 'monthly', 'quarterly', 'yearly'
                    $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('gifts');
    }
};

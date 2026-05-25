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
        if (!Schema::hasTable('application_logs')) {
            try {
                Schema::create('application_logs', function (Blueprint $table) {

                    $table->id();

                    $table->foreignId('application_id')
                        ->constrained()
                        ->cascadeOnDelete();

                    $table->foreignId('user_id')
                        ->nullable()
                        ->constrained()
                        ->nullOnDelete();

                    $table->string('event');

                    $table->json('meta')->nullable();

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
        Schema::dropIfExists('application_logs');
    }
};

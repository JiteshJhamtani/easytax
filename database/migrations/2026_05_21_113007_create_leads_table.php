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
        if (! Schema::hasTable('leads')) {
            try {
                Schema::create('leads', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->nullable();
                    $table->string('phone');
                    $table->string('service_interested')->nullable();
                    $table->string('source')->nullable();
                    $table->string('status')->default('NEW');
                    $table->text('notes')->nullable();
                    $table->foreignId('marketer_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('leads');
    }
};

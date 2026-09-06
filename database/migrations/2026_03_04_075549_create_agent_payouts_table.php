<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('agent_payouts')) {
            try {
                Schema::create('agent_payouts', function (Blueprint $table) {
                    $table->id();

                    $table->foreignId('agent_id')
                        ->constrained('users')
                        ->cascadeOnDelete();

                    $table->decimal('amount', 12, 2);

                    $table->date('period_start');
                    $table->date('period_end');
                    $table->enum('status', ['pending', 'paid'])->default('pending');
                    $table->timestamp('paid_at')->nullable();

                    $table->text('notes')->nullable();

                    $table->timestamps();
                });
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payouts');
    }
};

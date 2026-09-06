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
        if (! Schema::hasTable('payment_logs')) {
            try {
                Schema::create('payment_logs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('application_id')->constrained()->cascadeOnDelete();
                    $table->string('transaction_id')->index();
                    $table->string('event')->nullable(); // initiated, webhook, retry
                    $table->string('status')->nullable(); // SUCCESS, FAILED
                    $table->json('payload')->nullable();
                    $table->json('response')->nullable();
                    $table->text('error')->nullable();
                    $table->timestamps();
                });
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};

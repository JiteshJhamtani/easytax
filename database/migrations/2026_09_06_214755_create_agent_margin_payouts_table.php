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
        if (! Schema::hasTable('agent_margin_payouts')) {
            Schema::create('agent_margin_payouts', function (Blueprint $table) {
                $table->id();
                $table->string('payout_number', 50)->unique();
                $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->string('payment_method', 30)->default('bank_transfer');
                $table->string('transaction_reference', 100);
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['parent_agent_id', 'payment_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_margin_payouts');
    }
};

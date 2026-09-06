<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_margin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sub_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->decimal('sub_agent_paid', 10, 2);
            $table->decimal('company_retained', 10, 2);
            $table->decimal('margin_amount', 10, 2);
            $table->string('status', 20)->default('CONFIRMED');
            $table->string('refund_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['parent_agent_id', 'status']);
            $table->index(['sub_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_margin_logs');
    }
};

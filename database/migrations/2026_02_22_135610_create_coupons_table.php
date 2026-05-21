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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            
            // Legacy service linkage
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            
            $table->integer('global_max_uses')->nullable();
            $table->integer('total_used')->default(0);
            $table->integer('max_uses_per_agent')->nullable();
            
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('target_agents')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

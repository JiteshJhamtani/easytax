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
        if (!Schema::hasTable('coupons')) {
            try {
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
            } catch (\Exception $e) {
                // Ignore if the database engine throws a 1050 table exists error despite hasTable returning false
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
        Schema::dropIfExists('coupons');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_agent_service_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sub_agent_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['parent_agent_id', 'sub_agent_id', 'service_id'], 'sub_agent_pricing_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_agent_service_pricing');
    }
};

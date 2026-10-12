<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('applications', 'agent_notes')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->text('agent_notes')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('applications', 'agent_notes')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('agent_notes');
            });
        }
    }
};

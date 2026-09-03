<?php

use App\Models\Application;
use App\Services\SessionResolver;
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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('session_label', 20)->nullable()->after('payout_id');
            $table->index('session_label');
        });

        // Backfill existing data
        Application::chunk(500, function ($applications) {
            foreach ($applications as $application) {
                // Use submitted_at if available, otherwise started_at, or fallback to created_at
                $date = $application->submitted_at ?? $application->started_at ?? $application->created_at;

                if ($date) {
                    $sessionData = SessionResolver::forDate($date);
                    $application->session_label = $sessionData['label'];
                    // Use silent update to avoid triggering observers/updated_at
                    Application::where('id', $application->id)->update(['session_label' => $sessionData['label']]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['session_label']);
            $table->dropColumn('session_label');
        });
    }
};

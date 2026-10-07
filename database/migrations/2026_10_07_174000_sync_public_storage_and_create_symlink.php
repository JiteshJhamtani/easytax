<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Synchronize storage files and ensure public storage accessibility on production
     * without requiring manual SSH terminal commands.
     */
    public function up(): void
    {
        $appPublic = storage_path('app/public');
        $publicStorage = public_path('storage');

        if (! file_exists($appPublic)) {
            @mkdir($appPublic, 0755, true);
        }

        // 1. If public/storage is an actual directory (not a symlink), sync legacy files
        if (file_exists($publicStorage) && ! is_link($publicStorage) && is_dir($publicStorage)) {
            // Copy any files from public/storage to storage/app/public
            try {
                File::copyDirectory($publicStorage, $appPublic);
            } catch (Throwable $e) {
                // Ignore copy errors if files already exist
            }

            // Copy all storage/app/public files back into public/storage so web servers can serve them
            try {
                File::copyDirectory($appPublic, $publicStorage);
            } catch (Throwable $e) {
                // Ignore
            }

            // Attempt to replace directory with symlink if operating system allows
            try {
                $tempBackup = public_path('storage_legacy_backup_'.time());
                if (@rename($publicStorage, $tempBackup)) {
                    if (@symlink($appPublic, $publicStorage)) {
                        // Successfully created symlink, remove temporary backup
                        File::deleteDirectory($tempBackup);
                    } else {
                        // Symlink not permitted, restore directory
                        @rename($tempBackup, $publicStorage);
                    }
                }
            } catch (Throwable $e) {
                // If symlink creation fails, public/storage remains a synced directory
            }
        } elseif (! file_exists($publicStorage)) {
            // Attempt to create symlink if it doesn't exist
            try {
                @symlink($appPublic, $publicStorage);
            } catch (Throwable $e) {
                // Ignore if not allowed
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
};

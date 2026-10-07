<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageMediaController extends Controller
{
    /**
     * Serve public storage files directly when the web server forwards /storage/{path} to Laravel.
     * This acts as an automated fallback when public/storage is not a symlink on the production server.
     */
    public function show(string $path): BinaryFileResponse
    {
        // Prevent path traversal attacks
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(404);
        }

        $realPath = storage_path('app/public/'.$path);

        if (! file_exists($realPath) || is_dir($realPath)) {
            // Also check if it exists in public/storage
            $fallback = public_path('storage/'.$path);
            if (file_exists($fallback) && ! is_dir($fallback)) {
                $realPath = $fallback;
            } else {
                abort(404);
            }
        }

        $mimeType = mime_content_type($realPath) ?: 'application/octet-stream';

        return response()->file($realPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}

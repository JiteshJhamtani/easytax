<?php

namespace App\Services;

use App\Models\ApplicationLog;
use Illuminate\Support\Facades\Auth;

class ApplicationLogger
{
    public static function log($applicationId, $event, $meta = [])
    {
        ApplicationLog::create([
            'application_id' => $applicationId,
            'user_id' => Auth::id(),
            'event' => $event,
            'meta' => $meta,
        ]);
    }
}

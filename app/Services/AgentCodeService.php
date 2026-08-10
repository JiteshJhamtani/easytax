<?php

namespace App\Services;

use App\Models\User;

class AgentCodeService
{
    public static function generate(): string
    {
        // 1. Find the most recently created agent
        $lastAgent = User::whereNotNull('agent_code')
            ->where('agent_code', 'like', 'AGT-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastAgent
            ? ((int) substr($lastAgent->agent_code, 4)) + 1
            : 1;

        // 2. ✅ Loop until we find a code that doesn't exist yet
        //    Handles gaps caused by sync importing agents from other servers
        do {
            $code = 'AGT-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (User::where('agent_code', $code)->exists());

        return $code;
    }
}

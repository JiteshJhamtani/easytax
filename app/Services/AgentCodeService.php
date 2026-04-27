<?php

namespace App\Services;

use App\Models\User;

class AgentCodeService
{
    public static function generate(): string
    {
        // 1. Find the absolute highest agent code in the entire database
        // We order by the code itself (descending) to ensure we always grab the biggest number.
        $lastAgent = User::whereNotNull('agent_code')
            ->where('agent_code', 'like', 'AGT-%')
            ->orderBy('agent_code', 'desc')
            ->first();

        // 2. Extract the number and add 1 
        $nextNumber = $lastAgent
            ? ((int) substr($lastAgent->agent_code, 4)) + 1
            : 1;

        // 3. Format it back to AGT-00000X and return it safely
        return 'AGT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
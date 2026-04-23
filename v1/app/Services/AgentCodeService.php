<?php

namespace App\Services;

use App\Models\User;

class AgentCodeService
{

    public static function generate(): string
    {
        $last = User::where('role', 'agent')
            ->orderByDesc('id')
            ->first();

        $nextNumber = $last
            ? ((int) substr($last->agent_code, 4)) + 1
            : 1;

        return 'AGT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

}

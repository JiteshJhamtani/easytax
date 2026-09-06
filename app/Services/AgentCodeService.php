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

    public static function generateSubAgentCode(User $parentAgent): string
    {
        $parentCode = $parentAgent->agent_code;
        if (empty($parentCode)) {
            $parentCode = self::generate();
            $parentAgent->update(['agent_code' => $parentCode]);
        }

        // Find existing sub-agents under this parent
        $existingCodes = User::where('parent_id', $parentAgent->id)
            ->where('agent_code', 'like', "{$parentCode}-%")
            ->pluck('agent_code')
            ->toArray();

        $maxSuffix = 0;
        foreach ($existingCodes as $existingCode) {
            $parts = explode('-', $existingCode);
            $suffix = end($parts);
            if (is_numeric($suffix)) {
                $maxSuffix = max($maxSuffix, (int) $suffix);
            }
        }

        $nextSuffix = $maxSuffix + 1;

        do {
            $code = sprintf('%s-%02d', $parentCode, $nextSuffix);
            $nextSuffix++;
        } while (User::where('agent_code', $code)->exists());

        return $code;
    }
}

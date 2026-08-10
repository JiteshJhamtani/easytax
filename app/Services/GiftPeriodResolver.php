<?php

// app/Services/GiftPeriodResolver.php

namespace App\Services;

use Carbon\Carbon;

class GiftPeriodResolver
{
    public function resolve(string $periodType, ?int $year = null, ?int $quarter = null, ?int $month = null): array
    {
        $year ??= now()->year;

        return match ($periodType) {
            'monthly' => $this->monthly($year, $month ?? now()->month),
            'quarterly' => $this->quarterly($year, $quarter ?? now()->quarter),
            'yearly' => $this->yearly($year),
            default => $this->yearly($year),
        };
    }

    private function monthly(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();

        return [$start, $start->copy()->endOfMonth()];
    }

    private function quarterly(int $year, int $quarter): array
    {
        $start = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();

        return [$start, $start->copy()->addMonths(3)->subDay()->endOfDay()];
    }

    private function yearly(int $year): array
    {
        return [
            Carbon::create($year, 1, 1)->startOfDay(),
            Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }
}

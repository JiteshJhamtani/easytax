<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SessionResolver
{
    /**
     * Get the session data for the current date.
     */
    public static function current(): array
    {
        return self::forDate(now());
    }

    /**
     * Determine the session label and bounds for a given date.
     */
    public static function forDate(Carbon|string $date): array
    {
        $date = Carbon::parse($date);
        $month = $date->month;
        $year = $date->year;

        // Cycle runs from Sep 1 to Aug 31
        // S1: Sep 1 to Mar 31
        // S2: Apr 1 to Aug 31

        if ($month >= 9) {
            $startYear = $year;
            $session = 'S1';
            $from = Carbon::create($startYear, 9, 1)->startOfDay();
            $to = Carbon::create($startYear + 1, 3, 31)->endOfDay();
        } elseif ($month <= 3) {
            $startYear = $year - 1;
            $session = 'S1';
            $from = Carbon::create($startYear, 9, 1)->startOfDay();
            $to = Carbon::create($startYear + 1, 3, 31)->endOfDay();
        } else {
            // month is 4 to 8
            $startYear = $year - 1;
            $session = 'S2';
            $from = Carbon::create($startYear + 1, 4, 1)->startOfDay();
            $to = Carbon::create($startYear + 1, 8, 31)->endOfDay();
        }

        $nextYearShort = substr((string) ($startYear + 1), -2);
        $label = "{$startYear}-{$nextYearShort} {$session}";

        return [
            'label' => $label,
            'session' => $session,
            'start_year' => $startYear,
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Generate a list of all sessions from the configured start year up to the current session.
     */
    public static function all(): Collection
    {
        $startYear = config('easytax_session.start_year', 2024);
        $current = self::current();
        $currentStartYear = $current['start_year'];
        $currentSession = $current['session'];

        $sessions = collect();

        for ($y = $startYear; $y <= $currentStartYear; $y++) {
            $nextYearShort = substr((string) ($y + 1), -2);

            // Add S1
            $sessions->push([
                'label' => "{$y}-{$nextYearShort} S1",
                'name' => "{$y}-{$nextYearShort} Session 1 (Sep-Mar)",
                'from' => Carbon::create($y, 9, 1)->startOfDay(),
                'to' => Carbon::create($y + 1, 3, 31)->endOfDay(),
            ]);

            // Add S2 if we haven't exceeded the current session
            if ($y < $currentStartYear || ($y === $currentStartYear && $currentSession === 'S2')) {
                $sessions->push([
                    'label' => "{$y}-{$nextYearShort} S2",
                    'name' => "{$y}-{$nextYearShort} Session 2 (Apr-Aug)",
                    'from' => Carbon::create($y + 1, 4, 1)->startOfDay(),
                    'to' => Carbon::create($y + 1, 8, 31)->endOfDay(),
                ]);
            }
        }

        return $sessions->reverse()->values(); // Newest first
    }

    /**
     * Resolve a session label back to its date bounds.
     */
    public static function fromLabel(string $label): ?array
    {
        $sessions = self::all();
        $session = $sessions->firstWhere('label', $label);

        if (! $session) {
            return null;
        }

        return [
            'label' => $session['label'],
            'from' => $session['from'],
            'to' => $session['to'],
        ];
    }
}

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
        $label = trim($label);
        $sessions = self::all();
        $session = $sessions->firstWhere('label', $label);

        if ($session) {
            return [
                'label' => $session['label'],
                'from' => $session['from'],
                'to' => $session['to'],
            ];
        }

        // Dynamic fallback regex: parses any valid session format (e.g. "2024-25 S1", "2025-26 S2")
        if (preg_match('/^(\d{4})-(\d{2})\s+(S[12])$/i', $label, $matches)) {
            $y = (int) $matches[1];
            $s = strtoupper($matches[3]);

            if ($s === 'S1') {
                return [
                    'label' => "{$y}-{$matches[2]} S1",
                    'from' => Carbon::create($y, 9, 1)->startOfDay(),
                    'to' => Carbon::create($y + 1, 3, 31)->endOfDay(),
                ];
            } else {
                return [
                    'label' => "{$y}-{$matches[2]} S2",
                    'from' => Carbon::create($y + 1, 4, 1)->startOfDay(),
                    'to' => Carbon::create($y + 1, 8, 31)->endOfDay(),
                ];
            }
        }

        return null;
    }

    /**
     * Get the active session label.
     * Checks requested parameter/query/input, saves to HTTP session, or retrieves from HTTP session, or defaults to current.
     */
    public static function activeSessionLabel(?string $requestedLabel = null): string
    {
        $label = $requestedLabel;

        if ((empty($label) || !is_string($label)) && function_exists('request') && request()->has('session')) {
            $label = request()->input('session');
        }

        // If a specific session was passed/requested and is valid
        if (!empty($label) && is_string($label) && trim($label) !== '' && $label !== 'null') {
            $matched = self::fromLabel(trim($label));
            if ($matched) {
                if (function_exists('session') && request()->hasSession()) {
                    session()->put('easytax_active_session', $matched['label']);
                }
                return $matched['label'];
            }
        }

        // Otherwise check what was previously saved in the user's browser session
        if (function_exists('session') && request()->hasSession() && session()->has('easytax_active_session')) {
            $saved = session()->get('easytax_active_session');
            if (is_string($saved) && self::fromLabel($saved)) {
                return $saved;
            }
        }

        // Fallback to real-time current session
        return self::current()['label'];
    }

    /**
     * Get the active session details (bounds, label, etc.)
     */
    public static function active(?string $requestedLabel = null): array
    {
        $label = self::activeSessionLabel($requestedLabel);

        return self::fromLabel($label) ?? self::current();
    }
}

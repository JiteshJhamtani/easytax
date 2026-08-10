<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('money')) {
    function money($amount)
    {
        $symbol = setting('currency_symbol', '₹');
        $position = setting('currency_position', 'before');

        $formatted = number_format((float) $amount, 2);

        return $position === 'before'
            ? $symbol.$formatted
            : $formatted.$symbol;
    }
}

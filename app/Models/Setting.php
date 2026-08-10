<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted()
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    public static function allCached(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, $default = null)
    {
        $settings = static::allCached();

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function clearCache(): void
    {
        Cache::forget('app_settings');
    }
}

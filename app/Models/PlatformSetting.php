<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $table = 'platform_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("platform_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set setting value
     */
    public static function set($key, $value, $type = 'string')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
            ]
        );

        Cache::forget("platform_setting_{$key}");
        Cache::forget('all_platform_settings');

        return $setting;
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings()
    {
        return Cache::remember('all_platform_settings', 3600, function () {
            $settings = self::query()->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }
}

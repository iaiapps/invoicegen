<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'setting_key',
        'setting_value',
    ];

    /**
     * Get setting value by key for specific user
     */
    public static function getValue($userId, $key, $default = null)
    {
        return Cache::remember("user_setting_{$userId}_{$key}", 3600, function () use ($userId, $key, $default) {
            $setting = self::where('user_id', $userId)
                ->where('setting_key', $key)
                ->first();

            return $setting ? $setting->setting_value : $default;
        });
    }

    /**
     * Set setting value for specific user
     */
    public static function setValue($userId, $key, $value)
    {
        $setting = self::updateOrCreate(
            ['user_id' => $userId, 'setting_key' => $key],
            ['setting_value' => is_array($value) ? json_encode($value) : $value]
        );

        Cache::forget("user_setting_{$userId}_{$key}");
        Cache::forget("user_all_settings_{$userId}");

        return $setting;
    }

    /**
     * Get all settings for specific user as array
     */
    public static function getAllForUser($userId)
    {
        return Cache::remember("user_all_settings_{$userId}", 3600, function () use ($userId) {
            $settings = self::where('user_id', $userId)->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->setting_key] = $setting->setting_value;
            }

            return $result;
        });
    }

    /**
     * Relationship to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

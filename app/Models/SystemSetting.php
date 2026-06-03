<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return cache()->remember("system_setting_{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("system_setting_{$key}");
    }
}

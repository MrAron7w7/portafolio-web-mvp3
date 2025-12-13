<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Obtener un setting por su key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Establecer un setting
     */
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    /**
     * Obtener todos los settings agrupados
     */
    public static function getAllGrouped()
    {
        return static::all()->groupBy('group');
    }

    /**
     * Obtener settings como array key => value
     */
    public static function getAllAsArray()
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
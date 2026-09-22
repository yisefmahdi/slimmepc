<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key): mixed
    {
        return static::where('key', $key)->value('value');
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

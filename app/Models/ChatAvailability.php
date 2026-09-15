<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAvailability extends Model
{
    protected $table = 'chat_availability';

    protected $fillable = [
        'day_of_week',
        'is_open',
        'open_at',
        'close_at',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    public static function dayName(int $day): string
    {
        return ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'][$day] ?? '';
    }
}

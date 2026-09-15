<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatClosedDate extends Model
{
    protected $fillable = [
        'closed_at',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'date',
        ];
    }
}

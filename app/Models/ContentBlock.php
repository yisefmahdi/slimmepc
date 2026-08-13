<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'page',
        'section',
        'block_key',
        'type',
        'value',
        'json_value',
        'sort_order',
    ];

    protected $casts = [
        'json_value' => 'array',
    ];
}


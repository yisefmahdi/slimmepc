<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPushLog extends Model
{
    protected $fillable = [
        'type',
        'ref_id',
        'title',
        'url',
        'targeted',
        'delivered',
        'response',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassEmail extends Model
{
    protected $fillable = [
        'message_type',
        'message_content',
    ];
}

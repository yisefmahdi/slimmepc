<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model
{
    protected $table = 'content_meta';

    protected $fillable = ['meta_key', 'meta_value'];
}

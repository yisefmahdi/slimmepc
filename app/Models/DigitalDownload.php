<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalDownload extends Model
{
    protected $fillable = [
        'digital_file_id',
        'order_id',
        'user_id',
        'ip',
    ];

    public function file()
    {
        return $this->belongsTo(DigitalFile::class, 'digital_file_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

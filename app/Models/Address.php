<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'street', 'house_number',
        'addition', 'postcode', 'city', 'country', 'phone', 'email', 'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function fullStreet(): string
    {
        return trim($this->street.' '.$this->house_number.($this->addition ? ' '.$this->addition : ''));
    }
}

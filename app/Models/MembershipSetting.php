<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipSetting extends Model
{
    protected $fillable = ['subscription_price'];

    /**
     * The single membership price (incl. VAT). Falls back to 0 when not set yet.
     */
    public static function price(): float
    {
        return (float) (static::first()?->subscription_price ?? 0);
    }
}

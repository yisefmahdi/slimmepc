<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'free_above', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'free_above' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function costFor(float $amountAfterDiscount): float
    {
        if ($this->slug === 'pickup') {
            return 0.00;
        }
        if ($this->free_above !== null && $amountAfterDiscount >= (float) $this->free_above) {
            return 0.00;
        }

        return round((float) $this->price, 2);
    }
}

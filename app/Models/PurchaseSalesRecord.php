<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseSalesRecord extends Model
{
    protected $fillable = [
        'product_name',
        'supplier_name',
        'purchase_date',
        'purchase_price',
        'customer_name',
        'sale_date',
        'sale_price',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function getProfitAttribute()
    {
        if ($this->sale_price !== null) {
            return $this->sale_price - $this->purchase_price;
        }

        return null;
    }
}

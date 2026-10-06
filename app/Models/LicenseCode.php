<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseCode extends Model
{
    protected $fillable = [
        'product_id',
        'code',
        'status',
        'order_id',
        'order_item_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}

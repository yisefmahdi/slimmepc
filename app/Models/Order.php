<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'billing_address_id', 'shipping_address_id',
        'cart_id', 'klantnummer', 'customer_email', 'customer_phone',
        'subtotal', 'tax_percentage', 'tax_amount',
        'discount_code', 'coupon_id', 'discount_amount',
        'shipping_method', 'shipping_cost', 'total_price',
        'payment_status', 'payment_method', 'mollie_payment_id', 'order_status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                do {
                    $candidate = 'ORD-'.strtoupper(Str::random(8));
                } while (self::where('order_number', $candidate)->exists());
                $order->order_number = $candidate;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function invoice()
    {
        return $this->hasOne(OrderInvoice::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}

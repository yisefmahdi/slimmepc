<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'discount_type', 'discount_value', 'min_amount',
        'start_date', 'end_date', 'status', 'usage_limit', 'used_count', 'is_single_use',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
        'is_single_use' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Coupon $coupon) {
            $coupon->code = Str::upper(trim($coupon->code));
        });
        static::updating(function (Coupon $coupon) {
            if ($coupon->isDirty('code')) {
                $coupon->code = Str::upper(trim($coupon->code));
            }
        });
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isExpired(): bool
    {
        if ($this->end_date && now()->gt($this->end_date)) return true;
        if ($this->start_date && now()->lt($this->start_date)) return true;
        return false;
    }

    public function isMaxedOut(): bool
    {
        if ($this->usage_limit === null) return false;
        return $this->used_count >= $this->usage_limit;
    }

    public function isActive(): bool
    {
        return $this->status && !$this->isExpired() && !$this->isMaxedOut();
    }

    public function discountAmount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return round($subtotal * (float) $this->discount_value / 100, 2);
        }
        return min((float) $this->discount_value, $subtotal);
    }
}

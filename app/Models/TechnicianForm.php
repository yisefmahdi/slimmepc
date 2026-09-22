<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TechnicianForm extends Model
{
    protected $fillable = [
        'user_id',
        'technician_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'quarter_count',
        'quarter_price',
        'travel_cost',
        'subtotal',
        'btw',
        'total',
        'payment_status',
        'description',
        'work_done',
        'advice',
        'rating',
        'comment',
        'member_discount',
        'coupon_id',
        'coupon_discount',
        'mollie_payment_id',
        'payment_method',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function technicianInvoice(): HasOne
    {
        return $this->hasOne(TechnicianInvoice::class, 'technician_form_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}

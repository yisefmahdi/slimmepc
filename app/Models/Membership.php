<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'klantnummer',
        'customer_type',
        'customer_gender',
        'name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'postcode',
        'city',
        'start_date',
        'end_date',
        'total',
        'payment_status',
        'payment_method',
        'mollie_payment_id',
        'terms_accepted',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'terms_accepted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(MembershipInvoice::class);
    }

    public function isActive(): bool
    {
        return $this->payment_status === 'paid'
            && $this->end_date
            && $this->end_date->isFuture();
    }
}

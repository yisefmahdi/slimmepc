<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipInvoice extends Model
{
    protected $fillable = [
        'membership_id',
        'klantnummer',
        'invoice_number',
        'invoice_date',
        'payment_method',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'pdf_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}

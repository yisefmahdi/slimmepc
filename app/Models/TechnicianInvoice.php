<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TechnicianInvoice extends Model
{
    protected $fillable = [
        'technician_form_id',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'btw',
        'total',
        'status',
        'pdf_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(TechnicianForm::class, 'technician_form_id');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (TechnicianInvoice $invoice) {
            if (empty($invoice->invoice_number)) {
                // Random + retry loop (uniqid() is time-based/predictable and
                // can collide under concurrency); the DB unique index is the
                // final guard.
                do {
                    $invoice->invoice_number = 'SLP-'.strtoupper(Str::random(8));
                } while (self::where('invoice_number', $invoice->invoice_number)->exists());
            }
        });
    }
}

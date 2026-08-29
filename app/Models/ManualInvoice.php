<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualInvoice extends Model
{
    protected $fillable = [
        'name',
        'email',
        'invoice_number',
        'device_info',
        'description',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_percentage' => 'integer',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

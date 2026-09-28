<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaptopLoanPhoto extends Model
{
    protected $fillable = [
        'laptop_loan_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LaptopLoan::class, 'laptop_loan_id');
    }
}

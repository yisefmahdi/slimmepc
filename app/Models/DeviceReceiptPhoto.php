<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceReceiptPhoto extends Model
{
    protected $fillable = [
        'device_receipt_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(DeviceReceipt::class, 'device_receipt_id');
    }
}

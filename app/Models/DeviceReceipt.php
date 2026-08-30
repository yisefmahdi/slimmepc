<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceReceipt extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'device_type',
        'phone_number',
        'serial_number',
        'received_at',
        'notes',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function receiptNumber(): string
    {
        return 'DR-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}

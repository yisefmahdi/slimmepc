<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function photos(): HasMany
    {
        return $this->hasMany(DeviceReceiptPhoto::class, 'device_receipt_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Streaming URLs voor admin preview (disk local, zelfde als repair inbox).
     *
     * @return array<int, array{photo_id: int, url: string}>
     */
    public function photoUrls(): array
    {
        return $this->photos->map(fn (DeviceReceiptPhoto $photo) => [
            'photo_id' => $photo->id,
            'url' => route('admin.bevestiging-mail.ontvangst.photo', [
                'receipt' => $this->id,
                'photo' => $photo->id,
            ]),
        ])->all();
    }
}

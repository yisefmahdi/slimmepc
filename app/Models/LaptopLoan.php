<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaptopLoan extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'phone',
        'address',
        'postcode',
        'city',
        'repair_number',
        'laptop_type',
        'given_at',
        'status',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'given_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function loanNumber(): string
    {
        return 'LL-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LaptopLoanPhoto::class, 'laptop_loan_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Streaming URLs voor admin preview (disk local, zelfde als ontvangst).
     *
     * @return array<int, array{photo_id: int, url: string}>
     */
    public function photoUrls(): array
    {
        return $this->photos->map(fn (LaptopLoanPhoto $photo) => [
            'photo_id' => $photo->id,
            'url' => route('admin.leen-huur.photo', [
                'loan' => $this->id,
                'photo' => $photo->id,
            ]),
        ])->all();
    }
}

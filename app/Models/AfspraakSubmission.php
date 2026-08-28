<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfspraakSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'afspraak_number',
        'name',
        'email',
        'street',
        'phone',
        'postcode',
        'house_number',
        'city',
        'device',
        'problem',
        'preferred_date',
        'preferred_time',
        'status',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'preferred_date' => 'date',
        ];
    }

    /**
     * Scope: only unseen (new) submissions.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}

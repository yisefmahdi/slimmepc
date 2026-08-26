<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'repair_number',
        'device',
        'problems',
        'description',
        'brand',
        'model',
        'serial',
        'data_importance',
        'opened_before',
        'name',
        'email',
        'phone',
        'postcode',
        'delivery_method',
        'contact_preference',
        'privacy',
        'photos',
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
            'privacy' => 'boolean',
            'problems' => 'array',
            'photos' => 'array',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'request_type',
        'message',
        'attachment',
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
        ];
    }

    /**
     * All messages of the chat thread (customer + admin).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class)->orderBy('created_at');
    }

    /**
     * The most recent reply in the thread.
     */
    public function latestReply(): HasOne
    {
        return $this->hasOne(ContactReply::class)->latestOfMany();
    }

    /**
     * Scope: only unseen (new) submissions.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * The absolute path of the stored attachment (if any).
     */
    public function attachmentPath(): ?string
    {
        if (! $this->attachment) {
            return null;
        }

        $path = storage_path('app/private/contact/'.$this->id.'/'.$this->attachment);

        return is_file($path) ? $path : null;
    }
}
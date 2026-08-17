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
        'admin_read_at',
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
            'admin_read_at' => 'datetime',
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
     * How many customer-side messages the admin has not seen yet.
     *
     * `admin_read_at` = "everything up to this moment was seen". Any customer
     * reply created after it (or the original message, if the thread was never
     * opened) counts as unread.
     */
    public function unreadCount(): int
    {
        $since = $this->admin_read_at;

        $customerReplies = $this->replies
            ->where('sender', 'customer')
            ->when($since, fn ($collection) => $collection->filter(
                fn (ContactReply $reply) => $reply->created_at->gt($since)
            ))
            ->count();

        $originalUnread = ($since && $this->created_at->lte($since)) ? 0 : 1;

        return $customerReplies + $originalUnread;
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
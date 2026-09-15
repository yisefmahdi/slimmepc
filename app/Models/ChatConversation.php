<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'guest_token',
        'user_id',
        'name',
        'email',
        'status',
        'ai_enabled',
        'handed_over_at',
        'admin_read_at',
        'last_activity_at',
        'rating',
        'rating_comment',
        'rated_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'ai_enabled' => 'boolean',
            'handed_over_at' => 'datetime',
            'admin_read_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'rated_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_conversation_id')->orderBy('id');
    }

    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Ongelezen klantberichten voor de admin (zelfde logica als ContactSubmission).
     */
    public function unreadCount(): int
    {
        $since = $this->admin_read_at;

        return (int) $this->messages()
            ->where('sender', 'customer')
            ->when($since, fn ($q) => $q->where('created_at', '>', $since))
            ->count();
    }
}

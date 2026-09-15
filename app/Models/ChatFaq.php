<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatFaq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'keywords',
        'category',
        'is_active',
        'sort_order',
        'embedding',
        'embedding_model',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'embedding' => 'array',
        ];
    }

    /**
     * Tekst die wordt embedded (vraag + antwoord + keywords).
     */
    public function embeddableText(): string
    {
        return trim($this->question."\n".$this->answer."\n".($this->keywords ?? ''));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

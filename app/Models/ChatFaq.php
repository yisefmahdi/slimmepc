<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatFaq extends Model
{
    protected $fillable = [
        'question',
        'answer',
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
     * Tekst die wordt embedded (vraag + antwoord). Puur semantisch —
     * geen keywords: de agent begrijpt betekenis, geen woordenlijsten.
     */
    public function embeddableText(): string
    {
        return trim($this->question."\n".$this->answer);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

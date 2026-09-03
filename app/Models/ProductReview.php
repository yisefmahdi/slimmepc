<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'guest_name', 'guest_email', 'rating', 'title', 'body', 'is_approved', 'ip_address',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->guest_name) return $this->guest_name;
        if ($this->user) return $this->user->name;
        return 'Gast';
    }
}

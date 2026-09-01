<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'title', 'brand', 'sku', 'price', 'old_price', 'stock_status', 'status', 'is_featured',
        'description', 'features', 'colors', 'sizes', 'main_image', 'gallery_images',
        'external_link', 'delivery_time', 'slug', 'discount_type', 'discount_value', 'discount_start_date', 'discount_end_date',
        'download_32bit_url', 'download_64bit_url', 'manual_url',
    ];

    protected $casts = [
        'features' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'gallery_images' => 'array',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public function getDiscountedPriceAttribute(): float
    {
        if (!$this->discount_type || !$this->discount_value || !$this->discount_start_date || !$this->discount_end_date) {
            return (float) $this->price;
        }

        $now = now();
        if ($now->lt($this->discount_start_date) || $now->gt($this->discount_end_date)) {
            return (float) $this->price;
        }

        if ($this->discount_type === 'percentage') {
            $discounted = (float) $this->price - ((float) $this->price * (float) $this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discounted = (float) $this->price - (float) $this->discount_value;
        } else {
            $discounted = (float) $this->price;
        }

        return max($discounted, 0);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->slug) && !empty($product->title)) {
                $product->slug = Str::slug($product->title);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('title') && ! $product->isDirty('slug')) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

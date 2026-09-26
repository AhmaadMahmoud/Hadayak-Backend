<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'is_active', 'sort_order',
    ];


    protected static function booted(): void
    {
        static::creating(function (Product $m) {
            if (blank($m->slug)) {
                $m->slug = str()->slug($m->name) ?: str()->random(8);
                while (static::where('slug', $m->slug)->exists()) {
                    $m->slug .= '-' . str()->random(4);
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /** منتجات مشابهة من نفس القسم (لشاشة تفاصيل المنتج) */
    public function related(int $limit = 6)
    {
        return static::query()
            ->where('category_id', $this->category_id)
            ->whereKeyNot($this->getKey())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit($limit);
    }
}

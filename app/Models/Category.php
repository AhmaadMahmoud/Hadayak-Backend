<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'sort_order', 'is_active'];


    protected static function booted(): void
    {
        static::creating(function (Category $m) {
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
        return ['is_active' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WrapOption extends Model
{
    protected $fillable = ['name', 'image', 'price', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

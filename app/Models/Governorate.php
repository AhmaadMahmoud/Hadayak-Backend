<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name', 'shipping_fee', 'delivery_days', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'shipping_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

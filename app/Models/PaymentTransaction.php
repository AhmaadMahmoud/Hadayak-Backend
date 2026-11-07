<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id', 'provider', 'transaction_id', 'amount_cents', 'success', 'is_refund', 'payload',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'is_refund' => 'boolean',
            'payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

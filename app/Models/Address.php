<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'governorate_id', 'area', 'street', 'building', 'floor',
        'apartment', 'landmark', 'phone', 'lat', 'lng', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /** رسوم الشحن لعنوان دا — من المحافظة أو الافتراضي */
    public function shippingFee(): float
    {
        return (float) ($this->governorate?->shipping_fee ?? Setting::get('delivery_fee', 50));
    }
}

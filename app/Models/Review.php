<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'is_approved'];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** هل المستخدم دا اشترى المنتج دا فعلًا؟ */
    public static function userBoughtProduct(int $userId, int $productId): bool
    {
        return OrderItem::where('product_id', $productId)
            ->whereHas('order', fn ($q) => $q->where('user_id', $userId)->where('status', '!=', 'cancelled'))
            ->exists();
    }
}

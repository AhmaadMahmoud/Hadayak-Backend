<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'visitor_id', 'session_id', 'user_id', 'type', 'page',
        'label', 'meta', 'referrer', 'device', 'ip', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** أنواع الأحداث بالعربي للعرض في الداشبورد */
    public const TYPES = [
        'page_view'        => '📄 زار صفحة',
        'search'           => '🔍 بحث عن',
        'filter_category'  => '🏷️ اختار قسم',
        'add_to_cart'      => '🛒 ضاف للسلة',
        'remove_from_cart' => '🗑️ شال من السلة',
        'select_wrap'      => '🎀 اختار تغليف',
        'select_card'      => '💌 اختار بطاقة',
        'checkout_started' => '💳 بدأ الدفع',
        'order_placed'     => '🎉 أكد الطلب',
        'registered'       => '✨ عمل حساب',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending'    => 'جديد',
        'confirmed'  => 'مؤكد',
        'preparing'  => 'بيتجهز',
        'delivering' => 'في الطريق',
        'delivered'  => 'اتسلم',
        'cancelled'  => 'ملغي',
    ];

    public const PAYMENT_METHODS = [
        'card'          => 'بطاقة ائتمان',
        'vodafone_cash' => 'فودافون كاش',
        'instapay'      => 'انستا باي',
        'cod'           => 'الدفع عند الاستلام',
    ];

    protected $fillable = [
        'number', 'user_id', 'address_id', 'status',
        'delivery_type', 'recipient_name', 'recipient_phone', 'hide_invoice',
        'wrap_option_id', 'wrap_price', 'card_design_id', 'card_price', 'card_message',
        'payment_method', 'payment_status', 'payment_reference',
        'items_total', 'delivery_fee', 'total', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'hide_invoice' => 'boolean',
            'wrap_price' => 'decimal:2',
            'card_price' => 'decimal:2',
            'items_total' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            // رقم طلب مقروء: HDK-2026-000123
            if (! $order->number) {
                $next = (static::max('id') ?? 0) + 1;
                $order->number = sprintf('HDK-%s-%06d', now()->format('Y'), $next);
            }

            // الطلب الهدية ميتبعتش معاه فاتورة السعر
            if ($order->delivery_type === 'gift') {
                $order->hide_invoice = true;
            }
        });

        // إيميل للعميل مع كل تغيير في حالة الطلب — فشله عمره ما يكسر التحديث
        static::updated(function (Order $order) {
            if (! $order->wasChanged('status') || $order->status === 'pending') {
                return;
            }

            $email = $order->user?->email;

            if ($email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($email)
                        ->send(new \App\Mail\OrderStatusUpdated($order));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wrapOption(): BelongsTo
    {
        return $this->belongsTo(WrapOption::class);
    }

    public function cardDesign(): BelongsTo
    {
        return $this->belongsTo(CardDesign::class);
    }

    /** إعادة حساب الإجماليات من العناصر والإضافات */
    public function recalculateTotals(): void
    {
        $this->items_total = $this->items->sum(fn (OrderItem $i) => $i->price * $i->qty);
        $this->total = $this->items_total + $this->wrap_price + $this->card_price + $this->delivery_fee;
        $this->save();
    }
}

<?php

namespace App\Services;

/**
 * سلة الموقع — session-based زي سلة التطبيق بالظبط.
 * العناصر: [product_id => ['id','name','price','image','qty']]
 */
class WebCart
{
    public static function items(): array
    {
        return session('webcart.items', []);
    }

    public static function count(): int
    {
        return (int) collect(self::items())->sum('qty');
    }

    public static function itemsTotal(): float
    {
        return (float) collect(self::items())->sum(fn ($i) => $i['price'] * $i['qty']);
    }

    public static function add(int $id, string $name, float $price, ?string $image = null, int $qty = 1): void
    {
        $items = self::items();

        if (isset($items[$id])) {
            $items[$id]['qty'] += $qty;
        } else {
            $items[$id] = ['id' => $id, 'name' => $name, 'price' => $price, 'image' => $image, 'qty' => $qty];
        }

        session(['webcart.items' => $items]);
    }

    public static function increment(int $id): void
    {
        $items = self::items();

        if (isset($items[$id])) {
            $items[$id]['qty']++;
            session(['webcart.items' => $items]);
        }
    }

    public static function decrement(int $id): void
    {
        $items = self::items();

        if (isset($items[$id])) {
            $items[$id]['qty']--;

            if ($items[$id]['qty'] <= 0) {
                unset($items[$id]);
            }

            session(['webcart.items' => $items]);
        }
    }

    public static function remove(int $id): void
    {
        $items = self::items();
        unset($items[$id]);
        session(['webcart.items' => $items]);
    }

    // ===== التغليف =====
    public static function setWrap(?int $id, float $price = 0): void
    {
        session(['webcart.wrap_id' => $id, 'webcart.wrap_price' => $id ? $price : 0]);
    }

    public static function wrapId(): ?int
    {
        return session('webcart.wrap_id');
    }

    public static function wrapPrice(): float
    {
        return (float) session('webcart.wrap_price', 0);
    }

    // ===== بطاقة المعايدة =====
    public static function setCard(?int $id, float $price = 0, ?string $message = null): void
    {
        session([
            'webcart.card_id' => $id,
            'webcart.card_price' => $id ? $price : 0,
            'webcart.card_message' => $id ? $message : null,
        ]);
    }

    public static function cardId(): ?int
    {
        return session('webcart.card_id');
    }

    public static function cardPrice(): float
    {
        return (float) session('webcart.card_price', 0);
    }

    public static function cardMessage(): ?string
    {
        return session('webcart.card_message');
    }

    public static function clear(): void
    {
        session()->forget('webcart');
    }
}

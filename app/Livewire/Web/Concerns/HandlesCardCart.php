<?php

namespace App\Livewire\Web\Concerns;

use App\Models\Product;
use App\Services\WebCart;
use Illuminate\Support\Facades\Storage;

/**
 * أزرار السلة في كارت المنتج: إضافة أول مرة، وبعدها + و − على نفس الكارت.
 */
trait HandlesCardCart
{
    public function addToCart(int $productId): void
    {
        $product = Product::where('is_active', true)->with('images')->find($productId);

        if (! $product || ! ($product->stock === null || $product->stock > 0)) {
            return;
        }

        WebCart::add(
            $product->id,
            $product->name,
            (float) $product->price,
            $product->images->first()?->path ? Storage::disk('public')->url($product->images->first()->path) : null,
        );

        \App\Support\Track::event('add_to_cart', $product->name, ['product_id' => $product->id]);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-added',
            name: $product->name,
            price: (float) $product->price,
            qty: 1,
            image: WebCart::items()[$product->id]['image'] ?? null,
            count: WebCart::count(),
        );
    }

    public function cartIncrement(int $productId): void
    {
        $qty = WebCart::items()[$productId]['qty'] ?? 0;

        if ($qty === 0) {
            $this->addToCart($productId);

            return;
        }

        $product = Product::where('is_active', true)->find($productId, ['id', 'stock']);

        // متعديش المخزون المتاح
        if (! $product || ($product->stock !== null && $qty >= $product->stock) || $qty >= 99) {
            return;
        }

        WebCart::increment($productId);
        $this->dispatch('cart-updated');
    }

    public function cartDecrement(int $productId): void
    {
        $item = WebCart::items()[$productId] ?? null;

        if (! $item) {
            return;
        }

        WebCart::decrement($productId);

        if ($item['qty'] <= 1) {
            \App\Support\Track::event('remove_from_cart', $item['name'], ['product_id' => $productId]);
        }

        $this->dispatch('cart-updated');
    }
}

<?php

namespace App\Livewire\Web;

use App\Models\Product;
use App\Services\WebCart;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;

    public int $qty = 1;

    public function mount(Product $product): void
    {
        abort_unless($product->is_active, 404);
        $this->product = $product->load('images', 'category');
    }

    public function incrementQty(): void
    {
        if ($this->qty < 99) {
            $this->qty++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->qty > 1) {
            $this->qty--;
        }
    }

    public function addToCart(?int $productId = null): void
    {
        $product = $productId
            ? Product::where('is_active', true)->with('images')->find($productId)
            : $this->product;

        if (! $product || ! ($product->stock === null || $product->stock > 0)) {
            return;
        }

        WebCart::add(
            $product->id,
            $product->name,
            (float) $product->price,
            $product->images->first()?->path ? Storage::disk('public')->url($product->images->first()->path) : null,
            $productId ? 1 : $this->qty,
        );

        \App\Support\Track::event('add_to_cart', $product->name, ['product_id' => $product->id]);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-added',
            name: $product->name,
            price: (float) $product->price,
            qty: $productId ? 1 : $this->qty,
            image: WebCart::items()[$product->id]['image'] ?? null,
            count: WebCart::count(),
        );
        $this->qty = 1;
    }

    #[Layout('components.web.layout', ['title' => 'تفاصيل المنتج'])]
    #[Title('هداياك')]
    public function render()
    {
        return view('livewire.web.product-show', [
            'related' => $this->product->related()->with('images')->take(4)->get(),
        ]);
    }
}

<?php

namespace App\Livewire\Web;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Services\WebCart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    public function addToCart(int $productId): void
    {
        $product = Product::where('is_active', true)->with('images')->find($productId);

        if ($product && ($product->stock === null || $product->stock > 0)) {
            WebCart::add(
                $product->id,
                $product->name,
                (float) $product->price,
                $product->images->first()?->path ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->images->first()->path) : null,
            );
            \App\Support\Track::event('add_to_cart', $product->name, ['product_id' => $product->id]);
            $this->dispatch('cart-updated');
        }
    }

    #[Layout('components.web.layout', ['title' => 'الرئيسية'])]
    #[Title('هداياك — فرحتك هديتنا')]
    public function render()
    {
        return view('livewire.web.home', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'featured' => Product::where('is_active', true)->with('images')->orderBy('sort_order')->take(8)->get(),
            'services' => Service::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}

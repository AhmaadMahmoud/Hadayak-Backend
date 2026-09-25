<?php

namespace App\Livewire\Web;

use App\Models\Category;
use App\Models\Product;
use App\Services\WebCart;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    #[Url(as: 'category')]
    public ?int $categoryId = null;

    #[Url(as: 'search')]
    public ?string $search = null;

    public function updatedSearch(): void
    {
        $this->resetPage();

        $term = trim((string) $this->search);

        if (mb_strlen($term) >= 2) {
            \App\Support\Track::event('search', $term, page: 'web.products');
        }
    }

    public function selectCategory(?int $id): void
    {
        $this->categoryId = $id;
        $this->resetPage();

        if ($id) {
            $name = \App\Models\Category::find($id)?->name;
            \App\Support\Track::event('filter_category', $name, ['category_id' => $id], page: 'web.products');
        }
    }

    public function addToCart(int $productId): void
    {
        $product = Product::where('is_active', true)->with('images')->find($productId);

        if ($product && ($product->stock === null || $product->stock > 0)) {
            WebCart::add(
                $product->id,
                $product->name,
                (float) $product->price,
                $product->images->first()?->path ? Storage::disk('public')->url($product->images->first()->path) : null,
            );
            \App\Support\Track::event('add_to_cart', $product->name, ['product_id' => $product->id]);
            $this->dispatch('cart-updated');
        }
    }

    #[Layout('components.web.layout', ['title' => 'المنتجات'])]
    #[Title('المنتجات — هداياك')]
    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($this->categoryId, fn ($q, $id) => $q->where('category_id', $id))
            ->when(trim((string) $this->search), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->with('images')
            ->orderBy('sort_order')
            ->paginate(16);

        return view('livewire.web.products', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}

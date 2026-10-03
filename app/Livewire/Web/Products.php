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

    #[Url(as: 'sort')]
    public string $sort = 'default';

    #[Url(as: 'budget')]
    public ?string $budget = null;

    /** شرائح الميزانية: [label, min, max] */
    public const BUDGETS = [
        'under100' => ['أقل من 100', 0, 100],
        '100-300' => ['100 – 300', 100, 300],
        '300-600' => ['300 – 600', 300, 600],
        'over600' => ['أكتر من 600', 600, null],
    ];

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function selectBudget(?string $key): void
    {
        $this->budget = ($key === $this->budget) ? null : $key;
        $this->resetPage();

        if ($this->budget) {
            \App\Support\Track::event('filter_budget', self::BUDGETS[$this->budget][0] ?? $this->budget, page: 'web.products');
        }
    }

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
            $this->dispatch('cart-added',
                name: $product->name,
                price: (float) $product->price,
                qty: 1,
                image: WebCart::items()[$product->id]['image'] ?? null,
                count: WebCart::count(),
            );
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
            ->when($this->budget && isset(self::BUDGETS[$this->budget]), function ($q) {
                [, $min, $max] = self::BUDGETS[$this->budget];
                $q->where('price', '>=', $min);
                if ($max !== null) {
                    $q->where('price', '<', $max);
                }
            })
            ->with('images')
            ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->when($this->sort === 'default', fn ($q) => $q->orderBy('sort_order'))
            ->paginate(16);

        return view('livewire.web.products', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->whereHas('products', fn ($q) => $q->where('is_active', true))->orderBy('sort_order')->get(),
        ]);
    }
}

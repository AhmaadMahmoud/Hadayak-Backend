<?php

namespace App\Livewire\Web;

use App\Livewire\Web\Concerns\HandlesCardCart;
use App\Models\Product;
use App\Models\Review;
use App\Services\WebCart;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductShow extends Component
{
    use HandlesCardCart;

    public Product $product;

    public int $qty = 1;

    // التقييمات
    public int $myRating = 0;

    public string $reviewComment = '';

    public bool $canReview = false;

    public bool $alreadyReviewed = false;

    public function mount(Product $product): void
    {
        abort_unless($product->is_active, 404);
        $this->product = $product->load('images', 'category');

        if ($userId = \Illuminate\Support\Facades\Auth::id()) {
            $this->alreadyReviewed = Review::where('product_id', $product->id)->where('user_id', $userId)->exists();
            $this->canReview = ! $this->alreadyReviewed && Review::userBoughtProduct($userId, $product->id);
        }
    }

    public function submitReview(): void
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        // حماية من السيرفر: مشتري فعلي + مقيمش قبل كدا
        if (! $userId
            || Review::where('product_id', $this->product->id)->where('user_id', $userId)->exists()
            || ! Review::userBoughtProduct($userId, $this->product->id)) {
            return;
        }

        $this->validate(
            [
                'myRating' => 'required|integer|min:1|max:5',
                'reviewComment' => 'nullable|string|max:1000',
            ],
            ['myRating.min' => 'اختار عدد النجوم الأول ⭐'],
        );

        Review::create([
            'product_id' => $this->product->id,
            'user_id' => $userId,
            'rating' => $this->myRating,
            'comment' => trim($this->reviewComment) ?: null,
        ]);

        $this->alreadyReviewed = true;
        $this->canReview = false;
        \App\Support\Track::event('review', $this->product->name, ['rating' => $this->myRating]);
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
        $reviews = $this->product->approvedReviews()->with('user:id,name')->latest()->take(20)->get();

        $minShipping = \App\Models\Governorate::where('is_active', true)->min('shipping_fee');
        $deliverySummary = \App\Models\Setting::get('delivery_summary', '١–٣ أيام عمل للقاهرة والجيزة، و٣–٥ أيام لباقي المحافظات');

        return view('livewire.web.product-show', [
            'related' => $this->product->related()->with('images')->take(4)->get(),
            'reviews' => $reviews,
            'avgRating' => round((float) $this->product->approvedReviews()->avg('rating'), 1),
            'reviewsCount' => $this->product->approvedReviews()->count(),
            'minShipping' => $minShipping !== null ? (float) $minShipping : null,
            'deliverySummary' => $deliverySummary,
        ]);
    }
}

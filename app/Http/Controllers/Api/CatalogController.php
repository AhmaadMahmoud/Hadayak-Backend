<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardDesign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\WrapOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    /** داتا شاشة الهوم: الأقسام + الخدمات الخاصة */
    public function home(): JsonResponse
    {
        return response()->json([
            'categories' => Category::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => $this->category($c)),
            'services' => Service::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'image' => $this->imageUrl($s->image),
                ]),
        ]);
    }

    /** منتجات قسم معين + بحث */
    public function products(Request $request): JsonResponse
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($request->integer('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->string('search')->toString(), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->with('images')
            ->orderBy('sort_order')
            ->paginate(20);

        return response()->json([
            'products' => collect($products->items())->map(fn ($p) => $this->productCard($p)),
            'has_more' => $products->hasMorePages(),
            'page' => $products->currentPage(),
        ]);
    }

    /** تفاصيل منتج + المنتجات المشابهة */
    public function product(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);
        $product->load('images', 'category');

        return response()->json([
            'product' => [
                ...$this->productCard($product),
                'description' => $product->description,
                'category' => $this->category($product->category),
                'images' => $product->images->map(fn ($i) => $this->imageUrl($i->path))->values(),
            ],
            'related' => $product->related()->with('images')->get()
                ->map(fn ($p) => $this->productCard($p)),
        ]);
    }

    /** خيارات التغليف لشاشة "غلف هديتك" */
    public function wraps(): JsonResponse
    {
        return response()->json([
            'wraps' => WrapOption::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn ($w) => [
                    'id' => $w->id,
                    'name' => $w->name,
                    'price' => (float) $w->price,
                    'image' => $this->imageUrl($w->image),
                ]),
        ]);
    }

    /** أشكال الكروت لشاشة "بطاقة المعايدة" */
    public function cards(): JsonResponse
    {
        return response()->json([
            'cards' => CardDesign::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'price' => (float) $c->price,
                    'image' => $this->imageUrl($c->image),
                ]),
        ]);
    }

    /** إعدادات عامة للتطبيق */
    public function config(): JsonResponse
    {
        return response()->json([
            'delivery_fee' => (float) Setting::get('delivery_fee', 50),
            'cod_enabled' => (bool) (int) Setting::get('cod_enabled', 0),
            'support_phone' => Setting::get('support_phone', ''),
            'support_whatsapp' => Setting::get('support_whatsapp', ''),
            'support_email' => Setting::get('support_email', 'orders@hadayak.com'),
        ]);
    }

    private function category(Category $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'image' => $this->imageUrl($c->image),
        ];
    }

    private function productCard(Product $p): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->price,
            'image' => $this->imageUrl($p->images->first()?->path),
            'in_stock' => $p->stock === null || $p->stock > 0,
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}

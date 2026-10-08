<?php

namespace App\Livewire\Web;

use App\Livewire\Web\Concerns\HandlesCardCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    use HandlesCardCart;

    #[Layout('components.web.layout', ['title' => 'الرئيسية'])]
    #[Title('هداياك — فرحتك هديتنا')]
    public function render()
    {
        return view('livewire.web.home', [
            'banners' => \App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::where('is_active', true)->whereHas('products', fn ($q) => $q->where('is_active', true))->orderBy('sort_order')->get(),
            'featured' => $this->featuredProducts(),
            'services' => Service::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * منتجات مميزة موزعة على الأقسام: واحد من كل قسم بالتبادل لحد ٨ منتجات.
     */
    private function featuredProducts()
    {
        $grouped = Product::where('is_active', true)
            ->with('images')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($group) => $group->values())
            ->values();

        $featured = collect();

        for ($i = 0; $featured->count() < 8; $i++) {
            $addedAny = false;

            foreach ($grouped as $group) {
                if (isset($group[$i])) {
                    $featured->push($group[$i]);
                    $addedAny = true;

                    if ($featured->count() >= 8) {
                        break;
                    }
                }
            }

            if (! $addedAny) {
                break;
            }
        }

        return $featured;
    }
}

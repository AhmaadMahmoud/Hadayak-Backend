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
            'featured' => Product::where('is_active', true)->with('images')->orderBy('sort_order')->take(8)->get(),
            'services' => Service::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}

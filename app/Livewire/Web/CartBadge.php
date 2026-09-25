<?php

namespace App\Livewire\Web;

use App\Services\WebCart;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = WebCart::count();
    }

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $this->count = WebCart::count();
    }

    public function render()
    {
        return view('livewire.web.cart-badge');
    }
}

<?php

namespace App\Livewire\Web;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public function mount()
    {
        if (! Auth::check()) {
            session(['url.intended' => route('web.orders')]);

            return $this->redirect(route('web.login'), navigate: true);
        }
    }

    #[Layout('components.web.layout', ['title' => 'طلباتي'])]
    #[Title('طلباتي — هداياك')]
    public function render()
    {
        return view('livewire.web.orders', [
            'orders' => Auth::user()->orders()->with('items')->latest()->paginate(10),
        ]);
    }
}

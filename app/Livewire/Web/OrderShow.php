<?php

namespace App\Livewire\Web;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class OrderShow extends Component
{
    public Order $order;

    public bool $confirmingCancel = false;

    public ?string $error = null;

    public function mount(Order $order)
    {
        if (! Auth::check()) {
            return $this->redirect(route('web.login'), navigate: true);
        }

        abort_unless($order->user_id === Auth::id(), 403);
        $this->order = $order->load('items', 'address');
    }

    public function confirmCancel(): void
    {
        $this->error = null;
        $this->confirmingCancel = true;
    }

    public function dismissCancel(): void
    {
        $this->confirmingCancel = false;
    }

    public function cancelOrder(): void
    {
        $this->confirmingCancel = false;

        if ($this->order->status !== 'pending') {
            $this->error = 'الطلب دخل مرحلة التجهيز ومينفعش يتلغي — كلمنا وإحنا نظبطك';

            return;
        }

        DB::transaction(function () {
            $this->order->update(['status' => 'cancelled']);

            // رجّع الكميات للمخزون
            foreach ($this->order->items as $item) {
                Product::whereKey($item->product_id)
                    ->whereNotNull('stock')
                    ->increment('stock', $item->qty);
            }
        });

        $this->order = $this->order->fresh()->load('items', 'address');
    }

    #[Layout('components.web.layout', ['title' => 'تفاصيل الطلب'])]
    #[Title('تفاصيل الطلب — هداياك')]
    public function render()
    {
        return view('livewire.web.order-show');
    }
}

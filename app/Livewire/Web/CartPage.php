<?php

namespace App\Livewire\Web;

use App\Models\CardDesign;
use App\Models\Setting;
use App\Models\WrapOption;
use App\Services\WebCart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CartPage extends Component
{
    public ?int $wrapId = null;

    public ?int $cardId = null;

    public string $cardMessage = '';

    public function mount(): void
    {
        $this->wrapId = WebCart::wrapId();
        $this->cardId = WebCart::cardId();
        $this->cardMessage = (string) WebCart::cardMessage();
    }

    public function increment(int $id): void
    {
        WebCart::increment($id);
        $this->dispatch('cart-updated');
    }

    public function decrement(int $id): void
    {
        WebCart::decrement($id);
        $this->dispatch('cart-updated');
    }

    public function remove(int $id): void
    {
        $name = WebCart::items()[$id]['name'] ?? null;
        WebCart::remove($id);
        \App\Support\Track::event('remove_from_cart', $name, ['product_id' => $id]);
        $this->dispatch('cart-updated');
    }

    public function selectWrap(?int $id): void
    {
        if ($id === $this->wrapId) {
            $id = null; // نفس الاختيار = إلغاء
        }

        $wrap = $id ? WrapOption::where('is_active', true)->find($id) : null;
        $this->wrapId = $wrap?->id;
        WebCart::setWrap($wrap?->id, (float) ($wrap?->price ?? 0));

        if ($wrap) {
            \App\Support\Track::event('select_wrap', $wrap->name, ['wrap_id' => $wrap->id]);
        }
    }

    public function selectCard(?int $id): void
    {
        if ($id === $this->cardId) {
            $id = null;
        }

        $card = $id ? CardDesign::where('is_active', true)->find($id) : null;
        $this->cardId = $card?->id;
        WebCart::setCard($card?->id, (float) ($card?->price ?? 0), $this->cardMessage ?: null);

        if ($card) {
            \App\Support\Track::event('select_card', $card->name, ['card_id' => $card->id]);
        }
    }

    public function updatedCardMessage(): void
    {
        if ($this->cardId) {
            WebCart::setCard($this->cardId, WebCart::cardPrice(), $this->cardMessage ?: null);
        }
    }

    #[Layout('components.web.layout', ['title' => 'سلة الهدايا'])]
    #[Title('سلة الهدايا — هداياك')]
    public function render()
    {
        return view('livewire.web.cart-page', [
            'items' => WebCart::items(),
            'itemsTotal' => WebCart::itemsTotal(),
            'wrapPrice' => WebCart::wrapPrice(),
            'cardPrice' => WebCart::cardPrice(),
            'deliveryFee' => (float) Setting::get('delivery_fee', 50),
            'freeShippingAt' => (float) Setting::get('free_shipping_threshold', 0),
            'wraps' => WrapOption::where('is_active', true)->orderBy('sort_order')->get(),
            'cards' => CardDesign::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}

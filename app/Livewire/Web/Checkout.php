<?php

namespace App\Livewire\Web;

use App\Models\Setting;
use App\Services\PlaceOrder;
use App\Services\WebCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Checkout extends Component
{
    public ?int $addressId = null;

    // بيانات الزائر (من غير حساب)
    public string $guestName = '';

    public string $guestPhone = '';

    public string $guestEmail = '';

    public bool $showNewAddress = false;

    // عنوان جديد
    public ?int $governorateId = null;

    public string $area = '';

    public string $street = '';

    public string $building = '';

    public string $floor = '';

    public string $apartment = '';

    public string $landmark = '';

    public string $addressPhone = '';

    // التوصيل
    public string $deliveryType = 'me';

    public string $recipientName = '';

    public string $recipientPhone = '';

    public string $paymentMethod = 'card';

    public ?string $error = null;

    public function mount()
    {
        if (empty(WebCart::items())) {
            return $this->redirect(route('web.cart'), navigate: true);
        }

        if (Auth::check()) {
            $default = Auth::user()->addresses()->orderByDesc('is_default')->latest()->first();
            $this->addressId = $default?->id;
            $this->showNewAddress = $default === null;
        } else {
            $this->showNewAddress = true;
        }

        \App\Support\Track::event('checkout_started', null, ['cart_total' => WebCart::itemsTotal()], page: 'web.checkout');
    }

    public function selectAddress(int $id): void
    {
        $this->addressId = $id;
        $this->showNewAddress = false;
    }

    public function toggleNewAddress(): void
    {
        $this->showNewAddress = ! $this->showNewAddress;
    }

    public function setDeliveryType(string $type): void
    {
        if (in_array($type, ['me', 'gift'], true)) {
            $this->deliveryType = $type;
        }
    }

    public function setPayment(string $method): void
    {
        $cod = (bool) (int) Setting::get('cod_enabled', 0);

        if (in_array($method, ['card', 'vodafone_cash', 'instapay'], true) || ($method === 'cod' && $cod)) {
            $this->paymentMethod = $method;
        }
    }

    public function placeOrder()
    {
        $this->error = null;

        // زائر من غير حساب؟ نتأكد من بياناته ونعمله حساب على السريع
        if (! Auth::check()) {
            $this->validate(
                [
                    'guestName' => 'required|max:255',
                    'guestPhone' => 'required|max:20',
                    'guestEmail' => 'required|email',
                ],
                [
                    'guestName.required' => 'اكتب اسمك',
                    'guestPhone.required' => 'اكتب رقم موبايلك',
                    'guestEmail.required' => 'اكتب بريدك الإلكتروني عشان نبعتلك تأكيد الطلب',
                    'guestEmail.email' => 'البريد الإلكتروني مش صحيح',
                ],
            );

            $existing = \App\Models\User::where('phone', $this->guestPhone)
                ->orWhere('email', $this->guestEmail)
                ->first();

            if ($existing) {
                session(['url.intended' => route('web.checkout')]);
                $this->error = 'الرقم أو الإيميل دا مسجل عندنا قبل كدا — سجل دخولك وهتلاقي كل حاجة محفوظة ليك';

                return;
            }

            $guest = \App\Models\User::create([
                'name' => $this->guestName,
                'phone' => $this->guestPhone,
                'email' => $this->guestEmail,
                'password' => \Illuminate\Support\Str::random(24),
            ]);

            Auth::login($guest, remember: true);
            session()->regenerate();

            \App\Support\Track::event('registered', $guest->name, ['user_id' => $guest->id, 'via' => 'guest_checkout'], page: 'web.checkout');
        }

        $user = Auth::user();

        // عنوان جديد لو مفيش عنوان مختار
        if ($this->showNewAddress || ! $this->addressId) {
            $this->validate(
                ['governorateId' => 'required|exists:governorates,id', 'area' => 'required', 'street' => 'required'],
                ['governorateId.required' => 'اختار المحافظة عشان نحسب الشحن', 'area.required' => 'اكتب المنطقة', 'street.required' => 'اكتب الشارع'],
            );

            $address = $user->addresses()->create([
                'label' => 'home',
                'governorate_id' => $this->governorateId,
                'area' => $this->area,
                'street' => $this->street,
                'building' => $this->building ?: null,
                'floor' => $this->floor ?: null,
                'apartment' => $this->apartment ?: null,
                'landmark' => $this->landmark ?: null,
                'phone' => $this->addressPhone ?: $user->phone,
            ]);

            $this->addressId = $address->id;
        }

        if ($this->deliveryType === 'gift') {
            $this->validate(
                ['recipientName' => 'required', 'recipientPhone' => 'required'],
                [
                    'recipientName.required' => 'اكتب اسم المستلم — الهدية رايحة لمين؟',
                    'recipientPhone.required' => 'اكتب رقم موبايل المستلم',
                ],
            );
        }

        try {
            $order = PlaceOrder::handle($user, [
                'items' => collect(WebCart::items())->map(fn ($i) => [
                    'product_id' => $i['id'],
                    'qty' => $i['qty'],
                ])->values()->all(),
                'address_id' => $this->addressId,
                'delivery_type' => $this->deliveryType,
                'recipient_name' => $this->deliveryType === 'gift' ? $this->recipientName : null,
                'recipient_phone' => $this->deliveryType === 'gift' ? $this->recipientPhone : null,
                'wrap_option_id' => WebCart::wrapId(),
                'card_design_id' => WebCart::cardId(),
                'card_message' => WebCart::cardMessage(),
                'payment_method' => $this->paymentMethod,
            ]);
        } catch (ValidationException $e) {
            $this->error = collect($e->errors())->flatten()->first();

            return;
        }

        \App\Support\Track::event('order_placed', $order->number, [
            'order_id' => $order->id,
            'total' => (float) $order->total,
        ], page: 'web.checkout');

        WebCart::clear();
        $this->dispatch('cart-updated');
        session()->flash('order_placed', $order->number);

        return $this->redirect(route('web.order', $order), navigate: true);
    }

    #[Layout('components.web.layout', ['title' => 'إتمام الطلب'])]
    #[Title('إتمام الطلب — هداياك')]
    public function render()
    {
        // المحافظة المختارة: من العنوان المحفوظ أو من اختيار العنوان الجديد
        $selectedGov = null;

        if ($this->showNewAddress || ! $this->addressId) {
            $selectedGov = $this->governorateId ? \App\Models\Governorate::find($this->governorateId) : null;
        } else {
            $selectedGov = Auth::user()?->addresses()->find($this->addressId)?->governorate;
        }

        return view('livewire.web.checkout', [
            'addresses' => Auth::user()?->addresses()->with('governorate')->orderByDesc('is_default')->latest()->get() ?? collect(),
            'governorates' => \App\Models\Governorate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'shipping_fee', 'delivery_days']),
            'items' => WebCart::items(),
            'itemsTotal' => WebCart::itemsTotal(),
            'wrapPrice' => WebCart::wrapPrice(),
            'cardPrice' => WebCart::cardPrice(),
            'deliveryFee' => $selectedGov ? (float) $selectedGov->shipping_fee : null,
            'deliveryDays' => $selectedGov?->delivery_days,
            'codEnabled' => (bool) (int) Setting::get('cod_enabled', 0),
        ]);
    }
}

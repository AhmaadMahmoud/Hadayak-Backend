<?php

namespace App\Livewire\Web;

use App\Models\Setting;
use App\Services\WebCart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * ستوديو تصميم كارت المعايدة — العميل بيصمم والنتيجة بتتحفظ صورة PNG
 * بجودة طباعة وبتترفق بالطلب.
 */
class CardStudio extends Component
{
    public ?string $error = null;

    public function saveCard(string $image)
    {
        $this->error = null;

        $prefix = 'data:image/png;base64,';

        if (! str_starts_with($image, $prefix)) {
            $this->error = 'حصلت مشكلة في حفظ التصميم — جرب تاني';

            return;
        }

        $binary = base64_decode(substr($image, strlen($prefix)), true);

        // حد أقصى 12 ميجا للصورة النهائية (وشين بجودة طباعة)
        if ($binary === false || strlen($binary) < 1000 || strlen($binary) > 12 * 1024 * 1024) {
            $this->error = 'حصلت مشكلة في حفظ التصميم — جرب تاني';

            return;
        }

        $path = 'custom-cards/' . now()->format('Ymd') . '-' . Str::lower(Str::random(12)) . '.png';
        Storage::disk('public')->put($path, $binary);

        WebCart::setCustomCard($path, (float) Setting::get('custom_card_price', 0));

        $this->dispatch('cart-updated');
        session()->flash('custom_card_saved', true);

        return $this->redirect(route('web.cart'), navigate: true);
    }

    #[Layout('components.web.layout', ['title' => 'صمّم كارتك'])]
    #[Title('صمّم كارتك بنفسك — هداياك')]
    public function render()
    {
        return view('livewire.web.card-studio', [
            'price' => (float) Setting::get('custom_card_price', 0),
        ]);
    }
}

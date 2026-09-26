<?php

namespace App\Livewire\Web;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Support\Track;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ServiceOrder extends Component
{
    use WithFileUploads;

    public Service $service;

    public $photo = null;

    public string $notes = '';

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public bool $success = false;

    /** tshirt أو mug — بيحدد شكل المعاينة */
    public string $template = 'tshirt';

    public function mount(Service $service): void
    {
        abort_unless($service->is_active, 404);
        $this->service = $service;

        $this->template = (str_contains($service->name, 'مج') || str_contains($service->name, 'كوب'))
            ? 'mug'
            : 'tshirt';

        if ($user = Auth::user()) {
            $this->name = $user->name;
            $this->phone = $user->phone ?? '';
            $this->email = $user->email ?? '';
        }
    }

    public function submit(): void
    {
        $this->validate(
            [
                'photo' => 'required|image|max:4096',
                'notes' => 'nullable|string|max:1000',
                'name' => 'required|max:255',
                'phone' => 'required|max:20',
                'email' => 'nullable|email',
            ],
            [
                'photo.required' => 'ارفع الصورة اللي عايزها على المنتج الأول',
                'photo.image' => 'الملف لازم يكون صورة',
                'photo.max' => 'أقصى حجم للصورة 4 ميجا',
                'name.required' => 'اكتب اسمك',
                'phone.required' => 'اكتب رقم موبايلك عشان نتواصل معاك',
                'email.email' => 'البريد الإلكتروني مش صحيح',
            ],
        );

        $path = $this->photo->store('service-requests', 'public');

        ServiceRequest::create([
            'service_id' => $this->service->id,
            'user_id' => Auth::id(),
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'image' => $path,
            'notes' => $this->notes ?: null,
        ]);

        Track::event('service_request', $this->service->name, ['service_id' => $this->service->id]);

        $this->success = true;
    }

    #[Layout('components.web.layout', ['title' => 'خدمة مخصصة'])]
    #[Title('خدمة مخصصة — هداياك')]
    public function render()
    {
        return view('livewire.web.service-order');
    }
}

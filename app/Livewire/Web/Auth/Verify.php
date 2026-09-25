<?php

namespace App\Livewire\Web\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Verify extends Component
{
    public string $code = '';

    public ?string $error = null;

    public ?string $notice = null;

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route('web.home'), navigate: true);
        }

        if (! session('web_pending_phone')) {
            return $this->redirect(route('web.register'), navigate: true);
        }
    }

    public function submit()
    {
        $this->error = null;

        $this->validate(['code' => 'required'], ['code.required' => 'اكتب الكود اللي وصلك']);

        $user = User::where('phone', session('web_pending_phone'))->first();

        if (! $user || $user->otp_code !== $this->code || $user->otp_expires_at?->isPast()) {
            $this->error = 'الكود غير صحيح أو انتهت صلاحيته';

            return;
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        session()->forget('web_pending_phone');
        Auth::login($user, remember: true);
        session()->regenerate();

        return $this->redirect(route('web.home'), navigate: true);
    }

    public function resend(): void
    {
        $user = User::where('phone', session('web_pending_phone'))->first();

        if ($user) {
            $code = app()->isLocal() ? '1234' : (string) random_int(1000, 9999);
            $user->forceFill(['otp_code' => $code, 'otp_expires_at' => now()->addMinutes(10)])->save();
            Log::info("Hadayak OTP for {$user->phone}: {$code}");
            $this->notice = 'اتبعت كود جديد';
        }
    }

    #[Layout('components.web.layout', ['title' => 'تأكيد الحساب'])]
    #[Title('تأكيد الحساب — هداياك')]
    public function render()
    {
        return view('livewire.web.auth.verify');
    }
}

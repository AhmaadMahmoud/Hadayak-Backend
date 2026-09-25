<?php

namespace App\Livewire\Web\Auth;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $error = null;

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route('web.home'), navigate: true);
        }
    }

    public function submit()
    {
        $this->error = null;

        $this->validate(
            [
                'name' => 'required|max:255',
                'phone' => 'required|max:20|unique:users,phone',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|same:password_confirmation',
            ],
            [
                'name.required' => 'اكتب اسمك',
                'phone.required' => 'اكتب رقم موبايلك',
                'phone.unique' => 'الرقم دا مسجل قبل كدا — سجل دخولك',
                'email.required' => 'اكتب بريدك الإلكتروني',
                'email.email' => 'البريد الإلكتروني مش صحيح',
                'email.unique' => 'الإيميل دا مسجل قبل كدا',
                'password.required' => 'اكتب كلمة السر',
                'password.min' => 'كلمة السر لازم تكون ٦ حروف على الأقل',
                'password.same' => 'كلمتا السر مش متطابقتين',
            ],
        );

        $user = User::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        // OTP بنفس منطق التطبيق
        $code = app()->isLocal() ? '1234' : (string) random_int(1000, 9999);
        $user->forceFill(['otp_code' => $code, 'otp_expires_at' => now()->addMinutes(10)])->save();
        Log::info("Hadayak OTP for {$user->phone}: {$code}");

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        \App\Support\Track::event('registered', $user->name, ['user_id' => $user->id], page: 'web.register');

        session(['web_pending_phone' => $user->phone]);

        return $this->redirect(route('web.verify'), navigate: true);
    }

    #[Layout('components.web.layout', ['title' => 'حساب جديد'])]
    #[Title('حساب جديد — هداياك')]
    public function render()
    {
        return view('livewire.web.auth.register');
    }
}

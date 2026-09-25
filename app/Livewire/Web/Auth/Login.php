<?php

namespace App\Livewire\Web\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    public string $login = '';

    public string $password = '';

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
            ['login' => 'required', 'password' => 'required'],
            ['login.required' => 'اكتب الإيميل أو رقم الموبايل', 'password.required' => 'اكتب كلمة السر'],
        );

        $key = 'web-login:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->error = 'محاولات كتير — استنى دقيقة وجرب تاني';

            return;
        }

        RateLimiter::hit($key, 60);

        $user = User::where('phone', $this->login)->orWhere('email', $this->login)->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            $this->error = 'بيانات الدخول غير صحيحة';

            return;
        }

        Auth::login($user, remember: true);
        session()->regenerate();

        return $this->redirect(session()->pull('url.intended', route('web.home')), navigate: true);
    }

    #[Layout('components.web.layout', ['title' => 'تسجيل الدخول'])]
    #[Title('تسجيل الدخول — هداياك')]
    public function render()
    {
        return view('livewire.web.auth.login');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** تسجيل حساب جديد — بيبعت OTP للتحقق */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $this->sendOtp($user);

        // إيميل الترحيب — فشله عمره ما يكسر التسجيل
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'تم إرسال كود التحقق',
            'phone' => $user->phone,
        ], 201);
    }

    /** التحقق من الكود — بيرجع توكن */
    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if (! $user || $user->otp_code !== $data['code'] || $user->otp_expires_at?->isPast()) {
            throw ValidationException::withMessages(['code' => 'الكود غير صحيح أو انتهت صلاحيته']);
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        return $this->tokenResponse($user);
    }

    /** إعادة إرسال الكود */
    public function resendOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', 'string']]);
        $user = User::where('phone', $data['phone'])->firstOrFail();
        $this->sendOtp($user);

        return response()->json(['message' => 'تم إرسال كود التحقق']);
    }

    /** تسجيل الدخول بالإيميل أو الموبايل + الباسورد */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'], // email or phone
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $data['login'])
            ->orWhere('email', $data['login'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['login' => 'بيانات الدخول غير صحيحة']);
        }

        return $this->tokenResponse($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    /** تعديل بيانات الحساب */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'current_password' => ['required_with:password', 'nullable', 'string'],
        ]);

        // تغيير كلمة السر محتاج كلمة السر الحالية
        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                throw ValidationException::withMessages(['current_password' => 'كلمة السر الحالية غير صحيحة']);
            }
            $user->password = $data['password'];
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return response()->json(['user' => $this->userPayload($user)]);
    }

    /** نسيت كلمة السر — بيبعت OTP على الموبايل */
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', 'string']]);

        $user = User::where('phone', $data['phone'])->first();

        if (! $user) {
            throw ValidationException::withMessages(['phone' => 'مفيش حساب مسجل بالرقم ده']);
        }

        $this->sendOtp($user);

        return response()->json(['message' => 'تم إرسال كود التحقق']);
    }

    /** تعيين كلمة سر جديدة بالكود */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if (! $user || $user->otp_code !== $data['code'] || $user->otp_expires_at?->isPast()) {
            throw ValidationException::withMessages(['code' => 'الكود غير صحيح أو انتهت صلاحيته']);
        }

        $user->forceFill([
            'password' => $data['password'],
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        // خروج من كل الأجهزة القديمة بعد تغيير كلمة السر
        $user->tokens()->delete();

        return $this->tokenResponse($user);
    }

    /** حذف الحساب نهائيًا — مطلوب لمتاجر التطبيقات */
    public function destroyAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete(); // العناوين والطلبات بتتمسح معاه (cascade)

        return response()->json(['message' => 'تم حذف الحساب نهائيًا']);
    }

    private function sendOtp(User $user): void
    {
        // في التطوير الكود ثابت 1234 — هيتستبدل بمزود SMS حقيقي لاحقًا
        $code = app()->isLocal() ? '1234' : (string) random_int(1000, 9999);

        $user->forceFill([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        Log::info("Hadayak OTP for {$user->phone}: {$code}");
    }

    private function tokenResponse(User $user): JsonResponse
    {
        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $this->userPayload($user),
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'phone_verified' => $user->phone_verified_at !== null,
        ];
    }
}

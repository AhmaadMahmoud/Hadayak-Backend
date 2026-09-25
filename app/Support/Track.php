<?php

namespace App\Support;

use App\Models\VisitorEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * تتبع زوار الموقع — first-party analytics بسيطة.
 * كل الدوال آمنة: أي فشل في التسجيل عمره ما يكسر الصفحة.
 */
class Track
{
    public const COOKIE = 'hdk_vid';

    /** أسماء الصفحات بالعربي حسب اسم الراوت */
    public const PAGES = [
        'web.home' => 'الرئيسية',
        'web.products' => 'المنتجات',
        'web.product' => 'صفحة منتج',
        'web.cart' => 'السلة',
        'web.checkout' => 'إتمام الطلب',
        'web.login' => 'تسجيل الدخول',
        'web.register' => 'حساب جديد',
        'web.verify' => 'تأكيد الحساب',
        'web.orders' => 'طلباتي',
        'web.order' => 'تفاصيل طلب',
        'web.about' => 'عن هداياك',
        'web.contact' => 'تواصل معنا',
        'web.privacy' => 'سياسة الخصوصية',
    ];

    /** معرف الزائر الثابت (كوكي سنة) — بيتعمل أول زيارة */
    public static function visitorId(): string
    {
        $existing = Cookie::get(self::COOKIE);

        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        // اتولد في نفس الطلب قبل كدا؟
        if ($fresh = request()->attributes->get(self::COOKIE)) {
            return $fresh;
        }

        $id = (string) Str::uuid();
        request()->attributes->set(self::COOKIE, $id);
        Cookie::queue(cookie(self::COOKIE, $id, 60 * 24 * 365));

        return $id;
    }

    public static function event(string $type, ?string $label = null, array $meta = [], ?string $page = null): void
    {
        try {
            $request = request();

            VisitorEvent::create([
                'visitor_id' => self::visitorId(),
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
                'type' => $type,
                'page' => $page ?? $request->route()?->getName(),
                'label' => $label ? Str::limit($label, 480) : null,
                'meta' => $meta ?: null,
                'referrer' => Str::limit((string) $request->headers->get('referer'), 480) ?: null,
                'device' => preg_match('/Mobile|Android|iPhone|iPad/i', (string) $request->userAgent()) ? 'mobile' : 'desktop',
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

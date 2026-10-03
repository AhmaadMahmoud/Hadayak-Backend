<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تحديث طلب هداياك</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background-color:#F6F2EE;font-family:'Cairo',Tahoma,Arial,sans-serif;">

@php
    $meta = [
        'confirmed'  => ['emoji' => '✅', 'title' => 'طلبك اتأكد!', 'text' => 'استلمنا طلبك وهنبدأ نجهز هديتك على طول'],
        'preparing'  => ['emoji' => '🎀', 'title' => 'هديتك بتتجهز!', 'text' => 'فريقنا بيغلف هديتك بكل حب دلوقتي'],
        'delivering' => ['emoji' => '🚚', 'title' => 'هديتك في الطريق!', 'text' => 'المندوب خرج بالطلب — خليك قريب من الموبايل'],
        'delivered'  => ['emoji' => '🎉', 'title' => 'الهدية وصلت!', 'text' => 'يارب تكون فرّحت اللي بتحبهم. شكرًا إنك اخترت هداياك'],
        'cancelled'  => ['emoji' => '😢', 'title' => 'الطلب اتلغى', 'text' => 'طلبك اتلغى زي ما طلبت. لو دا حصل بالغلط كلمنا وهنظبطك'],
    ][$order->status] ?? ['emoji' => '🎁', 'title' => 'في جديد في طلبك', 'text' => 'حالة طلبك اتحدثت'];
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F6F2EE;padding:24px 12px;">
<tr><td align="center">

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- الهيدر --}}
  <tr><td style="background:linear-gradient(135deg,#E30B2C 0%,#C20D2C 100%);background-color:#D81D35;border-radius:20px 20px 0 0;padding:28px 24px;text-align:center;">
    <img src="{{ asset('images/logo-white.png') }}" alt="هداياك" width="64" style="display:block;margin:0 auto;">
    <div style="color:#ffffff;font-size:26px;font-weight:700;margin-top:8px;">هداياك</div>
  </td></tr>

  {{-- الحالة --}}
  <tr><td style="background-color:#ffffff;padding:36px 24px 24px;text-align:center;">
    <table role="presentation" cellpadding="0" cellspacing="0" align="center"><tr>
      <td style="width:72px;height:72px;background-color:#FFF0EE;border-radius:50%;text-align:center;vertical-align:middle;font-size:34px;">{{ $meta['emoji'] }}</td>
    </tr></table>
    <div style="font-size:24px;font-weight:700;color:#281715;margin-top:16px;">{{ $meta['title'] }}</div>
    <div style="font-size:15px;color:#5C403C;margin-top:6px;">{{ $meta['text'] }}</div>
    <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin-top:16px;"><tr>
      <td style="background-color:#FFF0EE;border:2px solid #D81D35;border-radius:100px;padding:8px 24px;font-size:15px;font-weight:700;color:#D81D35;">
        رقم الطلب: {{ $order->number }}
      </td>
    </tr></table>
  </td></tr>

  {{-- ملخص سريع --}}
  <tr><td style="background-color:#ffffff;padding:0 24px 8px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F9F6F1;border-radius:14px;">
      <tr><td style="padding:16px 18px;">
        @foreach ($order->items as $item)
          <div style="font-size:14px;color:#281715;padding:4px 0;">🎁 {{ $item->product_name }} <span style="color:#8A8A8A;font-size:12px;">× {{ $item->qty }}</span></div>
        @endforeach
        <div style="font-size:14px;font-weight:700;color:#D81D35;border-top:1px solid #F0E9E0;margin-top:8px;padding-top:10px;">
          الإجمالي: <span dir="ltr">{{ number_format($order->total) }} ج.م</span>
        </div>
      </td></tr>
    </table>
  </td></tr>

  {{-- الحالة الحالية --}}
  <tr><td style="background-color:#ffffff;border-radius:0 0 20px 20px;padding:8px 24px 28px;text-align:center;">
    <span style="display:inline-block;background-color:#FFF0EE;border-radius:100px;padding:8px 20px;font-size:14px;font-weight:700;color:#D81D35;">
      حالة الطلب: {{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}
    </span>
  </td></tr>

  {{-- الفوتر --}}
  <tr><td style="padding:24px 24px 8px;text-align:center;">
    <div style="font-size:13px;font-weight:700;color:#D81D35;">هداياك — جهز الهدية وانت في مكانك 🎁</div>
    <div style="font-size:11px;color:#A8A3A3;margin-top:8px;line-height:1.8;">
      وصلك الإيميل دا لأن حالة طلبك على تطبيق هداياك اتحدثت<br>
      لأي استفسار كلمنا على info@hdayak.com
    </div>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>

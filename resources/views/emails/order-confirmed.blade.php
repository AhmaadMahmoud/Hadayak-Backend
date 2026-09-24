<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تأكيد طلب هداياك</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background-color:#F6F2EE;font-family:'Cairo',Tahoma,Arial,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F6F2EE;padding:24px 12px;">
<tr><td align="center">

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  {{-- الهيدر --}}
  <tr><td style="background:linear-gradient(135deg,#E30B2C 0%,#C20D2C 100%);background-color:#D81D35;border-radius:20px 20px 0 0;padding:28px 24px;text-align:center;">
    <img src="{{ asset('images/logo-white.png') }}" alt="هداياك" width="64" style="display:block;margin:0 auto;">
    <div style="color:#ffffff;font-size:26px;font-weight:700;margin-top:8px;">هداياك</div>
  </td></tr>

  {{-- رسالة النجاح --}}
  <tr><td style="background-color:#ffffff;padding:36px 24px 20px;text-align:center;">
    <table role="presentation" cellpadding="0" cellspacing="0" align="center"><tr>
      <td style="width:72px;height:72px;background-color:#FFF0EE;border-radius:50%;text-align:center;vertical-align:middle;font-size:34px;">🎉</td>
    </tr></table>
    <div style="font-size:24px;font-weight:700;color:#281715;margin-top:16px;">طلبك وصلنا يا {{ $order->user->name }}!</div>
    <div style="font-size:15px;color:#5C403C;margin-top:6px;">هديتك بقت في إيدين أمينة، وهنبدأ نجهزها على طول</div>
    <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin-top:16px;"><tr>
      <td style="background-color:#FFF0EE;border:2px solid #D81D35;border-radius:100px;padding:8px 24px;font-size:15px;font-weight:700;color:#D81D35;">
        رقم الطلب: {{ $order->number }}
      </td>
    </tr></table>
  </td></tr>

  {{-- تفاصيل الطلب --}}
  <tr><td style="background-color:#ffffff;padding:8px 24px 4px;">
    <div style="font-size:16px;font-weight:700;color:#D41D38;border-bottom:1px solid #F3EFE7;padding-bottom:10px;">تفاصيل الطلب</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:4px;">
      @foreach ($order->items as $item)
      <tr>
        <td style="padding:12px 0;font-size:14px;color:#281715;border-bottom:1px solid #F9F6F1;">{{ $item->product_name }} <span style="color:#8A8A8A;font-size:12px;">× {{ $item->qty }}</span></td>
        <td align="left" style="padding:12px 0;font-size:14px;font-weight:700;color:#281715;border-bottom:1px solid #F9F6F1;" dir="ltr">{{ number_format($item->price * $item->qty) }} ج.م</td>
      </tr>
      @endforeach

      @if ($order->wrapOption)
      <tr>
        <td style="padding:12px 0;font-size:14px;color:#5C403C;">🎀 {{ $order->wrapOption->name }}</td>
        <td align="left" style="padding:12px 0;font-size:14px;color:#5C403C;" dir="ltr">{{ number_format($order->wrap_price) }} ج.م</td>
      </tr>
      @endif

      @if ($order->cardDesign)
      <tr>
        <td style="padding:2px 0 12px;font-size:14px;color:#5C403C;border-bottom:1px solid #F3EFE7;">💌 {{ $order->cardDesign->name }}</td>
        <td align="left" style="padding:2px 0 12px;font-size:14px;color:#5C403C;border-bottom:1px solid #F3EFE7;" dir="ltr">{{ number_format($order->card_price) }} ج.م</td>
      </tr>
      @endif

      <tr>
        <td style="padding:12px 0 4px;font-size:13px;color:#8A8A8A;">التوصيل</td>
        <td align="left" style="padding:12px 0 4px;font-size:13px;color:#8A8A8A;" dir="ltr">{{ number_format($order->delivery_fee) }} ج.م</td>
      </tr>
      <tr>
        <td style="padding:10px 0 16px;font-size:17px;font-weight:700;color:#281715;">الإجمالي</td>
        <td align="left" style="padding:10px 0 16px;font-size:20px;font-weight:700;color:#D81D35;" dir="ltr">{{ number_format($order->total) }} ج.م</td>
      </tr>
    </table>
  </td></tr>

  {{-- رسالة الكارت --}}
  @if ($order->card_message)
  <tr><td style="background-color:#ffffff;padding:0 24px 8px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="background-color:#FFF0EE;border:1px solid #D81D35;border-radius:12px;padding:14px 18px;">
        <div style="font-size:13px;font-weight:700;color:#D81D35;">الكتابة على الكارت</div>
        <div style="font-size:14px;color:#281715;margin-top:4px;line-height:1.8;">{{ $order->card_message }}</div>
      </td></tr>
    </table>
  </td></tr>
  @endif

  {{-- التوصيل --}}
  <tr><td style="background-color:#ffffff;padding:12px 24px 8px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="background-color:#FCFAF8;border:1px solid #F3EFE7;border-radius:12px;padding:16px 18px;">
        <div style="font-size:13px;font-weight:700;color:#5C403C;">🚚 التوصيل إلى</div>
        <div style="font-size:14px;color:#281715;margin-top:6px;line-height:1.8;">
          @if ($order->delivery_type === 'gift')
            🎁 هدية إلى: <b>{{ $order->recipient_name }}</b> ({{ $order->recipient_phone }})<br>
          @endif
          @if ($order->address)
            {{ collect([$order->address->area, $order->address->street, $order->address->building ? 'مبنى '.$order->address->building : null, $order->address->floor ? 'الدور '.$order->address->floor : null, $order->address->apartment ? 'شقة '.$order->address->apartment : null])->filter()->implode('، ') }}
          @endif
        </div>
        @if ($order->hide_invoice)
        <div style="font-size:12px;color:#B26A00;background-color:#FFF8E1;border-radius:8px;padding:8px 12px;margin-top:10px;">
          متقلقش — الطلب هدية، فمش هنبعت فاتورة السعر مع الباكدج 😉
        </div>
        @endif
      </td></tr>
    </table>
  </td></tr>

  {{-- وسيلة الدفع --}}
  <tr><td style="background-color:#ffffff;padding:8px 24px 24px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
      <td style="font-size:13px;color:#8A8A8A;">وسيلة الدفع: <span style="color:#281715;font-weight:600;">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span></td>
      <td align="left" style="font-size:13px;color:#8A8A8A;">حالة الطلب: <span style="color:#D81D35;font-weight:700;">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></td>
    </tr></table>
  </td></tr>

  {{-- الفوتر --}}
  <tr><td style="background-color:#ffffff;border-radius:0 0 20px 20px;padding:0 24px 8px;"></td></tr>
  <tr><td style="padding:24px 24px 8px;text-align:center;">
    <div style="font-size:13px;font-weight:700;color:#D81D35;">هداياك — جهز الهدية وانت في مكانك 🎁</div>
    <div style="font-size:11px;color:#A8A3A3;margin-top:8px;line-height:1.8;">
      وصلك الإيميل دا لأنك عملت طلب على تطبيق هداياك<br>
      لأي استفسار كلمنا على support@hadayak.com
    </div>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>

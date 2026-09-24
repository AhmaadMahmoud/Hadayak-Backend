<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>أهلًا بيك في هداياك</title>
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

  {{-- الترحيب --}}
  <tr><td style="background-color:#ffffff;padding:36px 24px 20px;text-align:center;">
    <table role="presentation" cellpadding="0" cellspacing="0" align="center"><tr>
      <td style="width:72px;height:72px;background-color:#FFF0EE;border-radius:50%;text-align:center;vertical-align:middle;font-size:34px;">🎁</td>
    </tr></table>
    <div style="font-size:24px;font-weight:700;color:#281715;margin-top:16px;">أهلًا بيك يا {{ $user->name }}!</div>
    <div style="font-size:15px;color:#5C403C;margin-top:6px;line-height:1.9;">
      حسابك في هداياك اتعمل بنجاح ✨<br>
      من النهاردة تقدر تجهز الهدية وانت في مكانك — واحنا علينا الباقي
    </div>
  </td></tr>

  {{-- ازاي هداياك بتشتغل --}}
  <tr><td style="background-color:#ffffff;padding:8px 24px 12px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="background-color:#FCFAF8;border:1px solid #F3EFE7;border-radius:12px;padding:20px 18px;">
        <div style="font-size:15px;font-weight:700;color:#D41D38;margin-bottom:14px;text-align:center;">في 3 خطوات هديتك بتوصل 👇</div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="width:44px;vertical-align:top;padding:8px 0;">
              <div style="width:36px;height:36px;background-color:#FFF0EE;border-radius:10px;text-align:center;line-height:36px;font-size:18px;">🛍️</div>
            </td>
            <td style="padding:8px 8px;vertical-align:middle;">
              <div style="font-size:14px;font-weight:700;color:#281715;">اختار الهدية</div>
              <div style="font-size:12px;color:#5C403C;">ألعاب، عطور، ساعات، ورد، دباديب وأكتر</div>
            </td>
          </tr>
          <tr>
            <td style="width:44px;vertical-align:top;padding:8px 0;">
              <div style="width:36px;height:36px;background-color:#FFF0EE;border-radius:10px;text-align:center;line-height:36px;font-size:18px;">🎀</div>
            </td>
            <td style="padding:8px 8px;vertical-align:middle;">
              <div style="font-size:14px;font-weight:700;color:#281715;">غلفها واكتب كارت</div>
              <div style="font-size:12px;color:#5C403C;">تغليف شيك وكارت معايدة بكلماتك انت</div>
            </td>
          </tr>
          <tr>
            <td style="width:44px;vertical-align:top;padding:8px 0;">
              <div style="width:36px;height:36px;background-color:#FFF0EE;border-radius:10px;text-align:center;line-height:36px;font-size:18px;">🚚</div>
            </td>
            <td style="padding:8px 8px;vertical-align:middle;">
              <div style="font-size:14px;font-weight:700;color:#281715;">نوصلها لباب البيت</div>
              <div style="font-size:12px;color:#5C403C;">ليك أو هدية لحد غالي — ومن غير فاتورة سعر 😉</div>
            </td>
          </tr>
        </table>
      </td></tr>
    </table>
  </td></tr>

  {{-- زرار البداية --}}
  <tr><td style="background-color:#ffffff;padding:12px 24px 32px;text-align:center;border-radius:0 0 20px 20px;">
    <table role="presentation" cellpadding="0" cellspacing="0" align="center"><tr>
      <td style="background-color:#D81D35;border-radius:20px;box-shadow:0 4px 8px rgba(216,29,53,0.3);">
        <a href="#" style="display:inline-block;padding:16px 56px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;font-family:'Cairo',Tahoma,Arial,sans-serif;">ابدأ التسوق 🎁</a>
      </td>
    </tr></table>
  </td></tr>

  {{-- الفوتر --}}
  <tr><td style="padding:24px 24px 8px;text-align:center;">
    <div style="font-size:13px;font-weight:700;color:#D81D35;">هداياك — جهز الهدية وانت في مكانك 🎁</div>
    <div style="font-size:11px;color:#A8A3A3;margin-top:8px;line-height:1.8;">
      وصلك الإيميل دا لأنك عملت حساب على تطبيق هداياك<br>
      لأي استفسار كلمنا على support@hadayak.com
    </div>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>

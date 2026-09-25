<?php

namespace App\Http\Middleware;

use App\Support\Track;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // بنسجل زيارات صفحات المتجر بس — مش الداشبورد ولا طلبات Livewire الداخلية
        $routeName = $request->route()?->getName();

        if (
            $request->isMethod('GET')
            && is_string($routeName)
            && str_starts_with($routeName, 'web.')
            && ! $request->headers->has('X-Livewire')
        ) {
            $label = Track::PAGES[$routeName] ?? $routeName;

            // صفحة منتج أو طلب؟ سجل اسمه عشان الرحلة تبقى مقروءة
            if ($routeName === 'web.product' && ($product = $request->route('product'))) {
                $label = 'منتج: '.$product->name;
            } elseif ($routeName === 'web.order' && ($order = $request->route('order'))) {
                $label = 'طلب: '.$order->number;
            }

            Track::event('page_view', $label, page: $routeName);
        }

        return $response;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\VisitorEvent;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorsStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $today = now()->startOfDay();

        $visitorsToday = VisitorEvent::where('created_at', '>=', $today)
            ->distinct('visitor_id')
            ->count('visitor_id');

        $viewsToday = VisitorEvent::where('created_at', '>=', $today)
            ->where('type', 'page_view')
            ->count();

        $topPage = VisitorEvent::where('created_at', '>=', $today)
            ->where('type', 'page_view')
            ->selectRaw('label, count(*) as views')
            ->groupBy('label')
            ->orderByDesc('views')
            ->first();

        $buyersToday = VisitorEvent::where('created_at', '>=', $today)
            ->where('type', 'order_placed')
            ->distinct('visitor_id')
            ->count('visitor_id');

        $conversion = $visitorsToday > 0
            ? round($buyersToday / $visitorsToday * 100, 1)
            : 0;

        return [
            Stat::make('زوار الموقع النهاردة', $visitorsToday)
                ->description('زائر مختلف')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary'),

            Stat::make('مشاهدات الصفحات', $viewsToday)
                ->description('صفحة اتفتحت النهاردة')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('أكتر صفحة زيارة', $topPage?->label ?? '—')
                ->description($topPage ? $topPage->views.' مشاهدة' : 'لسه مفيش زيارات')
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning'),

            Stat::make('نسبة التحويل', $conversion.'%')
                ->description('من الزوار عملوا طلب النهاردة')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color($conversion > 0 ? 'success' : 'gray'),
        ];
    }
}

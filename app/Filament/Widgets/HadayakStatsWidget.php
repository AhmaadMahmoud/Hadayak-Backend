<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HadayakStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->startOfDay();

        $ordersToday = Order::where('created_at', '>=', $today)->count();
        $revenueToday = Order::where('created_at', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $pending = Order::where('status', 'pending')->count();
        $customers = User::where('is_admin', false)->count();

        return [
            Stat::make('طلبات النهاردة', $ordersToday)
                ->description('طلب جديد من التطبيق')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('إيرادات النهاردة', number_format((float) $revenueToday) . ' ج.م')
                ->description('غير شامل الطلبات الملغية')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('طلبات مستنية تأكيد', $pending)
                ->description($pending > 0 ? 'محتاجة مراجعتك' : 'كله متظبط ✨')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('العملاء', $customers)
                ->description('إجمالي المسجلين')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray'),
        ];
    }
}

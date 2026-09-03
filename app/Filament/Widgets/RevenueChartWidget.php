<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'المبيعات آخر 14 يوم';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(13, 0))
            ->map(fn (int $back) => now()->subDays($back)->startOfDay());

        $revenue = $days->map(
            fn ($day) => (float) Order::whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->where('status', '!=', 'cancelled')
                ->sum('total')
        );

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ج.م)',
                    'data' => $revenue->all(),
                    'borderColor' => '#D81D35',
                    'backgroundColor' => 'rgba(216, 29, 53, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\VisitorEvent;
use Filament\Widgets\ChartWidget;

class VisitorsChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'زوار الموقع آخر 14 يوم';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(13, 0))
            ->map(fn (int $back) => now()->subDays($back)->startOfDay());

        $visitors = $days->map(
            fn ($day) => VisitorEvent::whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->distinct('visitor_id')
                ->count('visitor_id')
        );

        $orders = $days->map(
            fn ($day) => VisitorEvent::whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->where('type', 'order_placed')
                ->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'زوار',
                    'data' => $visitors->all(),
                    'borderColor' => '#D81D35',
                    'backgroundColor' => 'rgba(216, 29, 53, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'طلبات من الموقع',
                    'data' => $orders->all(),
                    'borderColor' => '#F1BE18',
                    'backgroundColor' => 'rgba(241, 190, 24, 0.08)',
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

<?php

namespace App\Filament\Pages;

use App\Models\VisitorEvent;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use UnitEnum;

class VisitorJourneys extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|UnitEnum|null $navigationGroup = 'التحليلات';

    protected static ?string $navigationLabel = 'رحلات الزوار';

    protected static ?string $title = 'رحلات الزوار';

    protected string $view = 'filament.pages.visitor-journeys';

    /** آخر الجلسات ومعاها كل خطوات الزائر بالترتيب */
    public function getSessions(): Collection
    {
        // آخر 40 جلسة نشطة في آخر 7 أيام
        $latest = VisitorEvent::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('session_id, max(created_at) as last_seen')
            ->groupBy('session_id')
            ->orderByDesc('last_seen')
            ->limit(40)
            ->pluck('session_id');

        return VisitorEvent::whereIn('session_id', $latest)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get()
            ->groupBy('session_id')
            ->map(fn ($events) => [
                'visitor' => substr($events->first()->visitor_id, 0, 8),
                'user' => $events->firstWhere('user_id', '!=', null)?->user?->name,
                'device' => $events->first()->device,
                'started_at' => $events->first()->created_at,
                'last_seen' => $events->last()->created_at,
                'ordered' => $events->contains('type', 'order_placed'),
                'events' => $events,
            ])
            ->sortByDesc('last_seen')
            ->values();
    }
}

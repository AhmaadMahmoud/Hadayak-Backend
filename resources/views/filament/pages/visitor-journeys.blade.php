<x-filament-panels::page>
    @php $sessions = $this->getSessions(); @endphp

    <style>
        .hdk-empty { background: var(--hdk-card, #fff); border-radius: 16px; padding: 48px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .hdk-empty .e { font-size: 40px; }
        .hdk-empty p { margin: 12px 0 0; font-weight: 700; color: #374151; }
        .hdk-empty small { color: #9ca3af; }

        .hdk-list { display: flex; flex-direction: column; gap: 14px; }

        .hdk-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid rgba(0,0,0,.05); overflow: hidden; }
        .dark .hdk-card { background: #18181b; border-color: rgba(255,255,255,.08); }

        .hdk-summary { display: flex; align-items: center; gap: 14px; padding: 16px 18px; cursor: pointer; list-style: none; }
        .hdk-summary::-webkit-details-marker { display: none; }

        .hdk-avatar { display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 999px; font-size: 19px; background: #f3f4f6; flex-shrink: 0; }
        .dark .hdk-avatar { background: #27272a; }
        .hdk-avatar.win { background: #dcfce7; }
        .dark .hdk-avatar.win { background: rgba(34,197,94,.18); }

        .hdk-who { flex: 1; min-width: 0; }
        .hdk-who b { display: block; font-size: 14px; color: #1f2937; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dark .hdk-who b { color: #f4f4f5; }
        .hdk-who b code { font-size: 11px; color: #9ca3af; font-weight: 400; }
        .hdk-who span { display: block; font-size: 12px; color: #6b7280; margin-top: 2px; }

        .hdk-badge { flex-shrink: 0; background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 800; border-radius: 999px; padding: 5px 12px; }
        .dark .hdk-badge { background: rgba(34,197,94,.18); color: #4ade80; }

        .hdk-chev { flex-shrink: 0; width: 16px; height: 16px; color: #9ca3af; transition: transform .2s; }
        details[open] .hdk-chev { transform: rotate(180deg); }

        .hdk-timeline { border-top: 1px solid rgba(0,0,0,.06); padding: 18px 22px 20px; }
        .dark .hdk-timeline { border-color: rgba(255,255,255,.08); }
        .hdk-timeline ol { margin: 0; padding: 0 18px 0 0; list-style: none; border-right: 2px solid #f1f5f9; display: flex; flex-direction: column; gap: 14px; }
        .dark .hdk-timeline ol { border-color: #27272a; }

        .hdk-step { position: relative; }
        .hdk-dot { position: absolute; right: -25px; top: 4px; width: 12px; height: 12px; border-radius: 999px; background: #d1d5db; box-shadow: 0 0 0 4px #fff; }
        .dark .hdk-dot { box-shadow: 0 0 0 4px #18181b; background: #3f3f46; }
        .hdk-dot.buy { background: #22c55e; }
        .hdk-dot.hot { background: #f59e0b; }
        .hdk-dot.find { background: #D81D35; }

        .hdk-step p { margin: 0; font-size: 13.5px; color: #374151; }
        .dark .hdk-step p { color: #d4d4d8; }
        .hdk-step p b { color: #111827; }
        .dark .hdk-step p b { color: #fafafa; }
        .hdk-step time { font-size: 11px; color: #9ca3af; }
    </style>

    @if ($sessions->isEmpty())
        <div class="hdk-empty">
            <div class="e">👀</div>
            <p>لسه مفيش زيارات مسجلة</p>
            <small>أول ما حد يفتح الموقع هتلاقي رحلته هنا خطوة بخطوة</small>
        </div>
    @else
        <div class="hdk-list">
            @foreach ($sessions as $session)
                <details class="hdk-card">
                    <summary class="hdk-summary">
                        <span class="hdk-avatar {{ $session['ordered'] ? 'win' : '' }}">
                            {{ $session['ordered'] ? '🎉' : ($session['device'] === 'mobile' ? '📱' : '💻') }}
                        </span>

                        <span class="hdk-who">
                            <b>
                                {{ $session['user'] ?? 'زائر مجهول' }}
                                <code>#{{ $session['visitor'] }}</code>
                            </b>
                            <span>
                                {{ $session['started_at']->translatedFormat('d M — h:i A') }}
                                · {{ $session['events']->count() }} خطوة
                                · {{ $session['device'] === 'mobile' ? 'موبايل' : 'كمبيوتر' }}
                            </span>
                        </span>

                        @if ($session['ordered'])
                            <span class="hdk-badge">عمل طلب ✓</span>
                        @endif

                        <svg class="hdk-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>

                    <div class="hdk-timeline">
                        <ol>
                            @foreach ($session['events'] as $event)
                                <li class="hdk-step">
                                    <span @class([
                                        'hdk-dot',
                                        'buy' => $event->type === 'order_placed',
                                        'hot' => in_array($event->type, ['add_to_cart', 'checkout_started']),
                                        'find' => $event->type === 'search',
                                    ])></span>
                                    <p>
                                        <b>{{ \App\Models\VisitorEvent::TYPES[$event->type] ?? $event->type }}</b>
                                        @if ($event->label)
                                            — {{ $event->label }}
                                        @endif
                                    </p>
                                    <time>{{ $event->created_at->format('h:i:s A') }}</time>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </details>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>

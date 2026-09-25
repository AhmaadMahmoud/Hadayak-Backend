<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-extrabold text-ink">المنتجات</h1>

        {{-- Search --}}
        <div class="relative w-full sm:w-80">
            <input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="بتدور على إيه؟"
                class="w-full rounded-full border border-sand bg-white py-3 pr-11 pl-4 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none"
            >
            <svg class="absolute right-4 top-1/2 size-4 -translate-y-1/2 text-mocha/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/></svg>
        </div>
    </div>

    {{-- Category pills --}}
    <div class="mt-5 flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
        <button
            type="button"
            wire:click="selectCategory(null)"
            @class([
                'shrink-0 rounded-full px-5 py-2 text-sm font-bold transition',
                'bg-brand text-white' => ! $categoryId,
                'border border-sand bg-white text-mocha hover:border-brand/40' => $categoryId,
            ])
        >الكل</button>
        @foreach ($categories as $category)
            <button
                type="button"
                wire:click="selectCategory({{ $category->id }})"
                @class([
                    'shrink-0 rounded-full px-5 py-2 text-sm font-bold transition',
                    'bg-brand text-white' => $categoryId === $category->id,
                    'border border-sand bg-white text-mocha hover:border-brand/40' => $categoryId !== $category->id,
                ])
            >{{ $category->name }}</button>
        @endforeach
    </div>

    {{-- Grid --}}
    @if ($products->isEmpty())
        <div class="mt-16 flex flex-col items-center text-center">
            <span class="flex size-20 items-center justify-center rounded-full bg-blush text-4xl">🔍</span>
            <p class="mt-4 text-lg font-extrabold text-ink">
                {{ trim((string) $search) !== '' ? 'ملقيناش حاجة باسم "'.$search.'"' : 'مفيش منتجات هنا لسه' }}
            </p>
            <p class="mt-1 text-sm text-mocha">جرب قسم تاني أو كلمة تانية 🎁</p>
        </div>
    @else
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4" wire:loading.class="opacity-50">
            @foreach ($products as $product)
                <x-web.product-card :product="$product" wire:key="p-{{ $product->id }}" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>

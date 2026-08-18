@php $why = $o['why'] ?? []; @endphp

<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20">
        <div class="reveal text-center">
            @if (!empty($why['badge']))
            <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $why['badge'] }}</span>
            @endif
        </div>

        @if (count($why['items'] ?? []))
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($why['items'] as $item)
            <article class="reveal rounded-[24px] border border-blue-100 bg-white p-6 text-center shadow-card">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-50 text-blue-600">
                    <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}" class="h-7 w-7"></i>
                </div>
                <h3 class="mt-5 text-base font-black">{{ $item['title'] ?? '' }}</h3>
                @if (!empty($item['description']))
                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                @endif
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>
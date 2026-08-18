@php $reis = $o['reis'] ?? []; @endphp

<section class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20">
    <div class="reveal text-center">
        @if (!empty($reis['badge']))
        <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $reis['badge'] }}</span>
        @endif
    </div>

    @if (count($reis['items'] ?? []))
    <div class="timeline-track relative mt-10 flex gap-6 overflow-x-auto">
        @foreach ($reis['items'] as $item)
        <div class="timeline-item reveal relative text-center">
            <div class="relative z-10 mx-auto grid h-14 w-14 place-items-center rounded-full border-4 border-blue-100 bg-white text-blue-600 shadow-card">
                <i data-lucide="{{ $item['icon'] ?? 'star' }}" class="h-6 w-6"></i>
            </div>
            <p class="mt-5 text-sm font-black">{{ $item['year'] ?? '' }}</p>
            @if (!empty($item['title']))
            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $item['title'] }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</section>
@php $werkplaats = $o['werkplaats'] ?? []; @endphp

<section class="mx-auto max-w-7xl px-5 pb-14 sm:px-8 lg:px-10 lg:pb-20">
    @if (!empty($werkplaats['badge']))
    <div class="reveal">
        <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $werkplaats['badge'] }}</span>
    </div>
    @endif

    @if (count($werkplaats['items'] ?? []))
    <div class="werkplaats-track mt-6" data-werkplaats-track>
        @foreach ($werkplaats['items'] as $item)
        <article class="werkplaats-card image-card relative overflow-hidden rounded-[22px] bg-slate-900 shadow-card">
            @if (!empty($item['image']))
            <img src="{{ asset('assets/img/landing/' . basename($item['image'] ?? '')) }}" alt="{{ $item['title'] ?? '' }}"
                 class="h-64 w-full object-cover" loading="lazy" decoding="async">
            @endif
            <div class="werkplaats-card-overlay absolute inset-0"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <p class="flex items-center gap-2 text-sm font-black text-white">
                    <i data-lucide="{{ $item['icon'] ?? 'wrench' }}" class="h-4 w-4 text-blue-400"></i>
                    {{ $item['title'] ?? '' }}
                </p>
            </div>
        </article>
        @endforeach
    </div>
    @endif
</section>
@php $hero = $o['hero'] ?? []; @endphp

<section class="overons-hero relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0">
        @if (!empty($hero['hero_image']))
        <img src="{{ asset($hero['hero_image']) }}" alt="{{ $hero['hero_image_alt'] ?? 'Professionele reparatiewerkplaats' }}"
             class="h-full w-full object-cover" fetchpriority="high" decoding="async">
        @endif
        <div class="overons-hero-overlay absolute inset-0"></div>
    </div>

    <div class="relative mx-auto grid min-h-[620px] max-w-7xl items-center px-5 py-16 sm:px-8 lg:grid-cols-[.9fr_1.1fr] lg:px-10">
        <div class="reveal max-w-2xl">
            @if (!empty($hero['badge']))
            <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-400">{{ $hero['badge'] }}</span>
            @endif

            <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                {{ $hero['title_line1'] ?? '' }}
                @if (!empty($hero['title_line2']))
                <span class="mt-2 block text-blue-500">{{ $hero['title_line2'] }}</span>
                @endif
            </h1>

            @if (!empty($hero['description']))
            <p class="mt-6 max-w-xl text-base leading-8 text-blue-100 sm:text-lg">{{ $hero['description'] }}</p>
            @endif

            @if (count($hero['trust_points'] ?? []))
            <div class="mt-7 flex flex-wrap gap-4 text-sm font-semibold text-blue-100">
                @foreach ($hero['trust_points'] as $tp)
                <span class="inline-flex items-center gap-2">
                    <i data-lucide="{{ $tp['icon'] ?? 'circle-check' }}" class="h-5 w-5 text-blue-400"></i>
                    {{ $tp['label'] ?? '' }}
                </span>
                @endforeach
            </div>
            @endif

            @if (!empty($hero['rating_value']) || !empty($hero['rating_count']))
            <a href="{{ $hero['rating_url'] ?? '#' }}" target="_blank" rel="noopener"
               class="overons-rating mt-7 inline-flex items-center gap-4 rounded-2xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur-md">
                <div class="text-3xl font-black">G</div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black">{{ $hero['rating_value'] ?? '4.9' }} {{ $hero['rating_scale'] ?? 'uit 5' }}</span>
                        <div class="flex text-amber-400">
                            @for ($i = 0; $i < 5; $i++)
                            <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-blue-100">{{ $hero['rating_count'] ?? '120+ reviews' }}</p>
                </div>
            </a>
            @endif
        </div>
    </div>
</section>
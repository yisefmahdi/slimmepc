@php $hero = $t['hero'] ?? []; @endphp

<section class="hero-background relative overflow-hidden px-4 pb-16 pt-16 sm:px-6 lg:px-8 lg:pb-20 lg:pt-20">
    <div class="dot-pattern pointer-events-none absolute right-0 top-0 h-56 w-56 opacity-35"></div>
    <div class="pointer-events-none absolute -bottom-48 -left-36 h-[420px] w-[420px] rounded-full border border-blue-200/50"></div>
    <div class="pointer-events-none absolute -right-24 top-5 h-[390px] w-[390px] rounded-full bg-blue-300/20 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[1fr_0.9fr]">
        <div>
            @if (!empty($hero['badge']))
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/90 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.1em] text-blue-600 shadow-soft backdrop-blur">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white">
                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                </span>
                {{ $hero['badge'] }}
            </div>
            @endif

            <h1 class="mt-7 max-w-2xl text-4xl font-black leading-[1.04] tracking-[-0.055em] text-slate-950 sm:text-5xl lg:text-6xl xl:text-6xl">
                {{ $hero['title_line1'] ?? 'Tarieven zonder' }}
                @if (!empty($hero['title_line2']))
                <span class="block text-blue-600">{{ $hero['title_line2'] }}</span>
                @endif
            </h1>

            @if (!empty($hero['description']))
            <p class="mt-6 max-w-xl text-base font-medium leading-8 text-slate-600 sm:text-lg">
                {{ $hero['description'] }}
            </p>
            @endif

            @php
                $btn1Url = !empty($hero['button1_url']) ? $hero['button1_url'] : '#tarieven';
                $btn2Url = !empty($hero['button2_url']) ? $hero['button2_url'] : '/reparatie-aanmelden';
            @endphp

            @if (!empty($hero['button1_text']) || !empty($hero['button2_text']))
            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                @if (!empty($hero['button1_text']))
                <a href="{{ $btn1Url }}"
                   class="group inline-flex min-h-[56px] items-center justify-center gap-3 rounded-xl bg-blue-600 px-7 text-sm font-extrabold text-white shadow-button transition duration-300 hover:-translate-y-1 hover:bg-blue-700">
                    <i data-lucide="tag" class="h-5 w-5"></i>
                    {{ $hero['button1_text'] }}
                    <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                </a>
                @endif

                @if (!empty($hero['button2_text']))
                <a href="{{ $btn2Url }}"
                   class="group inline-flex min-h-[56px] items-center justify-center gap-3 rounded-xl border border-blue-100 bg-white px-7 text-sm font-extrabold text-slate-900 shadow-soft transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:text-blue-600">
                    <i data-lucide="wrench" class="h-5 w-5 text-blue-600"></i>
                    {{ $hero['button2_text'] }}
                    <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                </a>
                @endif
            </div>
            @endif

            @php $trustPoints = $hero['trust_points'] ?? []; @endphp
            @if (count($trustPoints))
            <div class="mt-9 flex flex-wrap gap-x-7 gap-y-4">
                @foreach ($trustPoints as $tp)
                <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-blue-300 text-blue-600">
                        <i data-lucide="{{ $tp['icon'] ?? 'check' }}" class="h-4 w-4"></i>
                    </span>
                    {{ $tp['label'] ?? '' }}
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if (!empty($hero['hero_image']))
        <div class="relative flex min-h-[390px] items-center justify-center lg:min-h-[430px]">
            <div class="absolute left-1/2 top-1/2 h-[380px] w-[380px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-300/25 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 h-[340px] w-[340px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-100/80"></div>
            <img src="{{ asset($hero['hero_image']) }}" alt="{{ $hero['hero_image_alt'] ?? 'Tarieven voor computerreparatie' }}"
                 class="hero-floating relative z-10 max-h-[430px] w-full max-w-[570px] object-contain drop-shadow-2xl"
                 fetchpriority="high" decoding="async">
        </div>
        @endif
    </div>
</section>
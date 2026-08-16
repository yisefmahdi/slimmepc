@php $hero = $p['hero'] ?? []; @endphp

@php
    $waNumber = preg_replace('/\D+/', '', (string) ($hero['whatsapp_number'] ?? ''));
    $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber : '#';
@endphp

<section class="relative overflow-hidden bg-gradient-to-br from-[#edf6ff] via-white to-[#f4f9ff]">
    <div class="pointer-events-none absolute -right-24 -top-24 h-[420px] w-[420px] rounded-full bg-blue-300/20 blur-[110px]"></div>
    <div class="pointer-events-none absolute left-[35%] top-[30%] h-[260px] w-[260px] rounded-full bg-cyan-200/20 blur-[90px]"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                @if (!empty($hero['badge']))
                <p class="mb-4 text-sm font-bold uppercase tracking-wide text-blue-600">{{ $hero['badge'] }}</p>
                @endif

                <h1 class="text-4xl font-black leading-[1.05] text-[#0b1f4d] sm:text-5xl lg:text-6xl">
                    {{ $hero['title_line1'] ?? '' }}
                    @if (!empty($hero['title_line2']))
                    <br><span class="text-blue-600">{{ $hero['title_line2'] }}</span>
                    @endif
                </h1>

                @if (!empty($hero['description']))
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-700">{{ $hero['description'] }}</p>
                @endif

                @if (count($hero['trust_points'] ?? []))
                <div class="mt-7 space-y-3 text-sm text-slate-700">
                    @foreach ($hero['trust_points'] as $tp)
                    <div class="flex items-center gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white">
                            <i data-lucide="{{ $tp['icon'] ?? 'check' }}" class="h-3 w-3"></i>
                        </span>
                        <span>{{ $tp['label'] ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if (!empty($hero['button1_text']) || !empty($hero['button2_text']))
                <div class="mt-8 flex flex-wrap gap-4">
                    @if (!empty($hero['button1_text']))
                    <a href="#contactformulier"
                       class="inline-flex items-center gap-3 rounded-xl bg-blue-600 px-6 py-3.5 font-bold text-white shadow-lg shadow-blue-600/20 transition duration-300 hover:-translate-y-0.5 hover:bg-blue-500">
                        {{ $hero['button1_text'] }}
                        <span aria-hidden="true">→</span>
                    </a>
                    @endif

                    @if (!empty($hero['button2_text']))
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-3 rounded-xl border border-blue-200 bg-white px-6 py-3.5 font-bold text-blue-700 transition duration-300 hover:border-blue-400 hover:bg-blue-50">
                        {{ $hero['button2_text'] }}
                        <span aria-hidden="true">↗</span>
                    </a>
                    @endif
                </div>
                @endif
            </div>

            @if (!empty($hero['hero_image']))
            <div class="relative flex min-h-[360px] items-center justify-center lg:justify-end">
                <div class="absolute h-[200px] w-[200px] rounded-full bg-blue-300/20 blur-[90px]"></div>
                <img src="{{ asset($hero['hero_image']) }}" alt="{{ $hero['hero_image_alt'] ?? 'Contact met Slimme-PC' }}"
                     class="relative z-10 w-full max-w-[600px] object-contain"
                     fetchpriority="high" decoding="async">
            </div>
            @endif
        </div>
    </div>
</section>
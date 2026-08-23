@php
    $pricing = $t['pricing'] ?? [];
    $extra = $t['extra'] ?? [];
    $categories = $pricing['categories'] ?? [];
    $accordions = $extra['accordions'] ?? [];
    $trustCards = $extra['trust_cards'] ?? [];

    $cardClasses = [
        'flex items-start gap-4 border-b border-slate-100 p-6 md:border-r xl:border-b-0',
        'flex items-start gap-4 border-b border-slate-100 p-6 xl:border-b-0 xl:border-r',
        'flex items-start gap-4 border-b border-slate-100 p-6 md:border-b-0 md:border-r',
        'flex items-start gap-4 p-6',
    ];
@endphp

<section id="tarieven" class="pricing-background relative overflow-hidden px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
    <div class="pointer-events-none absolute -right-32 top-40 h-96 w-96 rounded-full bg-blue-200/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="text-center">
            <h2 class="text-3xl font-black tracking-[-0.04em] text-slate-950 sm:text-4xl">
                {{ $pricing['heading'] ?? 'Kies je apparaat of dienst' }}
            </h2>
            @if (!empty($pricing['description']))
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                {{ $pricing['description'] }}
            </p>
            @endif
        </div>

        @if (count($categories))
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($categories as $i => $cat)
            <button type="button" data-service-index="{{ $i }}"
                    class="service-tab {{ $i === 0 ? 'active' : '' }} flex min-h-[98px] items-center justify-center gap-3 rounded-2xl border border-blue-100 bg-white px-5 text-sm font-extrabold text-slate-800 shadow-soft">
                <span class="service-tab-icon flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition">
                    <i data-lucide="{{ $cat['icon'] ?? 'circle' }}" class="h-7 w-7"></i>
                </span>
                <span class="text-left">{{ $cat['label'] ?? '' }}</span>
            </button>
            @endforeach
        </div>

        <div class="mt-7">
            @foreach ($categories as $i => $cat)
            <section data-service-panel="{{ $i }}"
                     class="{{ $i === 0 ? '' : 'hidden' }} overflow-hidden rounded-[28px] border border-blue-100 bg-white shadow-panel">
                <div class="grid lg:grid-cols-[0.72fr_1.28fr]">
                    <div class="relative overflow-hidden border-b border-blue-100 bg-gradient-to-br from-white via-blue-50/50 to-blue-100/60 p-7 sm:p-9 lg:border-b-0 lg:border-r">
                        <div class="absolute -bottom-10 -left-8 h-48 w-48 rounded-full bg-blue-200/30 blur-3xl"></div>
                        <div class="relative z-10">
                            <div class="inline-flex rounded-lg bg-blue-50 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.07em] text-blue-600">
                                {{ $cat['label'] ?? '' }}
                            </div>
                            <h2 class="mt-5 text-3xl font-black tracking-[-0.045em] text-slate-950 sm:text-4xl">
                                {{ $cat['title'] ?? '' }}
                            </h2>
                            @if (!empty($cat['description']))
                            <p class="mt-5 max-w-sm text-sm font-medium leading-7 text-slate-600 sm:text-base">
                                {{ $cat['description'] }}
                            </p>
                            @endif
                            @if (!empty($cat['image']))
                            <div class="relative mt-8 flex min-h-[275px] items-center justify-center">
                                <div class="absolute h-48 w-48 rounded-full bg-blue-300/25 blur-3xl"></div>
                                <img src="{{ asset('assets/img/landing/' . basename($cat['image'] ?? '')) }}" alt="{{ $cat['title'] ?? '' }}"
                                     class="device-floating relative z-10 max-h-[260px] w-full object-contain"
                                     loading="lazy" decoding="async">
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 sm:p-9">
                        @if (count($cat['prices'] ?? []))
                        <div class="divide-y divide-slate-100">
                            @foreach ($cat['prices'] as $price)
                            <div class="price-row group flex items-center gap-4 px-2 py-5">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                                    <i data-lucide="{{ $price['icon'] ?? 'circle' }}" class="h-5 w-5"></i>
                                </div>
                                <p class="min-w-0 flex-1 text-sm font-bold text-slate-900 sm:text-base">
                                    {{ $price['title'] ?? '' }}
                                </p>
                                <div class="shrink-0 text-right">
                                    @if (!empty($price['prefix']))
                                    <span class="mr-1 text-xs font-medium text-slate-500 sm:text-sm">{{ $price['prefix'] }}</span>
                                    @endif
                                    <span class="{{ ($price['price'] ?? '') === 'Offerte' ? 'text-xl' : 'text-2xl' }} font-black tracking-[-0.035em] text-blue-600">
                                        {{ $price['price'] ?? '' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if (!empty($cat['notice']))
                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/80 p-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-blue-600 shadow-sm">
                                <i data-lucide="info" class="h-5 w-5"></i>
                            </div>
                            <p class="text-xs font-medium leading-6 text-slate-600 sm:text-sm">{{ $cat['notice'] }}</p>
                        </div>
                        @endif

                        <a href="/reparatie-aanmelden"
                           class="group mt-5 inline-flex min-h-[54px] items-center justify-center gap-3 rounded-xl bg-blue-600 px-7 text-sm font-extrabold text-white shadow-button transition duration-300 hover:-translate-y-0.5 hover:bg-blue-700">
                            <i data-lucide="wrench" class="h-5 w-5"></i>
                            Reparatie aanmelden
                            <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </section>
            @endforeach
        </div>
        @endif

        @if (count($accordions))
        <div class="mt-5 space-y-4">
            @foreach ($accordions as $acc)
                @php
                    $isGreen = ($acc['accent'] ?? 'blue') === 'green';
                    $iconWrap = $isGreen ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600';
                    $borderColor = $isGreen ? 'border-green-100' : 'border-blue-100';
                    $priceColor = $isGreen ? 'text-green-600' : 'text-blue-600';
                    $rowBg = $isGreen ? 'bg-green-50/20' : 'bg-blue-50/30';
                @endphp
                <article class="accordion-item rounded-[20px] border border-blue-100 bg-white p-5 shadow-soft sm:p-6">
                    <button type="button" class="accordion-trigger flex w-full items-center gap-5 text-left">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $iconWrap }}">
                            <i data-lucide="{{ $acc['icon'] ?? 'circle' }}" class="h-7 w-7"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-lg font-black tracking-[-0.025em] sm:text-xl">{{ $acc['title'] ?? '' }}</span>
                            @if (!empty($acc['description']))
                            <span class="mt-1 block text-xs leading-5 text-slate-500 sm:text-sm">{{ $acc['description'] }}</span>
                            @endif
                        </span>
                        <span class="accordion-chevron flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-slate-900">
                            <i data-lucide="chevron-down" class="h-5 w-5"></i>
                        </span>
                    </button>

                    <div class="accordion-content">
                        @if (count($acc['prices'] ?? []))
                        <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border {{ $borderColor }} {{ $rowBg }}">
                            @foreach ($acc['prices'] as $row)
                            <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-bold">{{ $row['title'] ?? '' }}</p>
                                    @if (!empty($row['description']))
                                    <p class="mt-1 text-xs text-slate-500">{{ $row['description'] }}</p>
                                    @endif
                                </div>
                                <strong class="{{ $priceColor }}">{{ $row['price'] ?? '' }}</strong>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        @endif

        @if (count($trustCards))
        <div class="mt-5 grid overflow-hidden rounded-[22px] border border-blue-100 bg-white shadow-soft md:grid-cols-2 xl:grid-cols-4">
            @foreach ($trustCards as $i => $card)
            <article class="{{ $cardClasses[$i] ?? 'flex items-start gap-4 border-b border-slate-100 p-6 md:border-r xl:border-b-0' }}">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                    <i data-lucide="{{ $card['icon'] ?? 'shield-check' }}" class="h-7 w-7"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold">{{ $card['title'] ?? '' }}</h3>
                    @if (!empty($card['description']))
                    <p class="mt-2 text-xs leading-5 text-slate-500">{{ $card['description'] }}</p>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>
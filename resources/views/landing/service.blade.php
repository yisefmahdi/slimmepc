@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        {{-- Hero --}}
        @php
            $h = $s['hero'] ?? [];
            $usp = $h['usp'] ?? [];
        @endphp
        <section id="hero" class="relative overflow-hidden bg-brand-gradient text-white">
            <div class="absolute inset-0 opacity-20"
                style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,.4), transparent 40%), radial-gradient(circle at 80% 0%, rgba(255,255,255,.25), transparent 35%);">
            </div>
            <div class="container relative mx-auto grid max-w-7xl gap-10 px-4 py-16 md:grid-cols-2 md:py-24">
                <div>
                    @if (!empty($h['badge']))
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-sm font-semibold">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>{{ $h['badge'] }}
                        </span>
                    @endif
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight md:text-5xl">
                        {{ $h['title1'] ?? '' }}<br>
                        {{ $h['title2'] ?? '' }}
                        @if (!empty($h['title3']))
                            <span class="gradient-text">{{ $h['title3'] }}</span>
                        @endif
                    </h1>
                    @if (!empty($h['description']))
                        <p class="mt-5 max-w-xl text-lg text-white/85">{{ $h['description'] }}</p>
                    @endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}"
                            class="rounded-full bg-white px-7 py-3 font-semibold text-brand-heading shadow-lg transition hover:bg-white/90">
                            Reparatie aanvragen
                        </a>
                        <a href="tel:0850801167"
                            class="rounded-full border border-white/40 px-7 py-3 font-semibold text-white transition hover:bg-white/10">
                            Bel 085 080 1167
                        </a>
                    </div>

                    @if (!empty($usp) && is_array($usp))
                        <div class="mt-10 grid grid-cols-2 gap-4">
                            @foreach ($usp as $u)
                                <div class="flex items-start gap-3 rounded-2xl bg-white/10 p-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                        <i data-lucide="{{ $u['icon'] ?? 'check' }}" class="h-5 w-5"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold leading-tight">{{ $u['title'] ?? '' }}</p>
                                        @if (!empty($u['subtitle']))
                                            <p class="text-sm text-white/75">{{ $u['subtitle'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="relative flex items-center justify-center">
                    @if (!empty($h['image']))
                        <img src="{{ asset($h['image']) }}" alt="{{ $h['title1'] ?? 'Reparatie' }}"
                            class="max-h-[420px] w-auto rounded-3xl shadow-2xl">
                    @else
                        <div class="flex h-72 w-72 items-center justify-center rounded-3xl bg-white/10">
                            <i data-lucide="cpu" class="h-20 w-20 opacity-60"></i>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Wat is er mis? --}}
        @php
            $p = $s['problems'] ?? [];
            $problems = $p['items'] ?? [];
        @endphp
        <section id="problems" class="container mx-auto max-w-7xl px-4 py-16 md:py-20">
            @if (!empty($p['title']))
                <h2 class="text-3xl font-extrabold text-brand-heading md:text-4xl">{{ $p['title'] }}</h2>
            @endif
            @if (!empty($p['subtitle']))
                <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ $p['subtitle'] }}</p>
            @endif
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($problems as $item)
                    <div class="group flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-xl">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-gradient text-white">
                            <i data-lucide="{{ $item['icon'] ?? 'alert-triangle' }}" class="h-6 w-6"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-brand-heading">{{ $item['title'] ?? '' }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Onze specialiteit (video) --}}
        @php
            $sp = $s['speciality'] ?? [];
            $specList = $sp['list'] ?? [];
        @endphp
        <section id="speciality" class="bg-slate-50">
            <div class="container mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 md:grid-cols-2 md:py-20">
                <div>
                    @if (!empty($sp['badge']))
                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-1.5 text-sm font-semibold text-blue-700">
                            <i data-lucide="sparkles" class="h-4 w-4"></i>{{ $sp['badge'] }}
                        </span>
                    @endif
                    <h2 class="mt-5 text-3xl font-extrabold text-brand-heading md:text-4xl">
                        {{ $sp['title1'] ?? '' }} <span class="gradient-text">{{ $sp['title2'] ?? '' }}</span>
                    </h2>
                    @if (!empty($sp['description']))
                        <p class="mt-4 max-w-xl text-lg text-slate-600">{{ $sp['description'] }}</p>
                    @endif
                    <ul class="mt-6 space-y-3">
                        @foreach ($specList as $li)
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-brand-gradient text-white">
                                    <i data-lucide="{{ $li['icon'] ?? 'check' }}" class="h-4 w-4"></i>
                                </span>
                                <span class="font-medium text-brand-heading">{{ $li['title'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    @if (!empty($sp['video']))
                        <video class="w-full rounded-3xl shadow-xl" controls playsinline preload="metadata">
                            <source src="{{ asset($sp['video']) }}" type="video/mp4">
                        </video>
                    @else
                        <div class="flex aspect-video w-full items-center justify-center rounded-3xl bg-brand-gradient text-white">
                            <i data-lucide="play" class="h-14 w-14"></i>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Professionele uitrusting --}}
        @php
            $eq = $s['equipment'] ?? [];
            $equip = $eq['items'] ?? [];
        @endphp
        <section id="equipment" class="container mx-auto max-w-7xl px-4 py-16 md:py-20">
            <h2 class="text-3xl font-extrabold text-brand-heading md:text-4xl">Professionele uitrusting</h2>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($equip as $item)
                    <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-card">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                            <i data-lucide="{{ $item['icon'] ?? 'wrench' }}" class="h-7 w-7"></i>
                        </span>
                        <h3 class="mt-4 font-bold text-brand-heading">{{ $item['title'] ?? '' }}</h3>
                        @if (!empty($item['subtitle']))
                            <p class="mt-1 text-sm text-slate-500">{{ $item['subtitle'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Een reparatie van dichtbij --}}
        @php
            $ex = $s['example'] ?? [];
        @endphp
        <section id="example" class="bg-slate-50">
            <div class="container mx-auto max-w-7xl px-4 py-16 md:py-20">
                @if (!empty($ex['title']))
                    <h2 class="text-3xl font-extrabold text-brand-heading md:text-4xl">{{ $ex['title'] }}</h2>
                @endif
                @if (!empty($ex['subtitle']))
                    <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ $ex['subtitle'] }}</p>
                @endif
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach (['before' => 'Vóór', 'diagnose' => 'Diagnose', 'after' => 'Na reparatie'] as $key => $label)
                        <div class="overflow-hidden rounded-2xl bg-white shadow-card">
                            @if (!empty($ex[$key . '_image']))
                                <img src="{{ asset($ex[$key . '_image']) }}" alt="{{ $ex[$key . '_label'] ?? $label }}"
                                    class="h-44 w-full object-cover">
                            @else
                                <div class="flex h-44 w-full items-center justify-center bg-slate-100 text-slate-400">
                                    <i data-lucide="image" class="h-10 w-10"></i>
                                </div>
                            @endif
                            <div class="p-5">
                                <span class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    {{ $ex[$key . '_label'] ?? $label }}
                                </span>
                                @if (!empty($ex[$key . '_text']))
                                    <p class="mt-2 text-sm text-slate-600">{{ $ex[$key . '_text'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if (!empty($ex['tested_title']))
                    <div class="mt-8 flex flex-wrap items-center gap-4 rounded-2xl bg-brand-gradient p-6 text-white">
                        <i data-lucide="check-circle" class="h-8 w-8"></i>
                        <div>
                            <p class="text-lg font-bold">{{ $ex['tested_title'] }}</p>
                            @if (!empty($ex['tested_text']))
                                <p class="text-white/80">{{ $ex['tested_text'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Andere reparaties --}}
        @php
            $o = $s['other'] ?? [];
            $others = $o['items'] ?? [];
        @endphp
        <section id="other" class="container mx-auto max-w-7xl px-4 py-16 md:py-20">
            @if (!empty($o['title']))
                <h2 class="text-3xl font-extrabold text-brand-heading md:text-4xl">{{ $o['title'] }}</h2>
            @endif
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($others as $item)
                    <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-card">
                        @if (!empty($item['image']))
                            <img src="{{ asset('assets/img/landing/' . $item['image']) }}" alt="{{ $item['title'] ?? '' }}"
                                class="h-14 w-14 rounded-xl object-cover">
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i data-lucide="tool" class="h-6 w-6"></i>
                            </span>
                        @endif
                        <div>
                            <h3 class="font-bold text-brand-heading">{{ $item['title'] ?? '' }}</h3>
                            @if (!empty($item['subtitle']))
                                <p class="text-sm text-slate-500">{{ $item['subtitle'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- FAQ + CTA --}}
        @php
            $f = $s['faq'] ?? [];
            $faqs = $f['items'] ?? [];
        @endphp
        <section id="faq" class="bg-slate-50">
            <div class="container mx-auto max-w-4xl px-4 py-16 md:py-20">
                @if (!empty($f['title']))
                    <h2 class="text-center text-3xl font-extrabold text-brand-heading md:text-4xl">{{ $f['title'] }}</h2>
                @endif
                <style>
                    .faq details[open] summary .faq-chev { transform: rotate(180deg); }
                </style>
                <div class="mt-10 space-y-3">
                    @foreach ($faqs as $qa)
                        <details class="faq overflow-hidden rounded-2xl bg-white shadow-card">
                            <summary class="flex w-full cursor-pointer list-none items-center justify-between gap-4 px-6 py-5 font-semibold text-brand-heading">
                                <span>{{ $qa['question'] ?? '' }}</span>
                                <i data-lucide="chevron-down" class="faq-chev h-5 w-5 shrink-0 transition-transform"></i>
                            </summary>
                            <p class="px-6 pb-5 text-slate-600">{{ $qa['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        @php
            $ctaBg = $f['cta_bg'] ?? '';
        @endphp
        <section class="relative overflow-hidden text-white"
            style="@if (!empty($ctaBg)) background-image: linear-gradient(rgba(29,78,216,.85), rgba(29,78,216,.85)), url('{{ asset($ctaBg) }}'); background-size: cover; background-position: center; @else background: var(--brand-gradient-from); @endif">
            <div class="container mx-auto max-w-5xl px-4 py-16 text-center md:py-20">
                @if (!empty($f['cta_title']))
                    <h2 class="text-3xl font-extrabold md:text-4xl">{{ $f['cta_title'] }}</h2>
                @endif
                @if (!empty($f['cta_subtitle']))
                    <p class="mt-4 text-lg text-white/85">{{ $f['cta_subtitle'] }}</p>
                @endif
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('contact') }}"
                        class="rounded-full bg-white px-7 py-3 font-semibold text-brand-heading shadow-lg transition hover:bg-white/90">
                        {{ $f['cta_button'] ?? 'Reparatie aanvragen' }}
                    </a>
                    @if (!empty($f['cta_phone']))
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $f['cta_phone']) }}"
                            class="rounded-full border border-white/40 px-7 py-3 font-semibold transition hover:bg-white/10">
                            {{ $f['cta_phone'] }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Onderaan USP-balk --}}
        @php
            $b = $s['bottom'] ?? [];
            $bottom = $b['items'] ?? [];
        @endphp
        <section id="bottom" class="bg-brand-gradient text-white">
            <div class="container mx-auto grid max-w-7xl gap-6 px-4 py-12 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($bottom as $item)
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20">
                            <i data-lucide="{{ $item['icon'] ?? 'check' }}" class="h-5 w-5"></i>
                        </span>
                        <div>
                            <p class="font-semibold leading-tight">{{ $item['title'] ?? '' }}</p>
                            @if (!empty($item['subtitle']))
                                <p class="text-sm text-white/75">{{ $item['subtitle'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
@endsection

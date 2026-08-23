@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="bg-slate-50 text-slate-900 overflow-hidden">

        {{-- HERO --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-[#edf6ff] via-[#f7fbff] to-white">

            <div class="absolute -top-24 right-[15%] h-[340px] w-[340px] rounded-full bg-blue-300/20 blur-[100px]"></div>
            <div class="absolute left-[35%] top-[30%] h-[260px] w-[260px] rounded-full bg-cyan-200/20 blur-[100px]"></div>

            <div class="relative mx-auto max-w-7xl px-6 lg:px-8 py-14 lg:py-20">

                <div class="grid lg:grid-cols-2 gap-12 items-center">

                    <div>

                        <p class="mb-4 text-sm font-bold uppercase tracking-wide text-blue-600">
                            {{ $s['hero']['badge'] ?? 'MacBook & iMac reparatie · Apeldoorn' }}
                        </p>

                        <h1 class="text-[30px] sm:text-[52px] lg:text-[60px] font-black leading-[1.05] text-[#0b1f4d]">
                            {{ $s['hero']['title1'] ?? 'Je Mac verdient' }}<br>
                            {{ $s['hero']['title2'] ?? 'meer dan een' }} <span class="text-blue-600">{{ $s['hero']['title3'] ?? 'snelle fix.' }}</span>
                        </h1>

                        <p class="mt-5 max-w-xl text-[16px] sm:text-[20px] lg:text-[24px] leading-relaxed text-slate-700">
                            {{ $s['hero']['description'] ?? '' }}
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3 sm:gap-4">
                            <a href="/reparatie-aanmelden"
                               class="inline-flex items-center gap-2 sm:gap-5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5">
                                Mac laten repareren <span>→</span>
                            </a>

                            <a href="#reparaties"
                               class="inline-flex items-center gap-2 sm:gap-4 border border-blue-300 bg-white hover:bg-blue-50 text-blue-700 font-bold rounded-xl px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] transition">
                                Bekijk reparaties <span>→</span>
                            </a>
                        </div>

                        <div class="mt-8 grid sm:grid-cols-2 gap-x-8 gap-y-3 text-sm text-slate-700">
                            @foreach ($s['hero']['trust'] ?? [] as $t)
                                <div class="flex items-center gap-3">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs text-white">✓</span>
                                    {{ $t['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="relative mt-10 lg:mt-0 flex justify-center min-h-[430px]">

                        <div class="absolute h-[360px] w-[360px] rounded-full bg-blue-300/20 blur-[90px]"></div>

                        <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/imac-macbook2.png') }}"
                             alt="MacBook en iMac reparatie"
                             class="relative z-10 w-full max-w-[340px] sm:max-w-none sm:w-[540px] lg:w-[580px] object-contain drop-shadow-2xl">
                    </div>

                </div>
            </div>
        </section>


        {{-- DEVICES --}}
        <section class="px-6 py-14">

            <div class="mx-auto max-w-7xl rounded-3xl border border-blue-100 bg-white p-7 shadow-sm lg:p-9">

                <div class="text-center">
                    <h2 class="text-3xl font-black text-[#0b1f4d]">
                        {{ $s['devices']['title'] ?? 'Welke Mac kunnen we voor je repareren?' }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ $s['devices']['subtitle'] ?? '' }}
                    </p>
                </div>

                <div class="mt-8 grid md:grid-cols-3 gap-5">
                    @foreach ($s['devices']['items'] ?? [] as $di)
                        <div class="group rounded-2xl border border-blue-100 bg-gradient-to-br from-white to-blue-50/40 p-5 transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl">
                            <div class="flex items-center gap-5">
                                <img src="{{ asset($di['image'] ?? '') }}" alt="{{ $di['name'] ?? '' }}" class="h-32 w-40 object-contain">
                                <div>
                                    <h3 class="text-xl font-black text-[#0b1f4d]">{{ $di['name'] ?? '' }}</h3>
                                    <p class="mt-2 text-sm text-slate-600">{{ $di['sub1'] ?? '' }}</p>
                                    <p class="text-sm text-slate-500">{{ $di['sub2'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>


        {{-- PROBLEMS + COMPONENT REPAIR --}}
        <section id="reparaties" class="px-6 pb-14">

            <div class="mx-auto max-w-7xl rounded-3xl border border-blue-100 bg-gradient-to-b from-[#f7fbff] to-white p-7 lg:p-9">

                <div class="text-center">
                    <h2 class="text-3xl font-black text-[#0b1f4d]">
                        {{ $s['problems']['title'] ?? 'Wat is er mis met je Mac?' }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ $s['problems']['subtitle'] ?? '' }}
                    </p>
                </div>

                <div class="mt-8 grid lg:grid-cols-[1.7fr_1fr] gap-6">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($s['problems']['items'] ?? [] as $pi)
                            <div class="rounded-2xl border border-blue-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-1 hover:border-blue-400 hover:shadow-lg">
                                <div class="text-4xl text-blue-600">{{ $pi['emoji'] ?? '' }}</div>
                                <h3 class="mt-3 font-black text-[#0b1f4d]">{{ $pi['title'] ?? '' }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $pi['subtitle'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#dcebff] via-[#edf6ff] to-white p-7">
                        <div class="absolute -bottom-14 -left-16 h-56 w-56 rounded-full bg-blue-300/20 blur-3xl"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-black text-[#0b1f4d]">
                                {{ $s['problems']['component_title'] ?? 'Niet alleen onderdelen vervangen.' }}
                            </h3>
                            <p class="mt-3 text-sm leading-relaxed text-slate-700">
                                {{ $s['problems']['component_text'] ?? '' }}
                            </p>
                            <div class="mt-5 space-y-3 text-sm text-slate-700">
                                @foreach ($s['problems']['component_items'] ?? [] as $comp)
                                    <div class="flex gap-3">
                                        <span class="text-blue-600">✓</span>
                                        {{ $comp['title'] ?? '' }}
                                    </div>
                                @endforeach
                            </div>
                            <a href="#"
                               class="mt-6 inline-flex items-center gap-3 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-500">
                                Bekijk moederbord reparatie <span>→</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        {{-- PROCESS --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-7xl rounded-3xl border border-blue-100 bg-white px-7 py-10">

                <div class="text-center">
                    <h2 class="text-3xl font-black text-[#0b1f4d]">
                        {{ $s['process']['title'] ?? 'Zo repareren wij je Mac' }}
                    </h2>
                    <p class="mt-2 text-slate-500">
                        {{ $s['process']['subtitle'] ?? '' }}
                    </p>
                </div>

                <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach ($s['process']['items'] ?? [] as $p2)
                        <div class="text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-blue-100 bg-white text-2xl text-blue-600 shadow-md">
                                {{ $p2['emoji'] ?? '' }}
                            </div>
                            <h3 class="mt-4 font-black text-[#0b1f4d]">{{ $p2['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $p2['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- WATER + BATTERY --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-7xl grid lg:grid-cols-2 gap-6">

                <div class="relative min-h-[320px] overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-br from-[#eaf5ff] to-white">
                    <div class="grid md:grid-cols-2 h-full">
                        <div class="relative z-10 p-7 lg:p-9">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl text-blue-600">💧</span>
                                <h2 class="text-2xl font-black text-blue-700">{{ $s['water']['title'] ?? 'Vloeistofschade?' }}</h2>
                            </div>
                            <p class="mt-4 text-sm leading-relaxed text-slate-700">{{ $s['water']['text'] ?? '' }}</p>
                            <a href="#"
                               class="mt-6 inline-flex items-center gap-3 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-500">
                                Mac met waterschade herstellen <span>→</span>
                            </a>
                        </div>
                        <div>
                            <img src="{{ asset($s['water']['image'] ?? 'assets/img/landing/macbook-moederbord-reparatie-utrecht-1920w.webp') }}"
                                 alt="MacBook waterschade"
                                 class="h-48 w-full md:h-full min-h-[280px] object-cover">
                        </div>
                    </div>
                </div>

                <div class="relative min-h-[320px] overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-br from-[#eef6ff] to-white">
                    <div class="grid md:grid-cols-2 h-full">
                        <div class="relative z-10 p-7 lg:p-9">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl text-blue-600">🔋</span>
                                <h2 class="text-2xl font-black text-[#0b1f4d]">{{ $s['battery']['title'] ?? 'Batterij problemen?' }}</h2>
                            </div>
                            <div class="mt-5 space-y-3 text-sm text-slate-700">
                                @foreach ($s['battery']['items'] ?? [] as $bi)
                                    <div class="flex gap-3">
                                        <span class="text-blue-600">✓</span>
                                        {{ $bi['title'] ?? '' }}
                                    </div>
                                @endforeach
                            </div>
                            <a href="#"
                               class="mt-6 inline-flex items-center gap-3 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-500">
                                Nieuwe batterij plaatsen <span>→</span>
                            </a>
                        </div>
                        <div>
                            <img src="{{ asset($s['battery']['image'] ?? 'assets/img/landing/TmDelUWE2PnmwawZ.medium-2.jpeg') }}"
                                 alt="MacBook batterij"
                                 class="h-48 w-full md:h-full min-h-[280px] object-contain p-5">
                        </div>
                    </div>
                </div>

            </div>
        </section>


        {{-- IMAC --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-7xl rounded-3xl border border-blue-100 bg-white p-7 lg:p-9">

                <div class="grid lg:grid-cols-[0.8fr_1fr] gap-10 items-center">

                    <div class="flex justify-center">
                        <img src="{{ asset($s['imac']['image'] ?? 'assets/img/landing/imac-2422-m1-3.webp') }}"
                             alt="iMac reparatie"
                             class="w-full max-w-[430px] object-contain">
                    </div>

                    <div>
                        <h2 class="text-3xl font-black text-[#0b1f4d]">
                            {{ $s['imac']['title'] ?? 'Ook je iMac is bij ons welkom.' }}
                        </h2>
                        <p class="mt-2 text-slate-600">{{ $s['imac']['text'] ?? '' }}</p>

                        <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
                            @foreach ($s['imac']['items'] ?? [] as $ii)
                                <div class="flex gap-3">
                                    <span class="text-blue-600">◉</span>
                                    {{ $ii['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                        <a href="#"
                           class="mt-7 inline-flex items-center gap-3 rounded-lg bg-blue-600 px-5 py-3 font-bold text-white transition hover:bg-blue-500">
                            Plan iMac reparatie <span>→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>


        {{-- WHY --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-7xl rounded-3xl bg-gradient-to-r from-[#eef6ff] via-white to-[#eef6ff] border border-blue-100 p-8">

                <div class="grid lg:grid-cols-2 gap-10 items-center">

                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-blue-600">
                            {{ $s['why']['badge'] ?? 'Slimme-PC Apeldoorn' }}
                        </p>
                        <h2 class="mt-2 text-3xl font-black text-[#0b1f4d]">
                            {{ $s['why']['title'] ?? 'MacBook & iMac: alles onder één dak' }}
                        </h2>
                        <p class="mt-3 max-w-xl text-slate-600">{{ $s['why']['text'] ?? '' }}</p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 text-sm text-slate-700">
                        @foreach ($s['why']['items'] ?? [] as $yi)
                            <div class="flex gap-3">
                                <span class="font-bold text-blue-600">✓</span>
                                {{ $yi['title'] ?? '' }}
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>


        {{-- RECENT REPAIRS --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-7xl">
                <h2 class="text-center text-3xl font-black text-[#0b1f4d]">
                    {{ $s['recent']['title'] ?? 'Recente Mac reparaties' }}
                </h2>

                <div class="mt-8 grid md:grid-cols-3 gap-5">
                    @foreach ($s['recent']['items'] ?? [] as $ri)
                        <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                            <img src="{{ asset($ri['image'] ?? '') }}" alt="{{ $ri['title'] ?? '' }}"
                                 class="h-44 w-full rounded-xl object-cover">
                            <h3 class="mt-4 font-black text-[#0b1f4d]">{{ $ri['title'] ?? '' }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $ri['text'] ?? '' }}</p>
                            <p class="mt-3 text-sm font-bold text-green-600">✓ Succesvol hersteld</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- FAQ --}}
        <section class="px-6 pb-14">

            <div class="mx-auto max-w-4xl">
                <div class="text-center">
                    <h2 class="text-3xl font-black text-[#0b1f4d]">
                        {{ $s['faq']['title'] ?? 'Veelgestelde vragen' }}
                    </h2>
                    <p class="mt-2 text-slate-500">{{ $s['faq']['subtitle'] ?? '' }}</p>
                </div>

                <div class="mt-8 space-y-3">
                    @foreach ($s['faq']['items'] ?? [] as $fi)
                        <details class="group rounded-xl border border-blue-100 bg-white p-5">
                            <summary class="cursor-pointer list-none font-bold text-[#0b1f4d]">
                                {{ $fi['question'] ?? '' }}
                            </summary>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $fi['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- FINAL CTA --}}
        <section class="px-6 pb-10">

            <div class="relative mx-auto max-w-7xl overflow-hidden rounded-3xl bg-gradient-to-r from-[#071c43] via-[#0b3470] to-[#0c4e9b] px-7 py-9 text-white lg:px-12">

                <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-blue-400/15 blur-[90px]"></div>
                <div class="absolute right-0 bottom-0 h-72 w-72 rounded-full bg-cyan-300/10 blur-[90px]"></div>

                <div class="relative z-10 grid lg:grid-cols-[1fr_auto] gap-8 items-center">

                    <div>
                        <h2 class="text-3xl lg:text-4xl font-black">
                            {{ $s['cta']['title'] ?? 'Probleem met je Mac?' }}
                        </h2>
                        <p class="mt-2 text-xl font-semibold text-blue-100">
                            {{ $s['cta']['subtitle'] ?? 'Vervangen is niet altijd nodig.' }}
                        </p>
                        <p class="mt-2 text-blue-100">
                            {{ $s['cta']['text'] ?? 'Laat hem eerst professioneel onderzoeken.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="/reparatie-aanmelden"
                           class="inline-flex items-center gap-3 rounded-xl bg-blue-600 px-6 py-3.5 font-bold text-white transition hover:bg-blue-500">
                            Mac reparatie aanmelden <span>→</span>
                        </a>
                        <a href="tel:+31552032145"
                           class="inline-flex items-center gap-3 rounded-xl border border-white/30 bg-white/5 px-6 py-3.5 font-bold text-white transition hover:bg-white/10">
                            Bel ons: 055 203 21 45
                        </a>
                    </div>

                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')
@endsection

@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="overflow-hidden bg-[#fbfdff] text-[#07153d]">

        {{-- HERO --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-white via-[#f8fbff] to-[#eef5ff]">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute right-[4%] top-[4%] h-[500px] w-[620px] rounded-full bg-blue-100/40 blur-[100px]"></div>
            </div>

            <div class="relative mx-auto max-w-[1500px] px-5 lg:px-8 pt-5 pb-10 lg:pt-6 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-slate-700 transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-slate-700 transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-[#07153d] font-semibold">{{ config('cms.pages.ipad.label') ?? 'iPad Reparatie' }}</span>
                </div>

                <div class="grid min-h-[590px] items-center gap-8 lg:grid-cols-[.85fr_1.35fr]">

                    <!-- LEFT -->
                    <div class="relative z-20">
                        <p class="mb-5 text-[13px] font-black uppercase tracking-[.02em] text-[#0862e6]">
                            {{ $s['hero']['badge'] ?? 'Tablet & iPad Reparatie · Apeldoorn' }}
                        </p>

                        <h1 class="max-w-[610px] text-[30px] sm:text-[52px] lg:text-[40px] font-black leading-[.98] tracking-[-.045em] text-[#09153d]">
                            {{ $s['hero']['title1'] ?? 'Tablet kapot?' }}
                            <span class="mt-1 block text-[#1264df]">
                                {{ $s['hero']['title2'] ?? 'Wij maken ’m' }}
                                <span class="block">{{ $s['hero']['title3'] ?? 'weer compleet.' }}</span>
                            </span>
                        </h1>

                        <p class="mt-6 max-w-[500px] text-[16px] sm:text-[18px] leading-[1.65] text-slate-700">
                            {{ $s['hero']['description'] ?? 'Van een gebarsten scherm en batterijproblemen tot laadproblemen en andere defecten.' }}
                        </p>

                        <div class="mt-7 grid max-w-[560px] gap-x-8 gap-y-3 text-[13px] font-semibold sm:grid-cols-2">
                            @foreach ($s['hero']['trust'] ?? [['title' => 'Professionele reparatie'], ['title' => 'Snel en betrouwbaar'], ['title' => 'Kwaliteitsonderdelen'], ['title' => 'Garantie op reparaties']] as $t)
                                <div class="flex items-center gap-3">
                                    <span class="grid h-[19px] w-[19px] place-items-center rounded-full bg-[#1264df] text-[10px] text-white">✓</span>
                                    {{ $t['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3 sm:gap-4">
                            <a href="/reparatie-aanmelden"
                               class="inline-flex items-center justify-between rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white shadow-[0_12px_30px_rgba(0,80,210,.18)] transition hover:-translate-y-[2px] hover:bg-[#044dbd]">
                                Reparatie aanmelden
                                <span class="ml-3 text-xl leading-none">→</span>
                            </a>
                            <a href="#problemen"
                               class="inline-flex items-center justify-between rounded-[6px] border border-[#1264df] bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#07153d] transition hover:bg-[#f3f7ff]">
                                Bekijk reparaties
                                <span class="ml-3">→</span>
                            </a>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="relative min-h-[380px] sm:min-h-[520px]">
                        <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/19f2b394-7583-4c87-9dd9-82cb5a851fd9.png') }}"
                             alt="Tablet reparatie"
                             class="absolute bottom-[-5px] left-[-2%] z-10 w-[92%] max-w-[850px] object-contain drop-shadow-[0_28px_35px_rgba(10,40,90,.18)] max-sm:static max-sm:mx-auto max-sm:w-full max-sm:max-w-[520px] max-sm:bottom-auto max-sm:left-auto">

                        <!-- SCREEN -->
                        <div class="absolute left-[6%] top-[12%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[72px] w-[72px] place-items-center rounded-full border border-blue-100 bg-white shadow-lg">
                                <span class="text-[28px] text-[#1264df]">▭</span>
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase">Scherm</div>
                        </div>
                        <!-- CAMERA -->
                        <div class="absolute right-[3%] top-[13%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[72px] w-[72px] place-items-center rounded-full border border-blue-100 bg-white shadow-lg">
                                <span class="text-[27px] text-[#1264df]">◉</span>
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase">Camera</div>
                        </div>
                        <!-- BATTERY -->
                        <div class="absolute left-[1%] top-[43%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[72px] w-[72px] place-items-center rounded-full border border-blue-100 bg-white shadow-lg">
                                <span class="text-[27px] text-[#1264df]">▥</span>
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase">Batterij</div>
                        </div>
                        <!-- CHARGE -->
                        <div class="absolute right-[2%] top-[43%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[72px] w-[72px] place-items-center rounded-full border border-blue-100 bg-white shadow-lg">
                                <span class="text-[27px] text-[#1264df]">⚡</span>
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase">Laadpoort</div>
                        </div>
                        <!-- MOTHERBOARD -->
                        <div class="absolute bottom-[12%] right-[15%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[72px] w-[72px] place-items-center rounded-full border border-blue-100 bg-white shadow-lg">
                                <span class="text-[25px] text-[#1264df]">▦</span>
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase">Moederbord</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- WHAT IS BROKEN? --}}
        <section id="problemen" class="pb-5 pt-1">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="overflow-hidden rounded-[14px] border border-blue-100 bg-gradient-to-b from-[#f3f8ff] to-white shadow-[0_10px_30px_rgba(10,50,100,.06)]">
                    <div class="pt-5 text-center">
                        <h2 class="text-[27px] font-black">
                            {{ $s['problems']['title'] ?? 'Wat is er kapot?' }}
                        </h2>
                        <p class="mt-1 text-[10px] text-slate-500">
                            {{ $s['problems']['subtitle'] ?? 'Klik op het probleem en bekijk wat wij voor je kunnen betekenen.' }}
                        </p>
                    </div>

                    <div class="grid items-center gap-4 px-5 pb-5 pt-4 lg:grid-cols-[.8fr_1.45fr_.8fr_.8fr]">

                        <!-- LEFT PROBLEMS -->
                        <div class="space-y-3">
                            @foreach ($s['problems']['left_items'] ?? [
                                ['emoji' => '▭', 'title' => 'Gebarsten scherm', 'subtitle' => 'Scherm vervangen'],
                                ['emoji' => '▥', 'title' => 'Batterij snel leeg', 'subtitle' => 'Batterij vervangen'],
                                ['emoji' => '⚡', 'title' => 'Laadt niet', 'subtitle' => 'Laadpoort reparatie'],
                                ['emoji' => '◷', 'title' => 'Start niet op', 'subtitle' => 'Moederbord reparatie'],
                            ] as $li)
                                <div class="rounded-[9px] border border-blue-100 bg-white p-4 shadow-sm">
                                    <div class="flex gap-3">
                                        <span class="text-[24px] text-[#1264df]">{{ $li['emoji'] ?? '' }}</span>
                                        <div>
                                            <h3 class="text-[11px] font-black">{{ $li['title'] ?? '' }}</h3>
                                            <p class="mt-1 text-[9px] text-slate-500">{{ $li['subtitle'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- CENTER TABLET -->
                        <div class="relative flex min-h-[260px] sm:min-h-[310px] items-center justify-center">
                            <img src="{{ asset($s['problems']['center_image'] ?? 'assets/img/landing/l00v181zmokewobpvy9j8uw8pw9wnl135377.avif') }}"
                                 class="relative z-10 w-[95%] max-w-[520px] object-contain drop-shadow-xl"
                                 alt="Tablet problemen">
                        </div>

                        <!-- RIGHT PROBLEMS -->
                        <div class="space-y-3">
                            @foreach ($s['problems']['right_items'] ?? [
                                ['emoji' => '☝', 'title' => 'Touch werkt niet', 'subtitle' => 'Touchscreen problemen'],
                                ['emoji' => '◉', 'title' => 'Camera / geluid', 'subtitle' => 'Camera of luidspreker'],
                                ['emoji' => '▯', 'title' => 'Knoppen defect', 'subtitle' => 'Knoppen vervangen'],
                                ['emoji' => '⚙', 'title' => 'Softwareproblemen', 'subtitle' => 'Systeemfouten & updates'],
                            ] as $ri)
                                <div class="rounded-[9px] border border-blue-100 bg-white p-4 shadow-sm">
                                    <div class="flex gap-3">
                                        <span class="text-[24px] text-[#1264df]">{{ $ri['emoji'] ?? '' }}</span>
                                        <div>
                                            <h3 class="text-[11px] font-black">{{ $ri['title'] ?? '' }}</h3>
                                            <p class="mt-1 text-[9px] text-slate-500">{{ $ri['subtitle'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- CTA -->
                        <div class="flex min-h-[300px] flex-col items-center justify-center rounded-[11px] border border-blue-100 bg-white p-5 text-center shadow-sm">
                            <div class="grid h-[78px] w-[78px] place-items-center rounded-full border-[4px] border-[#1264df] text-[38px] text-[#1264df]">⚡</div>
                            <h3 class="mt-5 text-[18px] font-black">
                                {{ $s['problems']['cta_title'] ?? 'Tablet laadt niet?' }}
                            </h3>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600 whitespace-pre-line">{{ $s['problems']['cta_text'] ?? "We controleren de kabel,\nlaadpoort, batterij en het\nlaadcircuit om de oorzaak\nte vinden." }}</p>
                            <a href="/reparatie-aanmelden"
                               class="mt-5 inline-flex items-center gap-6 rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                                Meer informatie
                                <span>→</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        {{-- SCREEN REPAIR --}}
        <section id="reparatie" class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="rounded-[13px] border border-blue-100 bg-white p-5 shadow-[0_8px_30px_rgba(10,50,100,.06)]">
                    <div class="text-center">
                        <h2 class="text-[27px] font-black">
                            {{ $s['screen']['title'] ?? 'Schermreparatie' }}
                        </h2>
                        <p class="mt-1 text-[10px] text-slate-500">
                            {{ $s['screen']['subtitle'] ?? 'Gebarsten, geen beeld of touchproblemen? Wij vervangen snel en vakkundig je scherm.' }}
                        </p>
                    </div>

                    <div class="mt-5 grid items-center gap-5 lg:grid-cols-[1.15fr_1.15fr_.8fr]">
                        <!-- BEFORE -->
                        <div class="relative overflow-hidden rounded-[10px] bg-[#eef4fb]">
                            <span class="absolute left-4 top-4 z-20 rounded-[6px] bg-black/70 px-4 py-2 text-[10px] font-bold text-white">
                                {{ $s['screen']['before_label'] ?? 'VOOR' }}
                            </span>
                            <img src="{{ asset($s['screen']['before_image'] ?? 'assets/img/landing/blauwe-achtergrond-met-gebroken-glaseffect_53876-147682.avif') }}"
                                 class="h-[230px] w-full object-contain"
                                 alt="Gebroken tablet scherm">
                        </div>

                        <!-- AFTER -->
                        <div class="relative overflow-hidden rounded-[10px] bg-[#eef4fb]">
                            <span class="absolute right-4 top-4 z-20 rounded-[6px] bg-[#075ee5] px-4 py-2 text-[10px] font-bold text-white">
                                {{ $s['screen']['after_label'] ?? 'NA' }}
                            </span>
                            <img src="{{ asset($s['screen']['after_image'] ?? 'assets/img/landing/Samsung-Galaxy-Tab-S10-FE-Tablet-Grijs-128GB.webp') }}"
                                 class="h-[230px] w-full object-contain"
                                 alt="Gerepareerd tablet scherm">
                        </div>

                        <!-- BENEFITS -->
                        <div class="px-3">
                            <ul class="space-y-3 text-[11px] font-semibold">
                                @foreach ($s['screen']['benefits'] ?? [['title' => 'Originele kwaliteit schermen'], ['title' => 'Perfecte touch & helder beeld'], ['title' => 'Professionele montage'], ['title' => 'Garantie op schermreparatie']] as $b)
                                    <li class="flex items-center gap-3">
                                        <span class="text-[#1264df]">◉</span>
                                        {{ $b['title'] ?? '' }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="/reparatie-aanmelden"
                               class="mt-5 inline-flex items-center justify-between gap-6 rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                                Scherm laten repareren
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- BRANDS --}}
        <section class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="rounded-[13px] border border-blue-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-5 text-center text-[22px] font-black">
                        {{ $s['brands']['title'] ?? 'Wij repareren verschillende merken en modellen' }}
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                        @foreach ($s['brands']['items'] ?? [
                            ['emoji' => '●', 'title' => 'Apple iPad', 'subtitle' => 'Alle iPad modellen'],
                            ['emoji' => '▯', 'title' => 'Samsung', 'subtitle' => 'Galaxy Tab'],
                            ['emoji' => '▭', 'title' => 'Lenovo', 'subtitle' => 'Lenovo Tab'],
                            ['emoji' => '▦', 'title' => 'Microsoft Surface', 'subtitle' => 'Surface Pro / Go'],
                            ['emoji' => '◉', 'title' => 'Huawei', 'subtitle' => 'Huawei MatePad'],
                            ['emoji' => '•••', 'title' => 'Andere tablets', 'subtitle' => 'Vraag naar jouw model'],
                        ] as $br)
                            <div class="rounded-[9px] border border-slate-200 bg-white p-5 text-center shadow-sm">
                                <div class="text-[32px] text-[#1264df]">{{ $br['emoji'] == '●' ? '●' : $br['emoji'] }}</div>
                                <h3 class="mt-2 text-[13px] font-black">{{ $br['title'] ?? '' }}</h3>
                                <p class="mt-1 text-[9px] text-slate-500">{{ $br['subtitle'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 text-center text-[9px] font-semibold text-[#1264df]">
                        Niet zeker van jouw model? Neem contact op →
                    </div>
                </div>
            </div>
        </section>

        {{-- STEPS --}}
        <section class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="rounded-[13px] border border-blue-100 bg-gradient-to-b from-white to-[#f6f9ff] p-5">
                    <h2 class="mb-5 text-center text-[22px] font-black">
                        {{ $s['steps']['title'] ?? 'Onze reparatie stappen' }}
                    </h2>

                    <div class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_.8fr]">
                        @foreach ($s['steps']['steps'] ?? [
                            ['number' => '01', 'title' => 'Beschadigd', 'description' => "Je tablet werkt\nniet zoals het hoort."],
                            ['number' => '02', 'title' => 'Reparatie', 'description' => "We onderzoeken en\nrepareren vakkundig."],
                            ['number' => '03', 'title' => 'Klaar', 'description' => "Je tablet is weer\nals nieuw."],
                        ] as $step)
                            <div class="relative rounded-[9px] border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="text-[27px] font-black text-[#1264df]">{{ $step['number'] ?? '' }}</div>
                                <h3 class="mt-1 text-[13px] font-black">{{ $step['title'] ?? '' }}</h3>
                                <p class="mt-3 text-[10px] leading-5 text-slate-600 whitespace-pre-line">{{ $step['description'] ?? '' }}</p>
                            </div>
                        @endforeach

                        <div class="flex flex-col justify-center px-4">
                            <ul class="space-y-3 text-[10px] font-semibold">
                                @foreach ($s['steps']['benefits'] ?? [['title' => 'Gratis diagnose'], ['title' => 'Duidelijk advies vooraf'], ['title' => 'Pas repareren na akkoord'], ['title' => 'Garantie op reparatie']] as $sb)
                                    <li class="flex gap-3"><span class="text-[#1264df]">◉</span> {{ $sb['title'] ?? '' }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- REPAIR OR REPLACE --}}
        <section class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[13px] border border-blue-100 shadow-sm lg:grid-cols-[1.2fr_1fr_.8fr]">

                    <!-- REPAIR -->
                    <div class="relative min-h-[300px] overflow-hidden bg-gradient-to-r from-[#075de5] to-[#2874ef] px-7 py-6 text-white">
                        <h2 class="text-[24px] font-black">
                            {{ $s['repair']['repair_title'] ?? 'Repareren of toch vervangen?' }}
                        </h2>
                        <p class="mt-1 text-[10px] text-blue-100">
                            {{ $s['repair']['repair_subtitle'] ?? 'Vaak is reparatie de beste en voordeligere keuze.' }}
                        </p>
                        <ul class="mt-5 space-y-3 text-[10px] font-semibold">
                            @foreach ($s['repair']['repair_items'] ?? [['title' => 'Scherm vervangen'], ['title' => 'Batterij vervangen'], ['title' => 'Laadpoort reparatie'], ['title' => 'Kleine defecten oplossen'], ['title' => 'Voordeliger & duurzamer']] as $ri)
                                <li>◉ {{ $ri['title'] ?? '' }}</li>
                            @endforeach
                        </ul>
                        <img src="{{ asset($s['repair']['repair_image'] ?? 'assets/img/landing/l00v181zmokewobpvy9j8uw8pw9wnl135377.avif') }}"
                             class="absolute bottom-[-5px] right-[3px] w-[48%] max-w-[260px] object-contain max-sm:static max-sm:mt-6 max-sm:w-full max-sm:max-w-[220px] max-sm:mx-auto"
                             alt="Tablet repareren">
                    </div>

                    <!-- REPLACE -->
                    <div class="relative min-h-[300px] bg-[#fff7f2] px-7 py-6">
                        <h3 class="text-[20px] font-black">
                            {{ $s['repair']['replace_title'] ?? 'Vervangen' }}
                        </h3>
                        <ul class="mt-5 space-y-3 text-[10px] font-semibold text-slate-700">
                            @foreach ($s['repair']['replace_items'] ?? [['title' => 'Soms niet nodig'], ['title' => 'Hogere kosten'], ['title' => 'Gegevens overzetten'], ['title' => 'Niet altijd de beste keuze']] as $rpl)
                                <li>◉ {{ $rpl['title'] ?? '' }}</li>
                            @endforeach
                        </ul>
                        <img src="{{ asset($s['repair']['replace_image'] ?? 'assets/img/landing/763983dd201b21a191e84072371b2c39884063.webp') }}"
                             class="absolute bottom-0 right-3 w-[48%] max-w-[220px] object-contain max-sm:static max-sm:mt-6 max-sm:w-full max-sm:max-w-[200px] max-sm:mx-auto"
                             alt="Tablet vervangen">
                    </div>

                    <!-- ADVICE -->
                    <div class="flex min-h-[300px] flex-col items-center justify-center bg-gradient-to-br from-[#eef6ff] to-[#f8fbff] p-6 text-center">
                        <div class="text-[58px] text-[#1264df]">▭</div>
                        <h3 class="mt-4 text-[18px] font-black whitespace-pre-line">{{ $s['repair']['advice_title'] ?? "Laat ons eerst bekijken\nwat er defect is." }}</h3>
                        <a href="/reparatie-aanmelden"
                           class="mt-5 inline-flex items-center gap-6 rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                            Advies op maat
                            <span>→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        {{-- NUMBERS + FAQ --}}
        <section class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid gap-4 lg:grid-cols-2">

                    <!-- NUMBERS -->
                    <div class="rounded-[13px] border border-blue-100 bg-white p-5">
                        <h2 class="mb-5 text-center text-[18px] font-black text-[#1264df]">
                            {{ $s['numbers']['title'] ?? 'Slimme-PC in cijfers' }}
                        </h2>
                        <div class="grid grid-cols-2 divide-x divide-slate-200 sm:grid-cols-4">
                            @foreach ($s['numbers']['items'] ?? [
                                ['emoji' => '⚒', 'value' => '10+', 'label' => 'Jaar ervaring'],
                                ['emoji' => '☆', 'value' => '2500+', 'label' => 'Tablets gerepareerd'],
                                ['emoji' => '♢', 'value' => '90 Dagen', 'label' => 'Garantie op reparaties'],
                                ['emoji' => '◷', 'value' => 'Snel', 'label' => 'Meeste reparaties klaar binnen 24–48 uur'],
                            ] as $ni)
                                <div class="px-4 py-2 text-center">
                                    <div class="text-[30px] text-[#1264df]">{{ $ni['emoji'] ?? '' }}</div>
                                    <div class="mt-2 text-[23px] font-black text-[#1264df]">{{ $ni['value'] ?? '' }}</div>
                                    <div class="mt-1 text-[9px] text-slate-500 leading-tight">{{ $ni['label'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- FAQ -->
                    <div class="rounded-[13px] border border-blue-100 bg-white p-5">
                        <h2 class="mb-4 text-[18px] font-black">
                            {{ $s['faq']['title'] ?? 'Veelgestelde vragen' }}
                        </h2>
                        <div class="divide-y divide-slate-200">
                            @foreach ($s['faq']['items'] ?? [
                                ['question' => 'Hoe lang duurt een tablet reparatie?', 'answer' => 'De meeste reparaties zijn afhankelijk van onderdeel en schade binnen korte tijd uitvoerbaar.'],
                                ['question' => 'Krijg ik garantie op de reparatie?', 'answer' => 'Ja, op uitgevoerde reparaties en gebruikte onderdelen geldt garantie volgens onze voorwaarden.'],
                                ['question' => 'Gaat mijn data verloren?', 'answer' => 'We proberen je gegevens altijd te behouden en bespreken risico\'s vooraf.'],
                                ['question' => 'Welke tablets repareren jullie?', 'answer' => 'We repareren onder andere Apple, Samsung, Lenovo, Microsoft Surface, Huawei en andere merken.'],
                            ] as $fi)
                                <details class="group">
                                    <summary class="flex cursor-pointer list-none items-center justify-between py-3 text-[10px] font-bold">
                                        {{ $fi['question'] ?? '' }}
                                        <span class="text-[#1264df] group-open:hidden">+</span>
                                        <span class="hidden text-[#1264df] group-open:inline">−</span>
                                    </summary>
                                    <p class="pb-3 text-[10px] leading-5 text-slate-500">{{ $fi['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                        <div class="mt-3 text-right text-[9px] font-bold text-[#1264df]">
                            Bekijk alle veelgestelde vragen →
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- FINAL CTA --}}
        <section class="pb-10">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[13px] bg-gradient-to-r from-[#075ee5] via-[#064fc5] to-[#061d4c] text-white shadow-[0_12px_35px_rgba(5,30,80,.18)] lg:grid-cols-[.9fr_1.25fr_1fr]">

                    <div class="relative min-h-[210px]">
                        <img src="{{ asset($s['cta']['image'] ?? 'assets/img/landing/scherm-en-beeldkwaliteit_hero_1751220810.webp') }}"
                             class="absolute bottom-0 left-5 h-[95%] max-w-[330px] object-contain max-lg:static max-lg:mx-auto max-lg:h-[220px] max-lg:w-full max-lg:max-w-[280px]"
                             alt="Tablet reparatie">
                    </div>

                    <div class="flex flex-col justify-center px-7 py-6">
                        <h2 class="text-[25px] font-black">
                            {{ $s['cta']['title'] ?? 'Geef je tablet een tweede kans.' }}
                        </h2>
                        <p class="mt-2 text-[10px] text-blue-100">
                            {{ $s['cta']['subtitle'] ?? 'Snel, vakkundig en met garantie gerepareerd.' }}
                        </p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="/reparatie-aanmelden"
                               class="inline-flex items-center rounded-[6px] bg-[#1264df] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                                Tablet reparatie aanmelden
                            </a>
                            <a href="tel:0552032145"
                               class="inline-flex items-center gap-3 rounded-[6px] border border-white/40 px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-white/10">
                                ☎ Bel ons: 055 203 21 45
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center border-white/15 px-7 py-6 lg:border-l">
                        <ul class="space-y-3 text-[10px]">
                            <li>◉ Gratis diagnose</li>
                            <li>◉ Geen verborgen kosten</li>
                            <li>◉ Garantie op reparaties</li>
                            <li>◉ Snelle service</li>
                        </ul>
                        <div class="mt-5 rounded-[8px] border border-white/30 p-4">
                            <div class="text-[11px] font-bold">
                                {{ $s['cta']['address_title'] ?? 'Slimme-PC Apeldoorn' }}
                            </div>
                            <div class="mt-1 text-[9px] leading-4 text-blue-100 whitespace-pre-line">{{ $s['cta']['address_text'] ?? "Laan van de Mensenrechten 400\n7331 VZ Apeldoorn" }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')
@endsection

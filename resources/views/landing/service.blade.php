@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <style>
        .hero-overlay {
            background: linear-gradient(90deg,
                rgba(2, 14, 29, .98) 0%,
                rgba(2, 16, 34, .93) 32%,
                rgba(2, 18, 38, .62) 58%,
                rgba(2, 18, 38, .18) 100%);
        }

        .repair-card {
            transition: .25s ease;
        }

        .repair-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(0, 63, 145, .12);
            border-color: #b9d7ff;
        }

        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
        }

        .faq-item.active .faq-content {
            max-height: 220px;
        }

        .faq-item.active .faq-plus {
            transform: rotate(45deg);
        }

        .faq-plus {
            transition: .25s ease;
        }
    </style>

    <main>
        {{-- HERO --}}
        <section class="relative overflow-hidden bg-[#06162a]"
            style="background-image:url('{{ asset($s['hero']['image'] ?? 'assets/img/landing/85cea032-2e38-4f3d-8071-f8677565c0a3.png') }}');background-position:center;background-size:cover;">

            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative max-w-[1440px] mx-auto px-6 lg:px-14 pt-6 pb-12 lg:pt-7 lg:pb-16">

                <div class="flex items-center gap-2 text-[12px] text-white/75 mb-9">
                    <span>Home</span>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>Diensten</span>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-white">{{ $s['hero']['title1'] ?? 'Laptop reparatie' }}</span>
                </div>

                <div class="max-w-[750px]">

                    <div class="flex items-center gap-2 text-[#1597ff] font-semibold uppercase text-xs tracking-wide mb-3">
                        <i data-lucide="circle-check" class="w-4 h-4"></i>
                        {{ $s['hero']['badge'] ?? '' }}
                    </div>

                    <h1 class="text-white font-black leading-[1.02] tracking-[-0.04em] text-[42px] sm:text-[52px] lg:text-[45px]">
                        {{ $s['hero']['title1'] ?? '' }}

                        <span class="block text-[#1597ff]">
                            {{ $s['hero']['title2'] ?? '' }}
                        </span>

                        <span class="block">
                            {{ $s['hero']['title3'] ?? '' }}
                        </span>
                    </h1>

                    <p class="text-white/90 mt-6 text-[16px] sm:text-[17px] leading-7 max-w-xl">
                        {!! nl2br(e($s['hero']['description'] ?? '')) !!}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-8 max-w-[750px]">
                        @foreach ($s['hero']['usp'] ?? [] as $u)
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 shrink-0 rounded-full border border-[#2598ff] flex items-center justify-center">
                                    <i data-lucide="{{ $u['icon'] ?? 'check' }}" class="w-5 h-5 text-white"></i>
                                </div>

                                <div>
                                    <div class="font-bold text-white text-[13px]">
                                        {{ $u['title'] ?? '' }}
                                    </div>
                                    <div class="text-white/65 text-[10px]">
                                        {{ $u['subtitle'] ?? '' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mt-9">
                        <a href="{{ $s['hero']['cta1_url'] ?? '#reparatie-aanmelden' }}"
                           class="inline-flex justify-center items-center gap-5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg px-7 py-4 shadow-[0_16px_35px_rgba(37,99,235,.22)] hover:shadow-[0_20px_45px_rgba(37,99,235,.32)] hover:-translate-y-1 transition duration-300">

                            {{ $s['hero']['cta1_text'] ?? 'Reparatie aanmelden' }}

                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>

                        <a href="{{ $s['hero']['cta2_url'] ?? '#werkwijze' }}"
                           class="inline-flex justify-center items-center gap-4 border border-white/80 hover:bg-white/10 text-white font-semibold rounded-lg px-7 py-4 transition">

                            {{ $s['hero']['cta2_text'] ?? 'Bekijk onze werkwijze' }}

                            <i data-lucide="play-circle" class="w-5 h-5"></i>
                        </a>
                    </div>

                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-[#1597ff]"></div>
        </section>


        {{-- PROBLEEM SELECTIE --}}
        <section class="py-7 lg:py-8 bg-white">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-5">
                    <h2 class="text-[30px] lg:text-[34px] font-black tracking-tight">
                        {{ $s['problems']['title'] ?? '' }}
                        <span class="text-[#087fea]">{{ $s['problems']['title_highlight'] ?? '' }}</span>
                        {{ $s['problems']['subtitle'] ?? '' }}
                    </h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($s['problems']['items'] ?? [] as $item)
                        <a href="#" class="repair-card relative group bg-white border border-gray-200 rounded-lg min-h-[110px] flex flex-col items-center justify-center shadow-card p-4">
                            <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="w-9 h-9 text-[#0759c7] mb-3"></i>
                            <span class="font-bold text-[13px]">{{ $item['title'] ?? '' }}</span>
                            <i data-lucide="chevron-right" class="absolute right-3 bottom-4 w-4 h-4 text-gray-300 group-hover:text-[#0759c7]"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- COMPONENT REPAIR --}}
        <section id="werkwijze" class="pb-7">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <div class="border border-gray-200 rounded-xl bg-[#fbfdff] shadow-soft p-5 lg:p-6">
                    <div class="grid lg:grid-cols-[1fr_1.45fr_.72fr] gap-6 items-stretch">

                        <div class="py-2">
                            <div class="text-[#087fea] uppercase text-[11px] font-bold flex items-center gap-2">
                                <i data-lucide="circle-check" class="w-4 h-4"></i>
                                {{ $s['speciality']['badge'] ?? '' }}
                            </div>

                            <h2 class="text-[31px] font-black leading-[1.04] mt-4">
                                {{ $s['speciality']['title1'] ?? '' }}
                                <span class="block text-[#0759c7]">{{ $s['speciality']['title2'] ?? '' }}</span>
                            </h2>

                            <p class="text-gray-600 leading-6 text-[14px] mt-4 max-w-md">
                                {!! nl2br(e($s['speciality']['description'] ?? '')) !!}
                            </p>

                            <div class="space-y-2 mt-5 text-[13px]">
                                @foreach ($s['speciality']['list'] ?? [] as $li)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="{{ $li['icon'] ?? 'circle-check-big' }}" class="w-4 h-4 text-[#0759c7]"></i>
                                        {{ $li['title'] ?? '' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative overflow-hidden rounded-xl min-h-[340px]">
                            @if (!empty($s['speciality']['video']))
                                <video id="specialityVideo" class="absolute inset-0 w-full h-full object-cover" controls preload="metadata"
                                       poster="{{ asset($s['speciality']['video_poster'] ?? '') }}">
                                    <source src="{{ url('/stream/video/' . basename($s['speciality']['video'])) }}" type="video/mp4">
                                </video>
                            @else
                                <img src="{{ asset($s['speciality']['video_poster'] ?? 'assets/img/landing/e4703bd3-ffe8-4ca1-8543-7f5a97484698.png') }}"
                                     class="absolute inset-0 w-full h-full object-cover" alt="Laptop component reparatie">
                            @endif

                            <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>

                            <button id="specialityPlayBtn" type="button" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-white/95 hover:scale-105 rounded-full shadow-xl flex items-center justify-center transition">
                                <i data-lucide="play" class="w-8 h-8 fill-[#0759c7] text-[#0759c7] ml-1"></i>
                            </button>

                            <div class="absolute bottom-4 left-4 bg-black/70 backdrop-blur-sm text-white font-semibold text-[12px] px-4 py-2 rounded-full flex items-center gap-2 pointer-events-none">
                                <i data-lucide="play-circle" class="w-4 h-4"></i>
                                Echte reparatie in onze werkplaats
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <div class="space-y-5">
                                @foreach ($s['equipment']['items'] ?? [] as $eq)
                                    <div class="flex gap-4">
                                        <div class="text-[#0759c7] shrink-0">
                                            <i data-lucide="{{ $eq['icon'] ?? 'check' }}" class="w-8 h-8"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-[14px]">{{ $eq['title'] ?? '' }}</h3>
                                            <p class="text-[12px] leading-5 text-gray-600">{{ $eq['subtitle'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <hr>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>


        {{-- BEFORE / DIAGNOSE / AFTER --}}
        <section class="pb-8">
            <div class="max-w-[1300px] mx-auto px-6">
                <div class="text-center mb-5">
                    <h2 class="font-black text-[29px] lg:text-[32px]">{{ $s['example']['title'] ?? '' }}</h2>
                    <p class="text-gray-500 text-[13px] mt-1">{{ $s['example']['subtitle'] ?? '' }}</p>
                </div>

                <div class="grid md:grid-cols-[1fr_42px_1fr_42px_1fr_.65fr] gap-3 items-center">

                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-card">
                        <div class="relative h-[175px]">
                            <img src="{{ asset($s['example']['before_image'] ?? 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp') }}" class="w-full h-full" alt="">
                            <span class="absolute top-0 left-0 bg-red-500 text-white font-bold text-[11px] px-4 py-2 rounded-br-xl">{{ $s['example']['before_label'] ?? 'Voor' }}</span>
                        </div>
                        <div class="p-4 text-center">
                            <p class="text-[12px] font-semibold">{!! nl2br(e($s['example']['before_text'] ?? '')) !!}</p>
                        </div>
                    </div>

                    <div class="hidden md:flex justify-center">
                        <div class="w-9 h-9 rounded-full bg-[#bde0ff] flex items-center justify-center">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-[#0759c7]"></i>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-card">
                        <div class="relative h-[175px]">
                            <img src="{{ asset($s['example']['diagnose_image'] ?? 'assets/img/landing/363f8f55-fba7-4f23-88db-8c8e728d522e.png') }}" class="w-full h-full object-cover" alt="">
                            <span class="absolute top-0 left-0 bg-[#ff7200] text-white font-bold text-[11px] px-4 py-2 rounded-br-xl">{{ $s['example']['diagnose_label'] ?? 'Diagnose' }}</span>
                        </div>
                        <div class="p-4 text-center">
                            <p class="text-[12px] font-semibold">{!! nl2br(e($s['example']['diagnose_text'] ?? '')) !!}</p>
                        </div>
                    </div>

                    <div class="hidden md:flex justify-center">
                        <div class="w-9 h-9 rounded-full bg-[#bde0ff] flex items-center justify-center">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-[#0759c7]"></i>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-card">
                        <div class="relative h-[175px]">
                            <img src="{{ asset($s['example']['after_image'] ?? 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png') }}" class="w-full h-full object-cover" alt="">
                            <span class="absolute top-0 left-0 bg-emerald-500 text-white font-bold text-[11px] px-4 py-2 rounded-br-xl">{{ $s['example']['after_label'] ?? 'Na' }}</span>
                        </div>
                        <div class="p-4 text-center">
                            <p class="text-[12px] font-semibold">{!! nl2br(e($s['example']['after_text'] ?? '')) !!}</p>
                        </div>
                    </div>

                    <div class="h-full min-h-[250px] rounded-lg bg-[#eef5ff] border border-[#d8e7ff] flex flex-col justify-center items-center text-center px-5">
                        <i data-lucide="shield-check" class="w-11 h-11 text-[#0759c7]"></i>
                        <div class="text-[#0759c7] font-black text-[20px] mt-4">{{ $s['example']['tested_title'] ?? '100% getest' }}</div>
                        <p class="text-[11px] leading-5 mt-2 text-gray-700">{{ $s['example']['tested_text'] ?? '' }}</p>
                    </div>

                </div>
            </div>
        </section>


        {{-- OTHER REPAIRS --}}
        <section class="pb-9">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <h2 class="text-center font-black text-[28px] lg:text-[31px] mb-5">
                    {{ $s['other']['title'] ?? '' }}
                    <span class="text-[#0759c7]">{{ $s['other']['title_highlight'] ?? '' }}</span>
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($s['other']['items'] ?? [] as $item)
                        <a href="#" class="repair-card overflow-hidden border border-gray-200 rounded-lg bg-white shadow-card">
                            <img src="{{ asset('assets/img/landing/'.basename($item['image'] ?? '')) }}" class="w-full h-[140px] object-cover" alt="">
                            <div class="text-center py-4">
                                <div class="font-bold text-[12px]">{{ $item['title'] ?? '' }}</div>
                                @if (!empty($item['subtitle']))
                                    <div class="text-gray-500 text-[10px] mt-1">{{ $item['subtitle'] }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- FAQ + CTA --}}
        <section class="pb-8">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <div class="grid lg:grid-cols-[.85fr_1.15fr] gap-5">

                    <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-soft">
                        <h2 class="font-black text-[22px] mb-3">{{ $s['faq']['title'] ?? 'Veelgestelde vragen' }}</h2>

                        @foreach ($s['faq']['items'] ?? [] as $item)
                            <div class="faq-item border-b">
                                <button class="faq-toggle w-full flex items-center justify-between py-4 text-left font-bold text-[13px]">
                                    {{ $item['question'] ?? '' }}
                                    <span class="faq-plus text-[#0759c7] text-2xl font-light">+</span>
                                </button>
                                <div class="faq-content">
                                    <p class="pb-4 text-gray-600 text-[12px] leading-5">{!! nl2br(e($item['answer'] ?? '')) !!}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-right mt-3">
                            <a href="{{ $s['faq']['more_url'] ?? '#' }}" class="inline-flex gap-2 items-center font-semibold text-[11px] text-gray-700">
                                Bekijk alle veelgestelde vragen
                                <i data-lucide="arrow-right" class="w-4 h-4 text-[#0759c7]"></i>
                            </a>
                        </div>
                    </div>

                    <div id="reparatie-aanmelden" class="relative overflow-hidden bg-gradient-to-r from-[#0752ae] to-[#063f96] rounded-xl min-h-[270px]">
                        <div class="relative z-10 px-8 lg:px-10 py-8 lg:py-10 max-w-[65%]">
                            <h2 class="text-white font-black text-[32px] lg:text-[38px] leading-tight">
                                {{ $s['faq']['cta_title'] ?? 'Laptop' }}
                                <span class="text-[#4cb4ff]">{{ $s['faq']['cta_title2'] ?? 'laten repareren?' }}</span>
                            </h2>

                            <p class="text-white/90 text-[13px] leading-6 mt-4">
                                {!! nl2br(e($s['faq']['cta_subtitle'] ?? '')) !!}
                            </p>

                            <div class="flex flex-wrap gap-4 mt-7">
                                <a href="#reparatie-aanmelden"
                                   class="bg-[#ff7200] hover:bg-[#ed6900] text-white rounded-lg px-6 py-3.5 font-bold text-[13px] flex items-center gap-5 transition">
                                    Reparatie aanmelden
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>

                                <a href="tel:{{ preg_replace('/[^0-9]/', '', $s['faq']['cta_phone'] ?? '0552032145') }}"
                                   class="border border-white/70 hover:bg-white/10 text-white rounded-lg px-6 py-3.5 font-bold text-[13px] flex items-center gap-3 transition">
                                    <i data-lucide="phone" class="w-5 h-5"></i>
                                    <span>
                                        <small class="block font-normal text-[10px] opacity-80">Bel ons direct</small>
                                        {{ $s['faq']['cta_phone'] ?? '055 203 21 45' }}
                                    </span>
                                </a>
                            </div>
                        </div>

                        <img src="{{ asset($s['faq']['cta_bg'] ?? 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png') }}" alt="Slimme-PC"
                             class="absolute right-0 bottom-0 h-[95%] object-contain object-bottom">
                    </div>

                </div>
            </div>
        </section>


        {{-- BOTTOM USP BAR --}}
        <section class="pb-10">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <div class="bg-[#f7f9fc] border border-gray-100 rounded-xl py-5 px-6">
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-5">
                        @foreach ($s['bottom']['items'] ?? [] as $item)
                            <div class="flex items-center gap-3">
                                <i data-lucide="{{ $item['icon'] ?? 'check' }}" class="w-7 h-7 text-[#0759c7] shrink-0"></i>
                                <div>
                                    <div class="font-bold text-[12px]">{{ $item['title'] ?? '' }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $item['subtitle'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
@endsection

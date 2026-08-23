@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="bg-slate-50 text-slate-900 overflow-hidden">

        {{-- HERO --}}
        <section
            class="relative overflow-hidden bg-gradient-to-br from-[#123b6d] via-[#0f4d8f] to-[#0b2f5c] text-white">

            <div class="absolute -top-28 left-[30%] w-[420px] h-[420px] rounded-full bg-blue-300/20 blur-[120px]"></div>
            <div class="absolute right-0 bottom-0 w-[380px] h-[380px] rounded-full bg-cyan-300/10 blur-[100px]"></div>

            <div class="relative max-w-7xl mx-auto px-6 lg:px-8 pt-6 pb-10 lg:pt-7 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-white/75 mb-6">
                    <a href="{{ url('/') }}" class="hover:text-white transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-white transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-white font-semibold">{{ config('cms.pages.console.label') ?? 'Playstation / Xbox' }}</span>
                </div>

                <div class="grid lg:grid-cols-2 gap-10 items-center">

                    <div>
                        <p class="text-sm uppercase tracking-wide font-bold text-blue-200 mb-4">
                            {{ $s['hero']['badge'] ?? 'Console reparatie · Apeldoorn' }}
                        </p>

                        <h1 class="text-[30px] sm:text-[52px] lg:text-[60px] font-black leading-[1.05]">
                            {{ $s['hero']['title1'] ?? 'PlayStation 5 of Xbox' }}
                            <span class="text-blue-300">{{ $s['hero']['title2'] ?? 'kapot?' }}</span>
                        </h1>

                        <p class="mt-5 text-[16px] sm:text-[20px] lg:text-[24px] font-semibold text-blue-50 leading-snug">
                            {{ $s['hero']['description'] ?? '' }}
                        </p>

                        <div class="mt-7 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3 text-sm">
                            @foreach ($s['hero']['problem_list'] ?? [] as $p)
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 shrink-0 rounded-full bg-blue-500 flex items-center justify-center text-xs text-white">✓</span>
                                    <span>{{ $p['title'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3 sm:gap-4">
                            <a href="{{ $s['hero']['cta1_url'] ?? '#' }}"
                               class="inline-flex justify-center items-center gap-2 sm:gap-5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] shadow-[0_16px_35px_rgba(37,99,235,.22)] hover:shadow-[0_20px_45px_rgba(37,99,235,.32)] hover:-translate-y-1 transition duration-300">

                                {{ $s['hero']['cta1_text'] ?? 'Console reparatie aanmelden' }}

                                <span>→</span>
                            </a>

                            <a href="{{ $s['hero']['cta2_url'] ?? '#problemen' }}"
                               class="inline-flex justify-center items-center gap-2 sm:gap-4 border border-white/80 hover:bg-white/10 text-white font-semibold rounded-lg px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] transition">

                                {{ $s['hero']['cta2_text'] ?? 'Bekijk problemen' }}

                                <span>↓</span>
                            </a>
                        </div>
                    </div>

                    <div class="relative mt-10 lg:mt-0 flex justify-center">
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] sm:w-[420px] sm:h-[420px] rounded-full bg-blue-400/20 blur-[90px]"></div>

                        <img
                            src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/playtios2.png') }}"
                            alt="PlayStation 5 en Xbox reparatie"
                            class="relative z-10 w-full max-w-[340px] sm:max-w-none sm:w-[520px] lg:w-[620px] xl:w-[750px] object-contain drop-shadow-2xl"
                        >
                    </div>

                </div>
            </div>
        </section>


        {{-- CONSOLE KEUZE --}}
        <section class="py-16 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-black text-slate-900">
                        {{ $s['consoles']['title'] ?? 'Welke console heb je?' }}
                    </h2>

                    <p class="mt-2 text-slate-500">
                        {{ $s['consoles']['subtitle'] ?? '' }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ($s['consoles']['items'] ?? [] as $ci)
                        <div class="{{ $loop->first ? 'bg-white border-2 border-blue-500' : 'bg-white border border-slate-200 hover:border-blue-300' }} rounded-2xl p-6 shadow-sm hover:shadow-lg transition">
                            <div class="flex items-center justify-between gap-6">
                                <img src="{{ asset('assets/img/landing/' . basename($ci['image'] ?? '')) }}" alt="{{ $ci['name'] ?? '' }}" class="w-40 h-40 object-contain">
                                <div>
                                    <h3 class="text-xl font-bold">{{ $ci['name'] ?? '' }}</h3>
                                    <a href="#"
                                       class="inline-block mt-4 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                                        Bekijken →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- PROBLEMEN --}}
        <section id="problemen" class="px-6 pb-12">
            <div class="max-w-7xl mx-auto bg-gradient-to-b from-blue-50 to-white border border-blue-100 rounded-3xl p-8">
                <div class="text-center">
                    <h2 class="text-3xl font-black text-slate-900">
                        {{ $s['problems']['title'] ?? 'Wat is er mis met je console?' }}
                    </h2>

                    <p class="mt-2 text-slate-500">
                        {{ $s['problems']['subtitle'] ?? '' }}
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    @foreach ($s['problems']['items'] ?? [] as $item)
                        <div class="bg-white rounded-xl border border-slate-200 p-4 text-center hover:border-blue-400 hover:-translate-y-1 transition">
                            <div class="text-3xl mb-3">{{ $item['emoji'] ?? '' }}</div>
                            <h3 class="font-bold text-sm">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $item['subtitle'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- WERKWIJZE --}}
        <section class="px-6 py-14">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-black text-center">
                    {{ $s['werkwijze']['title'] ?? 'Onze werkwijze' }}
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mt-12">
                    @foreach ($s['werkwijze']['steps'] ?? [] as $step)
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto rounded-full bg-white border-2 border-blue-200 shadow flex items-center justify-center font-black text-blue-600">
                                {{ $step['number'] ?? '' }}
                            </div>
                            <h3 class="font-bold mt-4">{{ $step['title'] ?? '' }}</h3>
                            <p class="text-sm text-slate-500 mt-2">
                                {{ $step['description'] ?? '' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- ONDER DE MOTORKAP --}}
        <section class="px-6 py-10">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="grid lg:grid-cols-3 items-center">
                        <div class="p-8">
                            <h2 class="text-3xl font-black">
                                {{ $s['motorkap']['title'] ?? 'Onder de motorkap' }}
                            </h2>

                            <p class="mt-3 text-slate-600">
                                {{ $s['motorkap']['description'] ?? '' }}
                            </p>

                            <div class="mt-6 space-y-3 text-sm">
                                @foreach ($s['motorkap']['checklist'] ?? [] as $li)
                                    <div class="flex gap-3">
                                        <span class="text-blue-600">✓</span>
                                        {{ $li['title'] ?? '' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-6">
                            <img src="{{ asset($s['motorkap']['image'] ?? 'assets/img/landing/playtios2.png') }}" alt="Console binnenkant" class="w-full object-contain">
                        </div>

                        <div class="p-8 space-y-5">
                            @foreach ($s['motorkap']['spots'] ?? [] as $spot)
                                <div>
                                    <h3 class="font-black">{{ $spot['title'] ?? '' }}</h3>
                                    <p class="text-sm text-slate-500 mt-1">
                                        {{ $spot['description'] ?? '' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- VOOR & NA --}}
        <section class="px-6 py-10">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-7">
                    <div class="flex items-center justify-between gap-6 flex-wrap">
                        <h2 class="text-2xl font-black">
                            {{ $s['voorana']['title'] ?? 'Voor & na: Professionele reiniging' }}
                        </h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mt-6">
                        <div class="relative">
                            <span class="absolute top-3 left-3 bg-slate-900 text-white text-xs font-bold px-3 py-1 rounded">
                                {{ $s['voorana']['before_label'] ?? 'VOOR' }}
                            </span>
                            <img src="{{ asset($s['voorana']['before_image'] ?? 'assets/img/landing/playtios2.png') }}" alt="Voor onderhoud"
                                 class="w-full h-56 object-cover rounded-2xl">
                        </div>

                        <div class="relative">
                            <span class="absolute top-3 left-3 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded">
                                {{ $s['voorana']['after_label'] ?? 'NA' }}
                            </span>
                            <img src="{{ asset($s['voorana']['after_image'] ?? 'assets/img/landing/playtios2.png') }}" alt="Na onderhoud"
                                 class="w-full h-56 object-cover rounded-2xl">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-3 mt-6 text-sm">
                        @foreach ($s['voorana']['checklist'] ?? [] as $li)
                            <div>✓ {{ $li['title'] ?? '' }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-3xl p-7">
                    <h2 class="text-2xl font-black">
                        {{ $s['voorana']['hdmi_title'] ?? 'HDMI probleem opgelost' }}
                    </h2>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        @foreach ($s['voorana']['hdmi_steps'] ?? [] as $step)
                            <div class="bg-slate-50 rounded-xl p-4">
                                <span class="text-xs text-blue-600 font-bold">{{ str_pad($step['number'] ?? '0', 2, '0', STR_PAD_LEFT) }}</span>
                                <p class="font-semibold mt-2">{{ $step['title'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>


        {{-- SERVICES --}}
        <section class="px-6 py-12">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-black text-center mb-8">
                    {{ $s['services']['title'] ?? 'Onze console services' }}
                </h2>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($s['services']['items'] ?? [] as $item)
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center hover:-translate-y-1 hover:shadow-lg transition">
                            <div class="text-4xl">{{ $item['emoji'] ?? '' }}</div>
                            <h3 class="font-bold mt-4">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-sm text-slate-500 mt-2">
                                {{ $item['description'] ?? '' }}
                            </p>
                            <p class="text-blue-600 font-bold mt-4">
                                {{ $item['price'] ?? '' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- GARANTIE --}}
        <section class="px-6 py-10">
            <div class="max-w-7xl mx-auto">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0a2d59] via-[#0e4d8f] to-[#1165b5] text-white p-10">
                    <div class="absolute right-0 bottom-0 w-72 h-72 bg-blue-300/20 rounded-full blur-[100px]"></div>

                    <div class="relative max-w-2xl">
                        <h2 class="text-3xl font-black">
                            {{ $s['garantie']['title'] ?? 'Garantie op reparaties' }}
                        </h2>

                        <p class="mt-4 text-blue-100">
                            {{ $s['garantie']['description'] ?? '' }}
                        </p>

                        <div class="mt-6 space-y-3">
                            @foreach ($s['garantie']['points'] ?? [] as $p)
                                <div>✓ {{ $p['title'] ?? '' }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')
@endsection

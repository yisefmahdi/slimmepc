@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="overflow-hidden bg-white text-[#0b132b]">

        {{-- HERO --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-white via-[#f8fbff] to-[#edf5ff]">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8 pt-5 pb-6 lg:pt-6 lg:pb-10">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-slate-700 transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-slate-700 transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-[#0b132b] font-semibold">{{ config('cms.pages.pcreparatie.label') ?? 'PC Reparatie' }}</span>
                </div>

                <div class="grid min-h-[620px] items-center gap-10 lg:grid-cols-[0.9fr_1.35fr]">
                    <div class="relative z-20">
                        <div class="mb-6 text-[13px] font-bold uppercase tracking-[0.02em] text-[#0964e8]">
                            {{ $s['hero']['badge'] ?? 'PC Reparatie & PC op maat · Apeldoorn' }}
                        </div>

                        <h1 class="max-w-[650px] text-[30px] sm:text-[52px] lg:text-[40px] font-black leading-[0.98] tracking-[-0.045em] text-[#0a1022]">
                            {{ $s['hero']['title1'] ?? 'Problemen met je PC?' }}
                            <span class="block text-[#1264df]">{{ $s['hero']['title2'] ?? 'Of tijd voor iets beters?' }}</span>
                        </h1>

                        <p class="mt-6 max-w-[570px] text-[16px] sm:text-[17px] leading-7 text-slate-600 whitespace-pre-line">{{ $s['hero']['description'] ?? "Van reparatie en upgrades tot een complete PC op maat.\nKwaliteit, snelheid en eerlijk advies." }}</p>

                        <div class="mt-7 grid max-w-[590px] grid-cols-1 gap-x-10 gap-y-3 text-[14px] font-semibold text-slate-700 sm:grid-cols-2">
                            @foreach ($s['hero']['bullets'] ?? [['title' => 'Diagnose & reparatie'], ['title' => 'Zakelijke computers'], ['title' => 'Upgrades'], ['title' => 'PC op maat'], ['title' => "Gaming PC's"], ['title' => 'Professionele montage']] as $b)
                                <div class="flex items-center gap-2">
                                    <span class="grid h-[19px] w-[19px] place-items-center rounded-full bg-[#1264df] text-white">✓</span>
                                    {{ $b['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3 sm:gap-4">
                            <a href="/reparatie-aanmelden"
                               class="group inline-flex items-center justify-center gap-3 rounded-[6px] bg-[#0e61e9] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white shadow-[0_10px_25px_rgba(14,97,233,.18)] transition hover:-translate-y-0.5 hover:bg-[#084fc5]">
                                PC laten repareren
                                <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#pc-op-maat"
                               class="group inline-flex items-center justify-center gap-3 rounded-[6px] border border-[#1264df] bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#1264df] transition hover:bg-[#f2f7ff]">
                                PC laten samenstellen
                                <svg class="h-[17px] w-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13" rx="2" stroke-width="2"/><path d="M8 21h8M12 17v4" stroke-width="2"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="relative min-h-[400px] sm:min-h-[520px]">
                        <div class="absolute left-1/2 top-1/2 h-[430px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#ddecff]/70 blur-[80px]"></div>
                        <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}"
                             alt="Custom PC Slimme-PC"
                             class="absolute left-[47%] top-[49%] z-10 w-[57%] max-w-[620px] -translate-x-1/2 -translate-y-1/2 object-contain drop-shadow-[0_30px_30px_rgba(20,54,105,.20)] max-lg:static max-lg:mx-auto max-lg:w-full max-lg:max-w-[520px] max-lg:translate-x-0 max-lg:left-auto max-lg:top-auto max-lg:translate-y-0">

                        <div class="absolute left-[1%] top-[7%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/cpu.png') }}" class="w-[190px] object-contain drop-shadow-xl" alt="GPU">
                            <div class="mt-0 pl-5 text-[13px] font-black uppercase text-[#0c1736]">GPU <span class="block text-[11px] font-medium normal-case text-slate-500">Grafische kaart</span></div>
                        </div>
                        <div class="absolute left-[2%] top-[39%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/b76aa0e9-6ac7-4a0e-92a4-41766b1f77d4.png') }}" class="w-[175px] object-contain drop-shadow-xl" alt="RAM">
                            <div class="mt-1 pl-4 text-[13px] font-black uppercase text-[#0c1736]">RAM <span class="block text-[11px] font-medium normal-case text-slate-500">Werkgeheugen</span></div>
                        </div>
                        <div class="absolute bottom-[8%] left-[7%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/SSD-hard.jpg') }}" class="w-[140px] object-contain drop-shadow-xl" alt="SSD">
                            <div class="mt-1 pl-5 text-[13px] font-black uppercase text-[#0c1736]">SSD <span class="block text-[11px] font-medium normal-case text-slate-500">Opslag</span></div>
                        </div>
                        <div class="absolute right-[4%] top-[5%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/pc/cpu.png') }}" class="mx-auto w-[100px] object-contain drop-shadow-xl" alt="CPU">
                            <div class="mt-2 text-[13px] font-black uppercase text-[#0c1736]">CPU <span class="block text-[11px] font-medium normal-case text-slate-500">Processor</span></div>
                        </div>
                        <div class="absolute right-[1%] top-[31%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/pc/motherboard.png') }}" class="w-[160px] object-contain drop-shadow-xl" alt="Motherboard">
                            <div class="mt-2 text-[13px] font-black uppercase text-[#0c1736]">Moederbord <span class="block text-[11px] font-medium normal-case text-slate-500">Motherboard</span></div>
                        </div>
                        <div class="absolute bottom-[17%] right-[0%] z-20 hidden xl:block">
                            <img src="{{ asset('assets/img/landing/pc/psu.png') }}" class="w-[150px] object-contain drop-shadow-xl" alt="PSU">
                            <div class="mt-2 text-[13px] font-black uppercase text-[#0c1736]">PSU <span class="block text-[11px] font-medium normal-case text-slate-500">Voeding</span></div>
                        </div>
                        <div class="absolute bottom-[0%] left-[50%] z-20 hidden -translate-x-1/2 xl:block">
                            <img src="{{ asset('assets/img/landing/pc/cooling.png') }}" class="mx-auto w-[145px] object-contain drop-shadow-xl" alt="Cooling">
                            <div class="text-center text-[13px] font-black uppercase text-[#0c1736]">Koeling <span class="block text-[11px] font-medium normal-case text-slate-500">Cooling</span></div>
                        </div>
                    </div>
                </div>

                <div class="grid border-t border-slate-200/80 py-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($s['benefits']['items'] ?? [
                        ['emoji' => '⌁', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & transparant'],
                        ['emoji' => '◷', 'title' => 'Snelle service', 'subtitle' => 'Vaak binnen 24 uur'],
                        ['emoji' => '♢', 'title' => 'Garantie', 'subtitle' => 'Op reparaties & onderdelen'],
                        ['emoji' => '☆', 'title' => 'Betrouwbaar', 'subtitle' => 'Jarenlange ervaring'],
                    ] as $bi)
                        <div class="flex items-center gap-3 px-4 py-3">
                            <div class="grid h-10 w-10 place-items-center rounded-lg border border-blue-100 bg-white text-[#1264df]">{{ $bi['emoji'] ?? '' }}</div>
                            <div>
                                <div class="text-[12px] font-black">{{ $bi['title'] ?? '' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $bi['subtitle'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- WAARMEE KUNNEN WE JE HELPEN? --}}
        <section class="py-5 lg:py-7">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <h2 class="mb-5 text-center text-[27px] font-black tracking-[-.03em]">
                    {{ $s['help']['repair_title'] ?? 'Waarmee kunnen we je helpen?' }}
                </h2>

                <div class="relative grid overflow-hidden rounded-[12px] lg:grid-cols-2">
                    <div id="pc-reparatie" class="relative min-h-[250px] overflow-hidden bg-gradient-to-r from-[#061632] to-[#07101d] px-7 py-7 text-white lg:px-10">
                        <img src="{{ asset($s['help']['repair_image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}" alt="" class="absolute bottom-0 left-0 h-full w-[48%] object-contain object-left-bottom opacity-90">
                        <div class="relative z-10 ml-auto max-w-[330px]">
                            <h3 class="text-[25px] font-black uppercase">{{ $s['help']['repair_title'] ?? 'Mijn PC is kapot' }}</h3>
                            <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-[13px]">
                                @foreach ($s['help']['repair_items'] ?? [['title'=>'Start niet'],['title'=>'Valt uit'],['title'=>'Geen beeld'],['title'=>'Maakt geluid'],['title'=>'Wordt te warm'],['title'=>'Overige problemen']] as $ri)
                                    <span>◉ &nbsp; {{ $ri['title'] ?? '' }}</span>
                                @endforeach
                            </div>
                            <a href="/reparatie-aanmelden" class="mt-6 flex h-[42px] items-center justify-center rounded-md border border-[#1972ff] text-[13px] font-bold transition hover:bg-[#1264df]">PC reparatie <span class="ml-auto mr-5">→</span></a>
                        </div>
                    </div>

                    <div class="absolute left-1/2 top-1/2 z-30 hidden h-[70px] w-[70px] -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white text-[15px] font-black shadow-lg lg:grid">OF</div>

                    <div id="pc-op-maat" class="relative min-h-[250px] overflow-hidden bg-gradient-to-r from-[#062918] to-[#03220f] px-7 py-7 text-white lg:px-10">
                        <img src="{{ asset($s['help']['custom_image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}" alt="" class="absolute bottom-0 right-0 h-full w-[48%] object-contain object-right-bottom">
                        <div class="relative z-10 max-w-[335px]">
                            <h3 class="text-[25px] font-black uppercase">{{ $s['help']['custom_title'] ?? 'Ik wil een PC' }}</h3>
                            <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-[13px]">
                                @foreach ($s['help']['custom_items'] ?? [['title'=>'Gaming PC'],['title'=>'Custom build'],['title'=>'Werk / kantoor'],['title'=>'Upgrade bestaande PC'],['title'=>'Foto & video'],['title'=>'Advies op maat']] as $ci)
                                    <span class="text-green-300">◉ &nbsp; {{ $ci['title'] ?? '' }}</span>
                                @endforeach
                            </div>
                            <a href="/reparatie-aanmelden" class="mt-6 flex h-[42px] items-center justify-center rounded-md border border-green-500 text-[13px] font-bold transition hover:bg-green-700">PC samenstellen <span class="ml-auto mr-5">→</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- JOUW PC, JOUW KEUZE --}}
        <section class="bg-gradient-to-b from-white to-[#f8fbff] py-6 lg:py-10">
            <div class="mx-auto max-w-[1250px] px-5">
                <div class="text-center">
                    <h2 class="text-[29px] font-black tracking-[-.035em]">
                        {{ $s['choice']['title1'] ?? 'Jouw PC,' }} <span class="text-[#1264df]">{{ $s['choice']['title2'] ?? 'jouw keuze' }}</span>
                    </h2>
                    <p class="mt-1 text-[11px] text-slate-500">{{ $s['choice']['subtitle'] ?? 'Klik op een onderdeel voor meer informatie' }}</p>
                </div>

                <div class="relative mx-auto mt-7 grid min-h-[470px] max-w-[1100px] items-center lg:grid-cols-[300px_1fr_300px]">
                    <div class="relative z-20 space-y-5">
                        @foreach ($s['choice']['left_items'] ?? [
                            ['emoji' => '▧', 'title' => 'Processor (CPU)', 'description' => "Het brein van je PC.\nMeer kernen, meer kracht."],
                            ['emoji' => '▥', 'title' => 'Werkgeheugen (RAM)', 'description' => "Meer RAM = soepeler multitasken\nen snelheid."],
                            ['emoji' => '▱', 'title' => 'Opslag (SSD / HDD)', 'description' => "Snelle SSD voor snelheid,\nHDD voor capaciteit."],
                        ] as $li)
                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-[0_8px_20px_rgba(15,50,100,.08)]">
                                <div class="flex gap-3">
                                    <span class="text-3xl text-[#1264df]">{{ $li['emoji'] ?? '' }}</span>
                                    <div>
                                        <h3 class="text-[12px] font-black uppercase text-[#0c2760]">{{ $li['title'] ?? '' }}</h3>
                                        <p class="mt-1 text-[10px] leading-4 text-slate-600 whitespace-pre-line">{{ $li['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="relative flex min-h-[430px] items-center justify-center">
                        <div class="absolute h-[340px] w-[460px] rounded-full bg-blue-100/60 blur-[55px]"></div>
                        <img src="{{ asset($s['choice']['center_image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}" alt="PC componenten" class="relative z-10 w-[480px] max-w-full object-contain drop-shadow-[0_25px_30px_rgba(0,41,100,.22)]">
                        <div class="absolute bottom-[-15px] z-20 rounded-xl border border-slate-200 bg-white px-7 py-3 text-center shadow-[0_8px_25px_rgba(15,50,100,.10)]">
                            <div class="text-2xl text-[#1264df]">❄</div>
                            <div class="mt-1 text-[11px] font-black uppercase text-[#0c2760]">{{ $s['choice']['cooling_title'] ?? 'Koeling' }}</div>
                            <p class="mt-1 text-[9px] leading-4 text-slate-500 whitespace-pre-line">{{ $s['choice']['cooling_text'] ?? "Houdt je PC koel en stil.\nBetere prestaties, langere levensduur." }}</p>
                        </div>
                    </div>

                    <div class="relative z-20 space-y-5">
                        @foreach ($s['choice']['right_items'] ?? [
                            ['emoji' => '▦', 'title' => 'Moederbord', 'description' => "Verbindt alles met elkaar.\nKies kwaliteit & stabiliteit."],
                            ['emoji' => '⌁', 'title' => 'Grafische kaart (GPU)', 'description' => "Voor gaming, 3D en zware\ntoepassingen."],
                            ['emoji' => '⬡', 'title' => 'Voeding (PSU)', 'description' => "Stabiele stroom voor een\nveilige en betrouwbare PC."],
                        ] as $ri)
                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-[0_8px_20px_rgba(15,50,100,.08)]">
                                <div class="flex gap-3">
                                    <span class="text-3xl text-[#1264df]">{{ $ri['emoji'] ?? '' }}</span>
                                    <div>
                                        <h3 class="text-[12px] font-black uppercase text-[#0c2760]">{{ $ri['title'] ?? '' }}</h3>
                                        <p class="mt-1 text-[10px] leading-4 text-slate-600 whitespace-pre-line">{{ $ri['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- PC PROBLEMEN --}}
        <section class="py-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="overflow-hidden rounded-[13px] border border-slate-200 bg-white shadow-[0_8px_30px_rgba(15,48,90,.07)]">
                    <div class="grid lg:grid-cols-[1.05fr_2.4fr_1.15fr]">
                        <div class="p-6 lg:p-7">
                            <h2 class="text-[24px] font-black leading-[1.05]">
                                {{ $s['problems']['title1'] ?? 'PC probleem?' }} <span class="block">Wij vinden de <span class="text-[#1264df]">oplossing.</span></span>
                            </h2>
                            <p class="mt-4 max-w-[240px] text-[11px] leading-5 text-slate-600 whitespace-pre-line">{{ $s['problems']['description'] ?? "Van kleine storingen tot complexe problemen,\nwij sporen het op en lossen het vakkundig op." }}</p>
                            <a href="/reparatie-aanmelden" class="mt-5 inline-flex items-center rounded-md bg-[#1264df] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#0a57c8]">
                                PC laten onderzoeken
                                <span class="ml-5">→</span>
                            </a>
                        </div>

                        <div class="grid border-y border-slate-200 sm:grid-cols-3 lg:border-x lg:border-y-0">
                            @foreach ($s['problems']['items'] ?? [
                                ['emoji' => '⏻', 'title' => 'Power', 'points' => 'Geen stroom,Start en valt uit,Ventilatoren draaien niet'],
                                ['emoji' => '▣', 'title' => 'Display', 'points' => 'Geen beeld,Blue screen / errors,GPU problemen'],
                                ['emoji' => '◴', 'title' => 'Performance', 'points' => 'Traag / haperingen,Oververhitting,Crashes / vastlopers'],
                            ] as $pi)
                                <div class="p-6 {{ !$loop->first ? 'border-t sm:border-l sm:border-t-0' : '' }}">
                                    <div class="text-[37px] {{ $pi['title'] == 'Power' ? 'text-green-600' : ($pi['title'] == 'Display' ? 'text-[#1264df]' : 'text-green-600') }}">{{ $pi['emoji'] ?? '' }}</div>
                                    <h3 class="mt-3 text-[12px] font-black uppercase">{{ $pi['title'] ?? '' }}</h3>
                                    <ul class="mt-4 space-y-2 text-[10px] text-slate-600">
                                        @foreach (explode(',', $pi['points'] ?? '') as $pt)
                                            @if (trim($pt) !== '')<li>✓ {{ trim($pt) }}</li>@endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>

                        <div class="min-h-[240px]">
                            <img src="{{ asset($s['problems']['image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}" alt="PC reparatie" class="h-full w-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- UPGRADES --}}
        <section class="py-4">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="overflow-hidden rounded-[13px] border border-slate-200 bg-white shadow-[0_8px_30px_rgba(15,48,90,.07)]">
                    <div class="grid xl:grid-cols-[290px_1fr]">
                        <div class="border-b border-slate-200 p-7 xl:border-b-0 xl:border-r">
                            <h2 class="text-[23px] font-black leading-[1.07]">
                                {{ $s['upgrades']['title1'] ?? 'Niet altijd een' }} <span class="block text-[#1264df]">{{ $s['upgrades']['title2'] ?? 'nieuwe PC nodig.' }}</span>
                            </h2>
                            <p class="mt-4 text-[11px] leading-5 text-slate-600 whitespace-pre-line">{{ $s['upgrades']['description'] ?? "Met de juiste upgrade geef je jouw PC\neen tweede leven en haal je weer het maximale eruit." }}</p>
                            <a href="/reparatie-aanmelden" class="mt-5 inline-flex items-center gap-5 rounded-md border border-[#1264df] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#1264df] hover:bg-[#f2f7ff] transition">
                                Upgrade advies
                                <span>→</span>
                            </a>
                        </div>

                        <div class="grid sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($s['upgrades']['items'] ?? [
                                ['title' => 'Opslag upgrade', 'before_label' => 'HDD 1TB', 'before_image' => 'assets/img/landing/pc/hdd.png', 'before_spec' => '100 MB/s', 'after_label' => 'NVMe SSD 1TB', 'after_image' => 'assets/img/landing/pc/nvme.png', 'after_spec' => '3500 MB/s'],
                                ['title' => 'Geheugen upgrade', 'before_label' => '8GB RAM', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => '32GB RAM', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                                ['title' => 'Koeling upgrade', 'before_label' => 'Standaard koeler', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => 'Premium koeler', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                                ['title' => 'Grafische kaart upgrade', 'before_label' => 'GTX 1650', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => 'RTX 4060', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                            ] as $up)
                                <div class="border-b border-slate-200 p-5 text-center {{ $loop->index % 2 == 1 ? 'sm:border-r-0' : 'sm:border-r' }} lg:border-b-0 {{ !$loop->last ? 'lg:border-r' : '' }}">
                                    <div class="text-[10px] font-black uppercase text-[#1264df]">{{ $up['title'] ?? '' }}</div>
                                    <div class="mt-4 flex items-end justify-center gap-4">
                                        <div>
                                            <div class="text-[9px] font-bold">{{ $up['before_label'] ?? '' }}</div>
                                            @php $bImg = $up['before_image'] ?? ''; $bSrc = $bImg ? (str_starts_with($bImg, 'assets/') ? asset($bImg) : asset('assets/img/landing/' . ltrim($bImg, '/'))) : ''; @endphp
                                            <img src="{{ $bSrc }}" alt="" class="mx-auto mt-2 h-[75px] object-contain">
                                            @if (!empty($up['before_spec']))<div class="mt-2 text-[14px] font-black text-[#092e78]">{{ $up['before_spec'] }}</div>@endif
                                        </div>
                                        <div class="pb-8 text-[22px] text-[#1264df]">→</div>
                                        <div>
                                            <div class="text-[9px] font-bold">{{ $up['after_label'] ?? '' }}</div>
                                            @php $aImg = $up['after_image'] ?? ''; $aSrc = $aImg ? (str_starts_with($aImg, 'assets/') ? asset($aImg) : asset('assets/img/landing/' . ltrim($aImg, '/'))) : ''; @endphp
                                            <img src="{{ $aSrc }}" alt="" class="mx-auto mt-2 h-[75px] object-contain">
                                            @if (!empty($up['after_spec']))<div class="mt-2 text-[14px] font-black text-[#092e78]">{{ $up['after_spec'] }}</div>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- PC BUILDS --}}
        <section class="py-8">
            <div class="mx-auto max-w-[1420px] px-5">
                <div class="text-center">
                    <h2 class="text-[29px] font-black tracking-[-.035em]">
                        {{ $s['builds']['title1'] ?? 'PC builds door' }} <span class="text-[#1264df]">{{ $s['builds']['title2'] ?? 'Slimme-PC' }}</span>
                    </h2>
                    <p class="mt-1 text-[11px] text-slate-500">{{ $s['builds']['subtitle'] ?? 'Met zorg samengesteld. Op maat gebouwd. 100% getest.' }}</p>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ($s['builds']['items'] ?? [
                        ['badge' => 'Gaming Beast', 'title' => 'Voor de beste game-ervaring', 'description' => 'High FPS • Stil • Krachtig', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                        ['badge' => 'Creator Pro', 'title' => 'Voor foto, video & 3D', 'description' => 'Stabiel • Snel • Betrouwbaar', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                        ['badge' => 'Workstation', 'title' => 'Voor werk & productiviteit', 'description' => 'Efficiënt • Uitbreidbaar • Stil', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                        ['badge' => 'Office PC', 'title' => 'Voor kantoor & dagelijks gebruik', 'description' => 'Snel • Betrouwbaar • Voordelig', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                    ] as $bld)
                        @php $bImg = $bld['image'] ?? ''; $bSrc = $bImg ? (str_starts_with($bImg, 'assets/') ? asset($bImg) : asset('assets/img/landing/' . ltrim($bImg, '/'))) : ''; @endphp
                        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_7px_20px_rgba(0,38,90,.07)] transition hover:-translate-y-1 hover:shadow-xl">
                            <img src="{{ $bSrc }}" class="h-[170px] w-full object-cover" alt="{{ $bld['title'] ?? '' }}">
                            <div class="p-4">
                                <div class="text-[10px] font-black uppercase text-[#1264df]">{{ $bld['badge'] ?? '' }}</div>
                                <h3 class="mt-2 text-[11px] font-semibold">{{ $bld['title'] ?? '' }}</h3>
                                <p class="mt-1 text-[10px] text-slate-500">{{ $bld['description'] ?? '' }}</p>
                                <div class="mt-3 text-[11px] text-amber-500">★★★★★</div>
                            </div>
                        </article>
                    @endforeach

                    <article class="flex flex-col justify-center rounded-xl border border-blue-100 bg-gradient-to-br from-[#f5f9ff] to-[#e6f0ff] p-7">
                        <div class="text-4xl text-[#1264df]">▣</div>
                        <h3 class="mt-5 text-[21px] font-black leading-tight">Jouw wensen,<br>jouw PC.</h3>
                        <p class="mt-4 text-[11px] leading-5 text-slate-600">Wij bouwen een PC die past bij jouw gebruik, budget en stijl.</p>
                        <a href="/reparatie-aanmelden" class="mt-5 flex h-10 items-center justify-center rounded-md border border-[#1264df] text-[11px] font-bold text-[#1264df] hover:bg-white transition">
                            PC op maat
                            <span class="ml-5">→</span>
                        </a>
                    </article>
                </div>
            </div>
        </section>

        {{-- WAAROM PC OP MAAT --}}
        <section class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="rounded-[14px] border border-slate-200 bg-white px-5 py-6 shadow-[0_7px_25px_rgba(0,38,90,.06)]">
                    <div class="grid gap-8 md:grid-cols-3 xl:grid-cols-6">
                        @foreach ($s['why']['items'] ?? [
                            ['emoji' => '◉', 'title' => 'Prestaties op maat', 'description' => 'Alleen wat je nodig hebt, geen onnodige kosten.'],
                            ['emoji' => '✥', 'title' => 'Betere kwaliteit', 'description' => 'Hoogwaardige onderdelen van topmerken.'],
                            ['emoji' => '⌘', 'title' => 'Uitbreidbaar', 'description' => 'Makkelijk te upgraden in de toekomst.'],
                            ['emoji' => '♢', 'title' => 'Professioneel gebouwd', 'description' => 'Netjes kabelmanagement en optimale koeling.'],
                            ['emoji' => '◌', 'title' => '100% getest', 'description' => 'We testen de PC uitgebreid voor levering.'],
                            ['emoji' => '♙', 'title' => 'Persoonlijk advies', 'description' => 'Wij denken met je mee voor het beste resultaat.'],
                        ] as $wi)
                            <div class="text-center">
                                <div class="mx-auto text-[28px] text-[#1264df]">{{ $wi['emoji'] ?? '' }}</div>
                                <h3 class="mt-2 text-[11px] font-black">{{ $wi['title'] ?? '' }}</h3>
                                <p class="mx-auto mt-1 max-w-[150px] text-[9px] leading-4 text-slate-500">{{ $wi['description'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ + CTA --}}
        <section class="pb-10 pt-2">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid gap-5 lg:grid-cols-[.85fr_1.05fr_1.25fr]">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-[17px] font-black">{{ $s['faq_cta']['faq_title'] ?? 'Veelgestelde vragen' }}</h2>
                        <div class="mt-4 divide-y divide-slate-200">
                            @foreach ($s['faq_cta']['faq_items'] ?? [
                                ['question' => 'Hoe lang duurt een PC reparatie?', 'answer' => 'De meeste reparaties worden binnen één tot enkele werkdagen uitgevoerd.'],
                                ['question' => 'Wat kost een diagnose?', 'answer' => 'We bekijken eerst het probleem en bespreken daarna duidelijk de mogelijkheden.'],
                                ['question' => 'Bouwen jullie ook gaming PC\'s?', 'answer' => 'Ja. We bouwen gaming PC\'s volledig afgestemd op jouw games en budget.'],
                                ['question' => 'Kan ik mijn eigen onderdelen aanleveren?', 'answer' => 'Dat kan in overleg. We controleren vooraf de compatibiliteit.'],
                            ] as $fi)
                                <details class="group">
                                    <summary class="flex cursor-pointer list-none items-center justify-between py-3 text-[10px] font-bold">{{ $fi['question'] ?? '' }} <span class="text-lg group-open:hidden">+</span><span class="hidden text-lg group-open:inline">−</span></summary>
                                    <p class="pb-3 text-[10px] leading-5 text-slate-500">{{ $fi['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                        <a href="#" class="mt-3 flex items-center justify-end gap-2 text-[9px] font-bold text-[#1264df]">Bekijk alle veelgestelde vragen →</a>
                    </div>

                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1768e5] to-[#0050cd] p-7 text-white shadow-[0_12px_35px_rgba(18,100,223,.25)]">
                        <div class="absolute -right-16 -top-20 h-[240px] w-[240px] rounded-full bg-white/10 blur-2xl"></div>
                        <div class="relative z-10">
                            <h2 class="text-[24px] font-black">{{ $s['faq_cta']['cta_title'] ?? 'Klaar voor een betere PC?' }}</h2>
                            <p class="mt-2 text-[11px] leading-5 text-blue-100 whitespace-pre-line">{{ $s['faq_cta']['cta_description'] ?? "Laat je PC repareren of stel jouw ideale PC samen.\nWij helpen je graag verder!" }}</p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="/reparatie-aanmelden" class="rounded-md bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#1264df] transition hover:bg-slate-100">PC reparatie aanvragen →</a>
                                <a href="/reparatie-aanmelden" class="rounded-md bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#1264df] transition hover:bg-slate-100">PC samenstellen →</a>
                            </div>
                            <div class="mt-6 flex items-center gap-3 text-[13px] font-semibold">☎ <span>Bel ons direct: 055 203 21 45</span></div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <img src="{{ asset($s['faq_cta']['cta_image'] ?? 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png') }}" alt="PC reparatie" class="h-[180px] w-full object-cover rounded-xl">
                        <h3 class="mt-4 text-[14px] font-black">PC op maat</h3>
                        <p class="mt-2 text-[11px] text-slate-600">Jouw wensen, jouw PC — volledig op maat gebouwd en getest.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')
@endsection

@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <style>
        .hero-bg {
            background:
                radial-gradient(circle at 67% 45%, rgba(0, 110, 255, .18), transparent 26%),
                radial-gradient(circle at 84% 25%, rgba(0, 137, 255, .12), transparent 20%),
                linear-gradient(90deg, #031326 0%, #04162b 48%, #031326 100%);
        }
        .grid-lines {
            background-image:
                linear-gradient(rgba(31, 116, 215, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(31, 116, 215, 0.08) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: linear-gradient(to bottom, transparent, black 30%, black 70%, transparent);
        }
        .blue-glow { filter: drop-shadow(0 0 18px rgba(32, 144, 255, .30)); }
        @media(max-width: 1024px) { .diagnostic-line { display: none; } }
    </style>

    <main class="overflow-hidden bg-white text-[#101827]">

        {{-- HERO --}}
        <section class="hero-bg relative overflow-hidden text-white">
            <div class="grid-lines absolute inset-0 opacity-60"></div>

            <div class="relative max-w-[1440px] mx-auto px-6 lg:px-14 pt-5 pb-9 lg:pt-6 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-white/75 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-white transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-white transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-white font-semibold">{{ config('cms.pages.moederbord.label') ?? 'Moederbord Reparatie' }}</span>
                </div>

                <div class="grid lg:grid-cols-[.92fr_1.08fr] items-center gap-8">

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 text-[#2d9cff] font-bold uppercase text-[11px] mb-4">
                            <i data-lucide="circle-check" class="w-4 h-4"></i>
                            {{ $s['hero']['badge'] ?? 'Moederbord reparatie Apeldoorn' }}
                        </div>

                        <h1 class="font-black tracking-[-0.045em] leading-[1.02] text-[30px] sm:text-[52px] lg:text-[40px]">
                            {{ $s['hero']['title1'] ?? 'Defect moederbord?' }}
                            <span class="block text-[#2d9cff] mt-1">{{ $s['hero']['title2'] ?? 'Wij repareren verder' }}</span>
                            <span class="block mt-1">{{ $s['hero']['title3'] ?? 'waar anderen stoppen.' }}</span>
                        </h1>

                        <p class="mt-6 text-white/85 text-[15px] sm:text-[16px] leading-7 max-w-[530px] whitespace-pre-line">{{ $s['hero']['description'] ?? "Geen onnodige vervanging. Eerst meten.\nDan repareren. Op componentniveau." }}</p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-8 max-w-[720px]">
                            @foreach ($s['hero']['usps'] ?? [
                                ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & duidelijk'],
                                ['icon' => 'microchip', 'title' => 'Component level repair', 'subtitle' => 'Wij vervangen niet het hele bord'],
                                ['icon' => 'flask-conical', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak dezelfde dag klaar'],
                                ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op reparaties & onderdelen'],
                            ] as $usp)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full border border-[#2598ff] flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $usp['icon'] ?? 'shield-check' }}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-[12px]">{{ $usp['title'] ?? '' }}</p>
                                        <p class="text-white/60 text-[10px] mt-1">{{ $usp['subtitle'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-8">
                            <a href="/reparatie-aanmelden"
                               class="inline-flex items-center justify-center gap-6 bg-[#0b63e5] hover:bg-[#0a57c8] px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-bold text-[14px] sm:text-[15px] transition">
                                Moederbord aanmelden
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                            <a href="#reparaties"
                               class="inline-flex items-center justify-center gap-4 border border-white/75 px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-semibold text-[14px] sm:text-[15px] hover:bg-white/10 transition">
                                Bekijk echte reparaties
                                <i data-lucide="play-circle" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>

                    <div class="relative min-h-[350px] lg:min-h-[480px]">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="absolute w-[430px] h-[300px] bg-[#0878ff]/20 blur-[110px] rounded-full"></div>
                            <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/fd440a8c-4dba-4e09-86a1-b71edb28ea87.png') }}"
                                 alt="Moederbord reparatie Slimme-PC"
                                 class="relative z-10 w-[82%] max-w-[570px] object-contain blue-glow lg:-translate-x-5 lg:translate-y-2">
                        </div>
                        <div class="absolute right-[3%] top-[34%] bg-[#06172c]/95 border border-[#258fff] text-white px-4 py-2 rounded-md font-bold text-[13px] sm:text-[17px]">CPU ✓</div>
                        <div class="absolute right-[1%] top-[55%] bg-[#06172c]/95 border border-[#258fff] text-white px-4 py-2 rounded-md font-bold text-[13px] sm:text-[17px]">3.3V ✓</div>
                        <div class="absolute right-[1%] bottom-[14%] bg-[#1b0811]/95 border border-red-500 text-red-400 px-4 py-2 rounded-md font-bold text-[13px] sm:text-[17px]">SHORT !</div>
                    </div>

                </div>
            </div>
            <div class="h-[2px] bg-[#208cff]"></div>
        </section>

        {{-- VAN KLACHT NAAR OPLOSSING --}}
        <section class="py-12 lg:py-16 bg-white">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="grid lg:grid-cols-[.65fr_1.35fr] gap-10 items-center">
                    <div>
                        <h2 class="text-[36px] lg:text-[43px] leading-[1.05] font-black tracking-tight">
                            {{ $s['process']['title1'] ?? 'Van klacht' }}
                            <span class="block text-[#0b63e5]">{{ $s['process']['title2'] ?? 'naar oplossing' }}</span>
                        </h2>
                        <p class="text-gray-700 mt-5 leading-7 text-[15px] max-w-[340px] whitespace-pre-line">{{ $s['process']['description'] ?? "Wij doorlopen een gestructureerd diagnoseproces om het echte\nprobleem te vinden en gericht te repareren." }}</p>
                    </div>

                    <div class="relative min-h-[380px] lg:min-h-[430px]">
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[280px] rounded-full border border-blue-100 flex items-center justify-center">
                            <div class="absolute inset-[28px] rounded-full border border-dashed border-[#9acaff]"></div>
                            <img src="{{ asset($s['process']['center_image'] ?? 'assets/img/landing/363f8f55-fba7-4f23-88db-8c8e728d522e.png') }}" class="relative z-10 w-[340px] object-contain" alt="">
                        </div>

                        @php
                            $procIcons = ['laptop','activity','microchip','syringe','shield-check'];
                            $procTitles = ['KLACHT','METING','COMPONENT','REPARATIE','TEST'];
                            $procSubs = ['Bijv. laptop start niet meer','Voeding & signalen worden gemeten','Defect onderdeel wordt gelokaliseerd','Microsolderen & vervangen van defecte onderdelen','Grondig getest voor 100% zekerheid'];
                        @endphp
                        @foreach ($s['process']['items'] ?? [
                            ['icon' => 'laptop', 'title' => 'KLACHT', 'subtitle' => 'Bijv. laptop start niet meer'],
                            ['icon' => 'activity', 'title' => 'METING', 'subtitle' => 'Voeding & signalen worden gemeten'],
                            ['icon' => 'microchip', 'title' => 'COMPONENT', 'subtitle' => 'Defect onderdeel wordt gelokaliseerd'],
                            ['icon' => 'syringe', 'title' => 'REPARATIE', 'subtitle' => 'Microsolderen & vervangen van defecte onderdelen'],
                            ['icon' => 'shield-check', 'title' => 'TEST', 'subtitle' => 'Grondig getest voor 100% zekerheid'],
                        ] as $idx => $it)
                            @php
                                $pos = [
                                    'absolute left-[4%] top-[3%] w-[200px]',
                                    'absolute right-[0%] top-[3%] w-[215px]',
                                    'absolute right-[2%] bottom-[15%] w-[215px]',
                                    'absolute left-1/2 bottom-[0%] -translate-x-1/2 w-[230px]',
                                    'absolute left-[2%] bottom-[15%] w-[200px]',
                                ][$idx] ?? '';
                            @endphp
                            <div class="{{ $pos }} bg-white shadow-card border border-gray-200 rounded-xl p-5 max-lg:static max-lg:mx-auto max-lg:mb-4 max-lg:w-full max-lg:max-w-[320px]">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="{{ $it['icon'] ?? $procIcons[$idx] }}" class="w-8 h-8 text-[#0b63e5] shrink-0"></i>
                                    <div>
                                        <h3 class="font-black text-[15px]">{{ $it['title'] ?? $procTitles[$idx] }}</h3>
                                        <p class="text-[11px] text-gray-600 mt-1">{{ $it['subtitle'] ?? $procSubs[$idx] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- REAL WORKBENCH + VIDEO --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="bg-gradient-to-r from-[#03172d] to-[#06213f] rounded-xl overflow-hidden shadow-soft p-6 lg:p-7">
                    <div class="grid lg:grid-cols-[.75fr_1.3fr_.55fr] gap-7 items-center">

                        <div class="text-white">
                            <div class="text-[#2d9cff] uppercase font-semibold text-[11px]">Dit gebeurt er op onze werkbank</div>
                            <h2 class="text-[31px] lg:text-[35px] font-black leading-[1.05] mt-4">
                                {{ $s['workbench']['title'] ?? 'Echte reparatie.' }}<br>
                                Geen trucjes.<br>
                                Gewoon <span class="text-[#2d9cff]">{{ $s['workbench']['highlight'] ?? 'vakwerk.' }}</span>
                            </h2>
                            <p class="mt-5 text-white/80 text-[13px] leading-6 whitespace-pre-line">{{ $s['workbench']['description'] ?? "Onder de microscoop zoeken we naar de oorzaak\nen repareren we op componentniveau." }}</p>
                            <div class="grid grid-cols-3 gap-3 mt-7 text-center">
                                @foreach ($s['workbench']['features'] ?? [['icon' => 'microscope','title'=>'Ervaren technici'],['icon'=>'settings','title'=>'Professionele apparatuur'],['icon'=>'shield-check','title'=>'Nauwkeurig & veilig']] as $f)
                                    <div>
                                        <i data-lucide="{{ $f['icon'] ?? 'microscope' }}" class="w-7 h-7 text-[#2d9cff] mx-auto"></i>
                                        <p class="text-[9px] mt-2 text-white/70">{{ $f['title'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative overflow-hidden rounded-xl border border-[#208cff] min-h-[330px] bg-black">
                            @if (!empty($s['workbench']['video']))
                                <video class="absolute inset-0 w-full h-full object-cover" controls preload="metadata" poster="{{ asset($s['workbench']['video_poster'] ?? 'assets/img/landing/e4703bd3-ffe8-4ca1-8543-7f5a97484698.png') }}">
                                    <source src="{{ asset($s['workbench']['video']) }}" type="video/mp4">
                                </video>
                            @else
                                <img src="{{ asset($s['workbench']['video_poster'] ?? 'assets/img/landing/e4703bd3-ffe8-4ca1-8543-7f5a97484698.png') }}" class="absolute inset-0 w-full h-full object-cover" alt="Moederbord reparatie onder microscoop">
                                <button class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[72px] h-[72px] rounded-full bg-[#07182c]/90 border border-[#2495ff] flex items-center justify-center">
                                    <i data-lucide="play" class="w-8 h-8 text-white fill-white ml-1"></i>
                                </button>
                                <div class="absolute left-4 bottom-3 right-4 flex items-center gap-3 text-white">
                                    <i data-lucide="play" class="w-3 h-3 fill-white"></i>
                                    <span class="text-[10px]">0:00 / 0:45</span>
                                    <div class="h-[3px] flex-1 bg-white/30 rounded"><div class="h-full w-[18%] bg-white rounded"></div></div>
                                </div>
                            @endif
                        </div>

                        <div class="text-white">
                            <div class="text-[#2d9cff] font-bold uppercase text-[10px] mb-5">Onze repair lab</div>
                            <div class="space-y-4 text-[12px]">
                                @foreach ($s['workbench']['lab_items'] ?? [['title'=>'Microscoop inspectie'],['title'=>'Soldeerstation (JBC)'],['title'=>'DC Power Supply'],['title'=>'Digitale Multimeter'],['title'=>'Oscilloscoop'],['title'=>'ESD veilige werkplek']] as $li)
                                    <div class="flex gap-3 items-center">
                                        <span class="w-5 h-5 rounded-full bg-[#0b63e5] flex items-center justify-center shrink-0">✓</span>
                                        {{ $li['title'] ?? '' }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6">
                                <div class="flex justify-between text-[11px] text-[#2d9cff]"><span>Component level repair</span><span class="text-white text-[15px]">100%</span></div>
                                <div class="h-[5px] bg-white/20 rounded-full mt-2 overflow-hidden"><div class="h-full w-full bg-[#168cff] rounded-full"></div></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        {{-- WHAT WE REPAIR --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-6">
                    <h2 class="font-black text-[31px] lg:text-[36px]">
                        {{ $s['repairs']['title'] ?? 'Wat repareren wij op een moederbord?' }}
                    </h2>
                    <p class="text-[12px] text-gray-600 mt-2">
                        {{ $s['repairs']['subtitle'] ?? 'Wij repareren alleen het defecte onderdeel, niet onnodig het hele moederbord.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($s['repairs']['items'] ?? [
                        ['icon' => 'zap', 'title' => 'Voeding (MOSFET / IC)', 'description' => 'Defecte voedingen worden opgespoord en vervangen.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                        ['icon' => 'plug', 'title' => 'Laadcircuit (DC / USB-C)', 'description' => 'Reparatie van laadpoort, laad-IC en power circuits.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                        ['icon' => 'cpu', 'title' => 'BIOS / Firmware', 'description' => 'BIOS problemen, corrupte chip of herprogrammeren.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                        ['icon' => 'laptop-minimal', 'title' => 'Connectoren & Poorten', 'description' => 'HDMI, USB, audio, DC-jack en andere connectoren.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                        ['icon' => 'activity', 'title' => 'Kortsluiting Opsporen', 'description' => 'Short circuit detectie en reparatie op componentniveau.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                        ['icon' => 'brain-circuit', 'title' => 'Component Vervangen', 'description' => 'IC, capacitors, resistors, coil, transistor en meer.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ] as $card)
                        <article class="repair-card bg-white border border-gray-200 rounded-xl shadow-card overflow-hidden">
                            <div class="p-4 min-h-[110px]">
                                <div class="flex gap-3">
                                    <i data-lucide="{{ $card['icon'] ?? 'cpu' }}" class="w-8 h-8 text-[#0b63e5] shrink-0"></i>
                                    <div>
                                        <h3 class="font-black text-[12px]">{{ $card['title'] ?? '' }}</h3>
                                        <p class="text-[10px] text-gray-600 leading-4 mt-2">{{ $card['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/landing/' . basename($card['image'] ?? 'assets/img/landing/what-computer-chips-made-of.jpg')) }}" class="w-full h-[125px] object-cover" alt="">
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- VS COMPARISON --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="relative grid lg:grid-cols-2 overflow-hidden rounded-xl">
                    <div class="relative bg-[#111820] text-white p-7 lg:p-8 min-h-[220px]">
                        <div class="absolute inset-0 opacity-20"><img src="{{ asset('assets/img/landing/what-computer-chips-made-of.jpg') }}" class="w-full h-full object-cover" alt=""></div>
                        <div class="relative z-10">
                            <h3 class="font-black text-[23px]">{{ $s['compare']['left_title'] ?? 'Het hele moederbord vervangen?' }}</h3>
                            <div class="space-y-3 mt-6 text-[13px]">
                                @foreach ($s['compare']['left_items'] ?? [['title'=>'Zeer hoge kosten'],['title'=>'Gegevens kunnen verloren gaan'],['title'=>'Niet altijd direct beschikbaar'],['title'=>'Niet altijd de echte oplossing']] as $li)
                                    <div class="flex gap-3 items-center"><span class="text-red-500 font-black text-lg">×</span> {{ $li['title'] ?? '' }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="relative bg-gradient-to-r from-[#074db8] to-[#062a7b] text-white p-7 lg:p-8 min-h-[220px]">
                        <div class="absolute right-7 top-1/2 -translate-y-1/2 opacity-50"><i data-lucide="shield-check" class="w-[110px] h-[110px] text-[#168cff]"></i></div>
                        <div class="relative z-10">
                            <h3 class="font-black text-[23px]">{{ $s['compare']['right_title'] ?? 'Slimme-PC repareert op componentniveau' }}</h3>
                            <div class="space-y-3 mt-6 text-[13px]">
                                @foreach ($s['compare']['right_items'] ?? [['title'=>'Alleen het defecte onderdeel vervangen'],['title'=>'Lagere kosten'],['title'=>'Gegevens blijven behouden'],['title'=>'Duurzame oplossing']] as $ri)
                                    <div class="flex gap-3 items-center"><span class="text-green-400 font-black">✓</span> {{ $ri['title'] ?? '' }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[75px] h-[75px] bg-white rounded-full flex items-center justify-center text-[#101827] font-black text-[22px] shadow-lg z-20 max-lg:hidden">VS</div>
                </div>
            </div>
        </section>

        {{-- REAL CASES --}}
        <section id="reparaties" class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-6">
                    <h2 class="font-black text-[30px] lg:text-[35px]">
                        {{ $s['cases']['title'] ?? 'Echte moederbord reparaties bij Slimme-PC' }}
                    </h2>
                    <p class="text-gray-500 text-[11px] mt-2">
                        {{ $s['cases']['subtitle'] ?? 'Voorbeelden van succesvolle component level reparaties.' }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-5">
                    @foreach ($s['cases']['items'] ?? [
                        ['badge' => 'CASE 01', 'title' => 'Laptop laadt niet', 'defect' => 'Defect: Charging IC', 'solution' => 'Oplossing: IC vervangen', 'image' => 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp'],
                        ['badge' => 'CASE 02', 'title' => 'Geen beeld', 'defect' => 'Defect: BIOS probleem', 'solution' => 'Oplossing: BIOS geprogrammeerd', 'image' => 'assets/img/landing/e6CDTwkoydKhv1YqP7v960jbKDJiHFBxVh8og5LH.png'],
                        ['badge' => 'CASE 03', 'title' => 'Dood moederbord', 'defect' => 'Kortsluiting in power circuit', 'solution' => 'Oplossing: MOSFET vervangen', 'image' => 'assets/img/landing/589e8caa-b215-46bc-8687-99fbcae79b5a.png'],
                    ] as $cs)
                        <article class="case-card border border-gray-200 rounded-xl bg-white shadow-card overflow-hidden">
                            <div class="grid grid-cols-[.9fr_1.1fr]">
                                <div class="p-5">
                                    <div class="text-[#0b63e5] uppercase font-black text-[9px]">{{ $cs['badge'] ?? '' }}</div>
                                    <h3 class="font-black text-[16px] mt-2">{{ $cs['title'] ?? '' }}</h3>
                                    <p class="text-[11px] text-gray-600 mt-3">{{ $cs['defect'] ?? '' }}</p>
                                    <p class="text-[11px] text-gray-600">{{ $cs['solution'] ?? '' }}</p>
                                    <p class="text-green-600 font-bold text-[11px] mt-3">Gerepareerd ✓</p>
                                </div>
                                <div class="relative">
                                    <img src="{{ asset('assets/img/landing/' . basename($cs['image'] ?? '')) }}" class="w-full h-full object-cover min-h-[170px]" alt="">
                                    <a href="/reparatie-aanmelden" class="absolute bottom-2 right-2 bg-white px-3 py-2 text-[10px] font-semibold rounded-md">Bekijk details →</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- FAQ + CTA --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="grid lg:grid-cols-[.75fr_1.25fr] gap-5">
                    <div class="border border-gray-200 rounded-xl bg-white shadow-card p-6">
                        <h2 class="font-black text-[20px] mb-2">
                            {{ $s['faq']['title'] ?? 'Veelgestelde vragen' }}
                        </h2>
                        @foreach ($s['faq']['items'] ?? [
                            ['question' => 'Kan elk moederbord gerepareerd worden?', 'answer' => 'Niet elk defect kan worden hersteld, maar veel problemen op componentniveau zijn wel degelijk te repareren.'],
                            ['question' => 'Hoe lang duurt een moederbord reparatie?', 'answer' => 'Dit hangt af van het defect en de beschikbaarheid van componenten.'],
                            ['question' => 'Wat kost een moederbord reparatie?', 'answer' => 'De kosten zijn afhankelijk van de diagnose en het beschadigde circuit of component.'],
                            ['question' => 'Is de reparatie betrouwbaar?', 'answer' => 'Na de reparatie testen wij het apparaat uitgebreid voordat het wordt teruggegeven.'],
                        ] as $fi)
                            <div class="faq-item border-b last:border-0">
                                <button class="faq-toggle w-full flex justify-between py-4 text-left font-semibold text-[12px]">
                                    {{ $fi['question'] ?? '' }}
                                    <span class="faq-plus text-[#0b63e5] text-xl">+</span>
                                </button>
                                <div class="faq-content"><p class="pb-4 text-gray-600 text-[11px] leading-5">{{ $fi['answer'] ?? '' }}</p></div>
                            </div>
                        @endforeach
                    </div>

                    <div id="aanmelden" class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#062c72] via-[#064aa8] to-[#031d45] min-h-[270px]">
                        <div class="absolute right-0 inset-y-0 w-[45%] max-lg:hidden">
                            <img src="{{ asset($s['cta']['image'] ?? 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp') }}" class="w-full h-full object-cover" alt="">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#064aa8] via-[#064aa8]/55 to-transparent"></div>
                        </div>
                        <div class="relative z-10 p-8 lg:p-10 max-w-[63%] max-lg:max-w-full text-white">
                            <h2 class="font-black text-[30px] lg:text-[35px] leading-tight">
                                {{ $s['cta']['title1'] ?? 'Moederbord defect betekent' }}
                                <span class="block text-[#3ca5ff]">{{ $s['cta']['title2'] ?? 'niet automatisch einde laptop.' }}</span>
                            </h2>
                            <p class="text-white/85 text-[12px] leading-5 mt-5 whitespace-pre-line">{{ $s['cta']['description'] ?? "Laat ons eerst onderzoeken wat er werkelijk defect is." }}</p>
                            <div class="flex flex-wrap gap-3 sm:gap-4 mt-7">
                                <a href="/reparatie-aanmelden" class="bg-[#0b63e5] hover:bg-[#0755c8] text-white px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-bold text-[14px] sm:text-[15px] flex items-center gap-4 transition">
                                    Moederbord laten onderzoeken
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>
                                <a href="tel:{{ str_replace(' ', '', $s['cta']['phone'] ?? '0552032145') }}" class="border border-white/70 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg flex items-center gap-3 hover:bg-white/10 transition">
                                    <i data-lucide="phone" class="w-5 h-5"></i>
                                    <span><small class="block text-[9px] opacity-75">Bel ons direct</small><strong class="text-[12px]">{{ $s['cta']['phone'] ?? '055 203 21 45' }}</strong></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- BOTTOM USP --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="bg-[#f8fafc] border border-gray-100 rounded-xl px-6 py-5">
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach ($s['benefits']['items'] ?? [
                            ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & transparant'],
                            ['icon' => 'microchip', 'title' => 'Component level repair', 'subtitle' => 'Niet vervangen, maar repareren'],
                            ['icon' => 'gauge', 'title' => 'Snelle service', 'subtitle' => 'Vaak dezelfde dag klaar'],
                            ['icon' => 'shield', 'title' => 'Garantie op reparaties', 'subtitle' => 'Op onderdelen & arbeid'],
                            ['icon' => 'lock-keyhole', 'title' => 'Veilig & betrouwbaar', 'subtitle' => 'ESD veilig & professioneel'],
                        ] as $bi)
                            <div class="flex gap-3 items-center">
                                <i data-lucide="{{ $bi['icon'] ?? 'shield-check' }}" class="w-7 h-7 text-[#0b63e5] shrink-0"></i>
                                <div>
                                    <p class="font-bold text-[11px]">{{ $bi['title'] ?? '' }}</p>
                                    <p class="text-gray-500 text-[9px] mt-1">{{ $bi['subtitle'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')

    <script>
    document.querySelectorAll('.faq-toggle').forEach(button => {
        button.addEventListener('click', function () {
            const item = this.closest('.faq-item');
            document.querySelectorAll('.faq-item').forEach(other => { if (other !== item) other.classList.remove('active'); });
            item.classList.toggle('active');
        });
    });
    </script>
@endsection

@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="overflow-hidden bg-[#fbfdff] text-[#07153d]">

        {{-- HERO --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-white via-[#f8fbff] to-[#eef5ff]">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute right-[3%] top-[5%] h-[500px] w-[650px] rounded-full bg-blue-100/40 blur-[100px]"></div>
            </div>

            <div class="relative mx-auto max-w-[1500px] px-5 lg:px-8 pt-5 pb-10 lg:pt-6 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-slate-700 transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-slate-700 transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-[#07153d] font-semibold">{{ config('cms.pages.netwerk.label') ?? 'Netwerkoplossingen' }}</span>
                </div>

                <div class="grid min-h-[590px] items-center gap-8 lg:grid-cols-[.83fr_1.35fr]">

                    <!-- HERO LEFT -->
                    <div class="relative z-20">
                        <p class="mb-5 text-[13px] font-black uppercase tracking-[.02em] text-[#0862e6]">
                            {{ $s['hero']['badge'] ?? 'NETWERKOPLOSSINGEN · APELDOORN' }}
                        </p>

                        <h1 class="max-w-[600px] text-[30px] sm:text-[52px] lg:text-[40px] font-black leading-[.98] tracking-[-.045em] text-[#09153d]">
                            {{ $s['hero']['title1'] ?? 'Sterk netwerk.' }}
                            <span class="mt-1 block text-[#1264df]">{{ $s['hero']['title2'] ?? 'Altijd verbonden.' }}</span>
                        </h1>

                        <p class="mt-6 max-w-[500px] text-[16px] sm:text-[17px] leading-[1.65] text-slate-700 whitespace-pre-line">{{ $s['hero']['description'] ?? "Betrouwbare netwerken voor thuis en bedrijven.\nVan stabiele WiFi en bekabeling tot complete netwerkinstallaties en beheer." }}</p>

                        <div class="mt-7 grid max-w-[570px] grid-cols-1 gap-x-8 gap-y-3 text-[13px] font-semibold sm:grid-cols-2">
                            @foreach ($s['hero']['bullets'] ?? [['title' => 'Snel & stabiel internet'], ['title' => 'WiFi dekking in elke ruimte'], ['title' => 'Professionele installatie'], ['title' => 'Bekabeld of draadloos'], ['title' => 'Netwerk voor thuis & zakelijk'], ['title' => 'Onderhoud & beheer']] as $b)
                                <div class="flex items-center gap-3">
                                    <span class="grid h-[19px] w-[19px] place-items-center rounded-full bg-[#1264df] text-[10px] text-white">✓</span>
                                    {{ $b['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3 sm:gap-4">
                            <a href="/reparatie-aanmelden"
                               class="group inline-flex items-center justify-between rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white shadow-[0_12px_30px_rgba(0,80,210,.18)] transition hover:-translate-y-[2px] hover:bg-[#044dbd]">
                                Gratis netwerkadvies
                                <span class="ml-3 text-xl transition group-hover:translate-x-1">→</span>
                            </a>
                            <a href="#oplossingen"
                               class="inline-flex items-center justify-between gap-2 rounded-[6px] border border-[#1264df] bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#07153d] transition hover:bg-[#f3f7ff]">
                                Bekijk oplossingen
                                <span class="ml-3 grid grid-cols-2 gap-[2px]">
                                    <i class="h-[4px] w-[4px] rounded-[1px] border border-[#1264df]"></i>
                                    <i class="h-[4px] w-[4px] rounded-[1px] border border-[#1264df]"></i>
                                    <i class="h-[4px] w-[4px] rounded-[1px] border border-[#1264df]"></i>
                                    <i class="h-[4px] w-[4px] rounded-[1px] border border-[#1264df]"></i>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- HERO RIGHT -->
                    <div class="relative min-h-[400px] sm:min-h-[530px]">
                        <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/e4be1a09-745c-4b20-bd54-d93ecab9442a.png') }}"
                             alt="Slimme-PC netwerkoplossingen"
                             class="absolute bottom-[-15px] right-[-4%] z-10 w-[92%] max-w-[830px] object-contain drop-shadow-[0_25px_30px_rgba(10,40,90,.18)] max-lg:static max-lg:mx-auto max-lg:w-full max-lg:max-w-[560px] max-lg:bottom-auto max-lg:right-auto">

                        <!-- Access Point -->
                        <div class="absolute left-[3%] top-[10%] z-20 hidden w-[190px] rounded-[11px] border border-blue-100 bg-white/95 px-4 py-3 shadow-[0_8px_25px_rgba(10,50,120,.10)] xl:flex">
                            <div class="mr-3 text-[31px] text-[#1264df]">◉</div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-[#1264df]">Access Point</div>
                                <p class="mt-1 text-[9px] leading-[1.35] text-slate-600">Sterke WiFi dekking<br>in elke ruimte</p>
                            </div>
                        </div>
                        <!-- Router -->
                        <div class="absolute left-[-2%] top-[33%] z-20 hidden w-[195px] rounded-[11px] border border-blue-100 bg-white/95 px-4 py-3 shadow-[0_8px_25px_rgba(10,50,120,.10)] xl:flex">
                            <div class="mr-3 text-[28px] text-[#1264df]">♢</div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-[#1264df]">Router / Firewall</div>
                                <p class="mt-1 text-[9px] leading-[1.35] text-slate-600">Veilige en stabiele<br>internetverbinding</p>
                            </div>
                        </div>
                        <!-- Switch -->
                        <div class="absolute bottom-[24%] left-[-4%] z-20 hidden w-[200px] rounded-[11px] border border-blue-100 bg-white/95 px-4 py-3 shadow-[0_8px_25px_rgba(10,50,120,.10)] xl:flex">
                            <div class="mr-3 text-[27px] text-[#1264df]">▤</div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-[#1264df]">Netwerk Switch</div>
                                <p class="mt-1 text-[9px] leading-[1.35] text-slate-600">Snel en betrouwbaar<br>dataverkeer</p>
                            </div>
                        </div>
                        <!-- Cabling -->
                        <div class="absolute bottom-[26%] right-[-1%] z-20 hidden w-[180px] rounded-[11px] border border-blue-100 bg-white/95 px-4 py-3 shadow-[0_8px_25px_rgba(10,50,120,.10)] xl:flex">
                            <div class="mr-3 text-[27px] text-[#1264df]">⚯</div>
                            <div>
                                <div class="text-[10px] font-black uppercase text-[#1264df]">Bekabeling</div>
                                <p class="mt-1 text-[9px] leading-[1.35] text-slate-600">Netwerkbekabeling<br>voor maximale<br>betrouwbaarheid</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- TRUST BAR --}}
        <section class="relative z-20 -mt-1 pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[10px] bg-gradient-to-r from-[#061a43] via-[#042558] to-[#06183d] text-white shadow-[0_10px_30px_rgba(5,30,80,.13)] sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($s['trust']['items'] ?? [
                        ['emoji' => '♢', 'title' => 'Gratis advies', 'subtitle' => 'Vrijblijvend & persoonlijk'],
                        ['emoji' => '♢', 'title' => 'Vakkundige installatie', 'subtitle' => 'Netjes & professioneel'],
                        ['emoji' => '⚭', 'title' => 'Top kwaliteit apparatuur', 'subtitle' => 'Betrouwbare merken'],
                        ['emoji' => '◷', 'title' => 'Nazorg & support', 'subtitle' => 'Wij blijven voor je klaar'],
                    ] as $ti)
                        <div class="flex items-center gap-5 border-white/15 px-7 py-5 {{ !$loop->last ? 'xl:border-r' : '' }} max-xl:border-b xl:border-b-0">
                            <div class="grid h-[45px] w-[45px] shrink-0 place-items-center rounded-full border border-white/70 text-[22px]">{{ $ti['emoji'] ?? '' }}</div>
                            <div>
                                <h3 class="text-[11px] font-black">{{ $ti['title'] ?? '' }}</h3>
                                <p class="mt-1 text-[9px] text-blue-100">{{ $ti['subtitle'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- SOLUTIONS --}}
        <section id="oplossingen" class="py-4">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="mb-6 text-center">
                    <h2 class="text-[29px] font-black tracking-[-.035em]">
                        {{ $s['solutions']['title'] ?? 'Waarmee kunnen we je helpen?' }}
                    </h2>
                    <p class="mt-1 text-[10px] text-slate-500">
                        {{ $s['solutions']['subtitle'] ?? 'Kies de oplossing die bij jouw situatie past.' }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                    @foreach ($s['solutions']['items'] ?? [
                        ['emoji' => '◉', 'title' => 'WiFi oplossingen', 'description' => "Sterke dekking en stabiel\ninternet in elke ruimte."],
                        ['emoji' => '⚯', 'title' => 'Netwerkbekabeling', 'description' => "Netwerkkabels trekken,\npunten aanleggen en\nstructuur verbeteren."],
                        ['emoji' => '▤', 'title' => 'Netwerkapparatuur', 'description' => "Routers, switches,\naccess points en\nfirewalls op maat."],
                        ['emoji' => '♧', 'title' => 'Zakelijk netwerk', 'description' => "Complete netwerkinstallaties\nvoor bedrijven en kantoren."],
                        ['emoji' => '♢', 'title' => 'Netwerk beveiliging', 'description' => "Beveiliging, gastnetwerken\nen toegangsbeheer."],
                        ['emoji' => '⚙', 'title' => 'Netwerkbeheer', 'description' => "Monitoring, onderhoud\nen support voor een\nzorgeloos netwerk."],
                    ] as $sol)
                        <article class="group flex min-h-[260px] flex-col items-center rounded-[11px] border border-slate-200 bg-white px-5 py-6 text-center shadow-[0_6px_25px_rgba(5,40,90,.06)] transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl">
                            <div class="text-[50px] text-[#1264df] leading-none">{{ $sol['emoji'] ?? '' }}</div>
                            <h3 class="mt-4 text-[14px] font-black">{{ $sol['title'] ?? '' }}</h3>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600 whitespace-pre-line">{{ $sol['description'] ?? '' }}</p>
                            <a href="/reparatie-aanmelden" class="mt-auto pt-5 text-[10px] font-bold text-[#1264df]">Meer info &nbsp; →</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- RECOGNIZE PROBLEMS --}}
        <section class="pb-6 pt-2">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <h2 class="mb-5 text-center text-[25px] font-black">
                    {{ $s['recognize']['title'] ?? 'Herken je dit?' }}
                </h2>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                    @foreach ($s['recognize']['items'] ?? [
                        ['emoji' => '◉', 'title' => 'WiFi valt weg'],
                        ['emoji' => '◌', 'title' => 'Slecht bereik'],
                        ['emoji' => '↗', 'title' => 'Trage verbinding'],
                        ['emoji' => '◎', 'title' => 'Geen internet'],
                        ['emoji' => '♧', 'title' => 'Netwerk uitbreiden'],
                        ['emoji' => '♙', 'title' => "Nieuw kantoor\naansluiten"],
                    ] as $ri)
                        <div class="flex h-[72px] items-center gap-4 rounded-[9px] border border-blue-100 bg-white px-5 shadow-sm">
                            <span class="text-[26px] text-[#1264df]">{{ $ri['emoji'] ?? '' }}</span>
                            <span class="text-[11px] font-bold whitespace-pre-line leading-tight">{{ $ri['title'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- HOME + BUSINESS --}}
        <section class="pb-6">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="mb-5 text-center">
                    <h2 class="text-[28px] font-black">
                        Voor thuis én <span class="text-[#1264df]">zakelijk</span>
                    </h2>
                    <p class="mt-1 text-[10px] text-slate-500">Oplossingen die passen bij jouw situatie.</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <!-- HOME -->
                    <article class="relative min-h-[330px] overflow-hidden rounded-[13px] border border-blue-100 bg-[#edf4ff] shadow-[0_8px_30px_rgba(5,40,90,.08)]">
                        <img src="{{ asset($s['home_business']['home_image'] ?? 'assets/img/landing/2b775919-c76612bc-step-2_-install-structured-cabling-(ethernet-backh.jpg') }}" alt="Thuisnetwerk" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-transparent"></div>
                        <div class="relative z-10 max-w-[330px] px-7 py-6">
                            <h3 class="text-[20px] font-black">{{ $s['home_business']['home_title'] ?? 'Thuisnetwerk' }}</h3>
                            <ul class="mt-4 space-y-3 text-[11px] font-semibold">
                                @foreach ($s['home_business']['home_items'] ?? [['title' => 'WiFi in elke kamer'], ['title' => 'Sneller internet'], ['title' => 'Slimme apparaten verbinden'], ['title' => 'Ouderlijk toezicht'], ['title' => 'Gastnetwerk']] as $hi)
                                    <li class="flex items-center gap-2"><span class="text-[#1264df]">◉</span> {{ $hi['title'] ?? '' }}</li>
                                @endforeach
                            </ul>
                            <a href="/reparatie-aanmelden" class="mt-6 inline-flex items-center gap-8 rounded-[5px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                                Thuisnetwerk optimaliseren
                                <span>→</span>
                            </a>
                        </div>
                    </article>

                    <!-- BUSINESS -->
                    <article class="relative min-h-[330px] overflow-hidden rounded-[13px] border border-blue-100 bg-[#edf4ff] shadow-[0_8px_30px_rgba(5,40,90,.08)]">
                        <img src="{{ asset($s['home_business']['business_image'] ?? 'assets/img/landing/LAN-Corning1.jpg') }}" alt="Zakelijk netwerk" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-transparent"></div>
                        <div class="relative z-10 max-w-[365px] px-7 py-6">
                            <h3 class="text-[20px] font-black">{{ $s['home_business']['business_title'] ?? 'Zakelijk netwerk' }}</h3>
                            <ul class="mt-4 space-y-2 text-[10px] font-semibold">
                                @foreach ($s['home_business']['business_items'] ?? [['title' => 'Stabiele verbindingen'], ['title' => 'Veilig en betrouwbaar'], ['title' => 'Schaalbaar en toekomstproof'], ['title' => "Gast- & medewerkersnetwerk"], ['title' => "WiFi voor gasten gescheiden\nvan bedrijfsnetwerk"], ['title' => "Werkplekken, printers,\nNAS & apparatuur verbinden"], ['title' => 'Centrale beheeropties']] as $bi)
                                    <li class="flex items-start gap-2"><span class="text-[#1264df]">◉</span> <span class="whitespace-pre-line">{{ $bi['title'] ?? '' }}</span></li>
                                @endforeach
                            </ul>
                            <a href="/reparatie-aanmelden" class="mt-5 inline-flex items-center gap-8 rounded-[5px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#044dbd]">
                                Zakelijk netwerk oplossingen
                                <span>→</span>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- PROCESS --}}
        <section class="pb-6 pt-1">
            <div class="mx-auto max-w-[1400px] px-5">
                <h2 class="mb-7 text-center text-[27px] font-black">
                    {{ $s['steps']['title'] ?? 'Onze werkwijze' }}
                </h2>
                <div class="grid gap-7 md:grid-cols-5">
                    @foreach ($s['steps']['steps'] ?? [
                        ['emoji' => '☵', 'title' => '1. Advies', 'description' => "We bespreken jouw wensen\nen bekijken de situatie."],
                        ['emoji' => '▧', 'title' => '2. Plan op maat', 'description' => "We maken een voorstel\ndat past bij jouw behoeften."],
                        ['emoji' => '⚒', 'title' => '3. Installatie & configuratie', 'description' => "Vakkundige installatie en\nconfiguratie van alle apparatuur."],
                        ['emoji' => '✓', 'title' => '4. Test & controle', 'description' => "We testen alles grondig\nvoor optimale prestaties."],
                        ['emoji' => '♫', 'title' => '5. Support & beheer', 'description' => "We blijven beschikbaar voor\nonderhoud en support."],
                    ] as $step)
                        <div class="relative text-center">
                            <div class="mx-auto grid h-[78px] w-[78px] place-items-center rounded-full border border-blue-100 bg-white text-[35px] text-[#1264df] shadow-[0_7px_25px_rgba(10,50,110,.08)]">{{ $step['emoji'] ?? '' }}</div>
                            @if (!$loop->last)
                                <div class="absolute left-[72%] top-[38px] hidden w-[70%] border-t-2 border-dashed border-[#1264df] md:block"></div>
                            @endif
                            <h3 class="mt-5 text-[12px] font-black">{{ $step['title'] ?? '' }}</h3>
                            <p class="mx-auto mt-2 max-w-[170px] text-[9px] leading-4 text-slate-600 whitespace-pre-line">{{ $step['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- BRANDS --}}
        <section class="pb-6">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="rounded-[11px] border border-slate-200 bg-white px-6 py-5 shadow-[0_5px_22px_rgba(5,40,90,.05)]">
                    <h2 class="mb-5 text-center text-[17px] font-black">
                        {{ $s['brands']['title'] ?? 'Merken waarop we vertrouwen' }}
                    </h2>
                    <div class="grid items-center gap-7 sm:grid-cols-2 md:grid-cols-5">
                        @foreach ($s['brands']['items'] ?? [
                            ['image' => 'assets/img/landing/brand-ubiquiti.png', 'title' => 'Ubiquiti'],
                            ['image' => 'assets/img/landing/brand-tplink.png', 'title' => 'TP-Link'],
                            ['image' => 'assets/img/landing/brand-mikrotik.png', 'title' => 'MikroTik'],
                            ['image' => 'assets/img/landing/brand-synology.png', 'title' => 'Synology'],
                            ['image' => 'assets/img/landing/brand-netgear.png', 'title' => 'Netgear'],
                        ] as $br)
                            <img src="{{ asset('assets/img/landing/' . basename($br['image'] ?? '')) }}" alt="{{ $br['title'] ?? '' }}" class="mx-auto h-[38px] max-w-[150px] object-contain">
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- FINAL CTA --}}
        <section id="contact" class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[12px] bg-gradient-to-r from-[#061c4c] via-[#05255c] to-[#071c43] text-white shadow-[0_12px_35px_rgba(5,30,80,.15)] lg:grid-cols-[1.3fr_.9fr_1.2fr]">

                    <div class="flex items-center gap-6 px-8 py-7">
                        <div class="grid h-[90px] w-[90px] shrink-0 place-items-center rounded-full bg-[#0765e8] text-[46px]">◉</div>
                        <div>
                            <h2 class="text-[24px] font-black">
                                {{ $s['final']['title'] ?? 'Klaar voor een sterker netwerk?' }}
                            </h2>
                            <p class="mt-2 max-w-[390px] text-[11px] leading-5 text-blue-100">
                                {{ $s['final']['subtitle'] ?? 'Vraag vrijblijvend advies aan en ontdek wat wij voor jou kunnen betekenen.' }}
                            </p>
                            <div class="mt-5 flex flex-wrap gap-3">
                                <a href="/reparatie-aanmelden" class="inline-flex items-center gap-6 rounded-[5px] bg-[#0765e8] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#0550c9]">
                                    Gratis advies aanvragen
                                    <span>→</span>
                                </a>
                                <a href="tel:0552032145" class="inline-flex items-center gap-3 rounded-[5px] border border-white/40 px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-white/10">
                                    ☎ Bel direct: 055 203 21 45
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center border-white/15 px-7 py-6 lg:border-l">
                        @foreach ($s['final']['benefits'] ?? [['title' => 'Gratis & vrijblijvend advies', 'subtitle' => 'Bij jou thuis of op locatie'], ['title' => 'Snelle service', 'subtitle' => 'Binnen 24–48 uur'], ['title' => 'Altijd bereikbaar', 'subtitle' => 'Ook voor spoedklussen']] as $fb)
                            <div class="flex gap-3 {{ !$loop->first ? 'mt-5' : '' }}">
                                <span class="text-[22px]">{{ $loop->index == 0 ? '♢' : ($loop->index == 1 ? '◷' : '◔') }}</span>
                                <div>
                                    <h3 class="text-[10px] font-black">{{ $fb['title'] ?? '' }}</h3>
                                    <p class="text-[9px] text-blue-100">{{ $fb['subtitle'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="relative min-h-[270px]">
                        <img src="{{ asset($s['final']['image'] ?? 'assets/img/landing/images.jpeg') }}" alt="Slimme-PC netwerk specialist" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-y-0 left-0 w-[100px] bg-gradient-to-r from-[#071e49] to-transparent"></div>
                    </div>

                </div>
            </div>
        </section>

        {{-- BOTTOM BENEFITS --}}
        <section class="pb-10">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[10px] border border-slate-200 bg-white md:grid-cols-3">
                    <div class="flex items-center gap-5 px-7 py-5">
                        <div class="text-[32px] text-[#1264df]">◎</div>
                        <div>
                            <h3 class="text-[10px] font-black text-[#1264df]">Lokaal in Apeldoorn</h3>
                            <p class="mt-1 text-[9px] leading-4 text-slate-600">Snelle service bij jou in de buurt voor thuis &amp; bedrijven.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 border-y border-slate-200 px-7 py-5 md:border-x md:border-y-0">
                        <div class="grid h-[41px] w-[41px] place-items-center rounded-full bg-[#1264df] text-white">✓</div>
                        <div>
                            <h3 class="text-[10px] font-black">Tevreden klanten</h3>
                            <p class="mt-1 text-[9px] leading-4 text-slate-600">Wij gaan voor 100% tevredenheid en kwaliteit.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 px-7 py-5">
                        <div class="grid h-[41px] w-[41px] place-items-center rounded-full bg-white text-[#1264df] ring-1 ring-slate-200">♧</div>
                        <div>
                            <h3 class="text-[10px] font-black">Persoonlijk advies</h3>
                            <p class="mt-1 text-[9px] leading-4 text-slate-600">We denken met je mee voor de beste oplossing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')
@endsection

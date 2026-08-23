@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="overflow-hidden bg-[#fbfdff] text-[#07153d]">

        {{-- HERO --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-white via-[#f8fbff] to-[#eef5ff]">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute right-[8%] top-[8%] h-[500px] w-[500px] rounded-full bg-blue-100/40 blur-[100px]"></div>
                <div class="absolute left-[35%] top-[30%] h-[330px] w-[330px] rounded-full bg-white blur-[80px]"></div>
            </div>

            <div class="relative mx-auto max-w-[1500px] px-5 lg:px-8 pt-5 pb-10 lg:pt-6 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-slate-700 transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-slate-700 transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-[#07153d] font-semibold">{{ config('cms.pages.software.label') ?? 'Software & Windows' }}</span>
                </div>

                <div class="grid min-h-[600px] items-center gap-8 lg:grid-cols-[0.75fr_1.45fr]">

                    <!-- HERO LEFT -->
                    <div class="relative z-20">
                        <p class="mb-5 text-[13px] font-black uppercase tracking-[.02em] text-[#0762e9]">
                            {{ $s['hero']['badge'] ?? 'IT Hulp & Software · Apeldoorn' }}
                        </p>

                        <h1 class="max-w-[560px] text-[30px] sm:text-[52px] lg:text-[40px] font-black leading-[.98] tracking-[-.045em] text-[#09143a]">
                            {{ $s['hero']['title1'] ?? 'Software-probleem?' }}
                            <span class="block">
                                {{ $s['hero']['title2'] ?? 'Wij' }}
                                <span class="text-[#1264df]">{{ $s['hero']['title3'] ?? 'lossen' }}</span>
                                {{ $s['hero']['title4'] ?? 'het op.' }}
                            </span>
                        </h1>

                        <p class="mt-6 max-w-[470px] text-[16px] sm:text-[17px] leading-[1.65] text-slate-700 whitespace-pre-line">{{ $s['hero']['description'] ?? "Van Windows en software tot printers,\ninternet, e-mail en netwerk.\nEén adres voor al jouw IT-hulp." }}</p>

                        <div class="mt-7 space-y-3">
                            @foreach ($s['hero']['bullets'] ?? [['title' => 'Voor particulier & zakelijk'], ['title' => 'Ervaren & snel geholpen'], ['title' => 'Eerlijk advies, vaste tarieven'], ['title' => 'Remote of bij ons in de winkel']] as $b)
                                <div class="flex items-center gap-3 text-[14px] font-semibold">
                                    <span class="grid h-[20px] w-[20px] place-items-center rounded-full bg-[#1264df] text-[11px] text-white">✓</span>
                                    {{ $b['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 max-w-[360px] space-y-3">
                            <a href="/reparatie-aanmelden"
                               class="group flex items-center justify-between rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white shadow-[0_12px_30px_rgba(7,94,229,.18)] transition hover:-translate-y-[2px] hover:bg-[#034ebf]">
                                <span>Hulp nodig? Neem contact op</span>
                                <span class="text-xl transition group-hover:translate-x-1">→</span>
                            </a>
                            <a href="#diensten"
                               class="flex items-center justify-between rounded-[6px] border border-[#1264df] bg-white px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#07153d] transition hover:bg-[#f3f7ff]">
                                <span>Bekijk alle diensten</span>
                                <span class="grid grid-cols-2 gap-[2px]">
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
                        <div class="absolute left-[50%] top-[52%] h-[420px] w-[690px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/80 blur-[55px]"></div>
                        <img src="{{ asset($s['hero']['image'] ?? 'assets/img/landing/software-hero.png') }}"
                             alt="IT hulp en software"
                             class="absolute bottom-[8px] left-[40%] z-10 w-[75%] max-w-[800px] -translate-x-1/2 object-contain drop-shadow-[0_25px_25px_rgba(20,50,100,.14)] max-lg:static max-lg:mx-auto max-lg:w-full max-lg:max-w-[560px] max-lg:translate-x-0 max-lg:left-auto max-lg:bottom-auto">

                        <!-- FLOATING ICONS - hidden on mobile/tablet -->
                        <div class="absolute left-[43%] top-[0%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[86px] w-[86px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-11 w-11 stroke-[#1264df]" stroke-width="2.5"><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.55a10 10 0 0 1 14 0"/><path d="M8.5 16.1a5 5 0 0 1 7 0"/><circle cx="12" cy="20" r="1" fill="#1264df"/></svg>
                            </div>
                            <div class="mt-3 text-[12px] font-black uppercase">Internet &amp; WiFi</div>
                        </div>
                        <div class="absolute left-[14%] top-[14%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[78px] w-[78px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" class="h-9 w-9 fill-[#1264df]"><path d="M7 3h10v4H7V3Zm11 5H6a3 3 0 0 0-3 3v6h4v4h10v-4h4v-6a3 3 0 0 0-3-3ZM9 19v-5h6v5H9Z"/></svg>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase">Printer</div>
                        </div>
                        <div class="absolute right-[19%] top-[13%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[78px] w-[78px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-9 w-9 stroke-[#1264df]" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase">E-mail</div>
                        </div>
                        <div class="absolute bottom-[37px] left-[7%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[76px] w-[76px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <div class="grid h-9 w-9 grid-cols-2 gap-[3px]"><span class="bg-[#1264df]"></span><span class="bg-[#1264df]"></span><span class="bg-[#1264df]"></span><span class="bg-[#1264df]"></span></div>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase leading-tight">Windows &amp;<br>Software</div>
                        </div>
                        <div class="absolute bottom-[1%] left-[43%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[76px] w-[76px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" class="h-10 w-10 fill-[#1264df]"><path d="M19.35 10.04A7.49 7.49 0 0 0 5.1 8.2 6 6 0 0 0 6 20h13a5 5 0 0 0 .35-9.96Z"/></svg>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase">Accounts &amp; Cloud</div>
                        </div>
                        <div class="absolute bottom-[18%] right-[18%] z-20 hidden text-center lg:block">
                            <div class="mx-auto grid h-[76px] w-[76px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-9 w-9 stroke-[#1264df]" stroke-width="2"><rect x="10" y="2" width="4" height="4"/><rect x="2" y="18" width="4" height="4"/><rect x="10" y="18" width="4" height="4"/><rect x="18" y="18" width="4" height="4"/><path d="M12 6v6m-8 6v-4h16v4M12 12v6"/></svg>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase">Netwerk</div>
                        </div>
                        <div class="absolute bottom-[4%] right-[0%] z-20 hidden text-center xl:block">
                            <div class="mx-auto grid h-[76px] w-[76px] place-items-center rounded-full border border-blue-100 bg-white shadow-[0_7px_22px_rgba(0,51,120,.12)]">
                                <svg viewBox="0 0 24 24" fill="#1264df" class="h-10 w-10"><path d="M12 2 4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3Zm-1 15-4-4 1.4-1.4 2.6 2.59 4.6-4.59L17 11l-6 6Z"/></svg>
                            </div>
                            <div class="mt-2 text-[11px] font-black uppercase">Beveiliging</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- INTERACTIVE PROBLEM SELECTOR --}}
        <section id="diensten" class="pb-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="overflow-hidden rounded-[14px] border border-[#dbe7f5] bg-white shadow-[0_10px_30px_rgba(10,50,100,.07)]">

                    <div class="px-5 pt-5 text-center">
                        <h2 class="text-[29px] font-black tracking-[-.035em]">
                            {{ $s['selector']['title'] ?? 'Waar kunnen we je mee helpen?' }}
                        </h2>
                        <p class="mt-1 text-[11px] text-slate-500">
                            {{ $s['selector']['subtitle'] ?? 'Klik op een categorie of kies het probleem dat je ervaart.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 px-4 pb-5 pt-4 sm:grid-cols-3 md:grid-cols-5 xl:grid-cols-9">
                        @php $svcKeys = ['windows','printer','wifi','network','email','cloud','security','devices','other']; @endphp
                        @foreach ($s['selector']['tabs'] ?? [
                            ['emoji' => '▦', 'title' => "Windows &\nSoftware"],
                            ['emoji' => '🖨', 'title' => 'Printer'],
                            ['emoji' => '◉', 'title' => "Internet &\nWiFi"],
                            ['emoji' => '⛓', 'title' => 'Netwerk'],
                            ['emoji' => '✉', 'title' => 'E-mail'],
                            ['emoji' => '☁', 'title' => "Accounts &\nCloud"],
                            ['emoji' => '♢', 'title' => 'Beveiliging'],
                            ['emoji' => '⌨', 'title' => 'Randapparatuur'],
                            ['emoji' => '•••', 'title' => "Ander IT-\nprobleem?"],
                        ] as $idx => $tab)
                            <button data-service="{{ $svcKeys[$idx] ?? 'other' }}" class="service-tab {{ $idx === 0 ? 'active-tab border-[#1264df] bg-[#f9fbff] shadow-[0_5px_18px_rgba(0,70,180,.08)]' : 'border-slate-200 bg-white shadow-sm' }} group min-h-[115px] rounded-[9px] border px-3 py-4 transition hover:-translate-y-1 hover:border-[#1264df]">
                                <div class="text-[30px] leading-none text-[#07153d] group-[.active-tab]:text-[#1264df]">{{ $tab['emoji'] ?? '' }}</div>
                                <div class="mt-3 text-[11px] font-black leading-tight whitespace-pre-line {{ $idx === 0 ? 'text-[#075ee5]' : '' }}">{{ $tab['title'] ?? '' }}</div>
                            </button>
                        @endforeach
                    </div>

                    <div class="relative grid border-t border-slate-100 lg:grid-cols-[1.05fr_1.45fr_.65fr]">
                        <div class="relative m-5 min-h-[245px] overflow-hidden rounded-[10px]">
                            <img id="serviceImage" src="{{ asset($s['selector']['selected_image'] ?? 'assets/img/landing/windows-service.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover transition duration-300">
                            <div id="serviceImageText" class="absolute bottom-6 left-5 rounded-[8px] bg-white/95 px-5 py-4 text-[10px] font-semibold leading-5 shadow-[0_7px_25px_rgba(0,0,0,.12)] whitespace-pre-line">{{ $s['selector']['selected_image_text'] ?? "Installatie • Updates • Drivers\nFouten • Trage PC • Software" }}</div>
                        </div>

                        <div class="flex items-center px-6 py-8">
                            <div class="w-full">
                                <h3 id="serviceTitle" class="text-[23px] font-black tracking-[-.02em]">
                                    {{ $s['selector']['selected_title'] ?? 'Windows & Software problemen?' }}
                                </h3>
                                <div id="serviceProblems" class="mt-6 grid gap-x-10 gap-y-4 text-[11px] font-medium text-slate-700 sm:grid-cols-2">
                                    @foreach ($s['selector']['selected_problems'] ?? [['title' => 'Windows start niet of vastlopers'], ['title' => 'Drivers installeren of bijwerken'], ['title' => 'Blauw scherm of foutmeldingen'], ['title' => 'Programma\'s installeren / verwijderen'], ['title' => 'Trage computer of lange opstarttijd'], ['title' => 'Software werkt niet goed'], ['title' => 'Windows updates problemen'], ['title' => 'Bestanden kwijt of beschadigd']] as $sp)
                                        <div class="flex items-center gap-3">
                                            <span class="text-[#1264df]">◉</span>
                                            {{ $sp['title'] ?? '' }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="m-5 flex min-h-[230px] flex-col items-center justify-center rounded-[11px] bg-gradient-to-b from-[#f2f7ff] to-[#edf4ff] px-5 text-center">
                            <div class="text-[52px] text-[#075ee5]">⚒</div>
                            <div class="mt-4 text-[17px] font-black leading-tight">
                                We helpen je snel<br>
                                weer op weg.
                            </div>
                            <a href="/reparatie-aanmelden"
                               class="mt-5 inline-flex items-center justify-between gap-6 rounded-[6px] bg-[#075ee5] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-white transition hover:bg-[#034ebf]">
                                Hulp aanvragen
                                <span class="text-lg">→</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- SERVICES GRID --}}
        <section class="py-3">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <h2 class="mb-5 text-center text-[27px] font-black tracking-[-.03em]">
                    {{ $s['services']['title'] ?? 'Dit kunnen we voor je doen' }}
                </h2>

                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                    @foreach ($s['services']['items'] ?? [
                        ['image' => 'assets/img/landing/windows-service-card.png', 'title' => 'Windows & Software', 'points' => 'Installatie Windows 10 / 11,Updates & optimalisatie,Drivers & programma\'s,Trage PC oplossen'],
                        ['image' => 'assets/img/landing/printer-service.png', 'title' => 'Printerproblemen', 'points' => 'Installatie & configuratie,Printer niet gevonden,Printen / scannen werkt niet,Drivers & WiFi problemen'],
                        ['image' => 'assets/img/landing/router-service.png', 'title' => 'Internet & WiFi', 'points' => 'Geen internet verbinding,Trage of instabiele WiFi,WiFi bereik vergroten,Router / modem hulp'],
                        ['image' => 'assets/img/landing/network-service.png', 'title' => 'Netwerk', 'points' => 'Thuisnetwerk instellen,Apparaten verbinden,Netwerkproblemen oplossen,Bekabeld of draadloos'],
                        ['image' => 'assets/img/landing/email-service.png', 'title' => 'E-mail', 'points' => 'E-mail instellen & herstellen,Verzenden/ontvangen werkt niet,Outlook, Gmail, etc.,Synchronisatie problemen'],
                        ['image' => 'assets/img/landing/security-service.png', 'title' => 'Beveiliging', 'points' => 'Malware & virussen verwijderen,Beveiligingsinstellingen,Ongewenste software,Privacy & veiligheid'],
                    ] as $card)
                        <article class="group rounded-[10px] border border-slate-200 bg-white p-4 shadow-[0_5px_20px_rgba(7,40,90,.06)] transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="h-[120px] overflow-hidden rounded-[7px] bg-[#edf4ff]">
                                <img src="{{ asset('assets/img/landing/' . basename($card['image'] ?? '')) }}" class="h-full w-full object-contain" alt="{{ $card['title'] ?? '' }}">
                            </div>
                            <h3 class="mt-4 text-[13px] font-black">{{ $card['title'] ?? '' }}</h3>
                            <ul class="mt-3 space-y-2 text-[9px] leading-4 text-slate-600">
                                @foreach (explode(',', $card['points'] ?? '') as $pt)
                                    @if (trim($pt) !== '')
                                        <li><span class="text-[#1264df]">✓</span> {{ trim($pt) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- PROCESS --}}
        <section class="py-6">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <h2 class="mb-6 text-center text-[27px] font-black">
                    {{ $s['steps']['title'] ?? 'Zo gaan we te werk' }}
                </h2>

                <div class="grid items-stretch gap-3 md:grid-cols-5">
                    @foreach ($s['steps']['steps'] ?? [
                        ['emoji' => '☵', 'title' => '1. Probleem bespreken', 'description' => 'Je vertelt ons wat er speelt. We stellen de juiste vragen.'],
                        ['emoji' => '⌕', 'title' => '2. Diagnose', 'description' => 'We onderzoeken het probleem grondig.'],
                        ['emoji' => '▧', 'title' => '3. Oplossing voorstellen', 'description' => 'Je ontvangt een duidelijke uitleg en een eerlijk advies.'],
                        ['emoji' => '⚙', 'title' => '4. Reparatie & testen', 'description' => 'We lossen het probleem op en testen alles goed.'],
                        ['emoji' => '✓', 'title' => '5. Alles werkt weer', 'description' => 'Je apparaat werkt weer zoals het hoort!'],
                    ] as $step)
                        <div class="relative rounded-[11px] border border-slate-200 bg-white px-6 py-6 shadow-[0_5px_20px_rgba(10,40,90,.05)]">
                            <div class="text-center text-[39px] text-[#1264df]">{{ $step['emoji'] ?? '' }}</div>
                            <h3 class="mt-5 text-[12px] font-black">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-3 text-[10px] leading-5 text-slate-600 whitespace-pre-line">{{ $step['description'] ?? '' }}</p>
                            @if (!$loop->last)
                                <span class="absolute -right-5 top-1/2 z-10 hidden -translate-y-1/2 text-2xl md:block">→</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- BLUE TRUST BAR --}}
        <section class="py-2">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid overflow-hidden rounded-[10px] bg-gradient-to-r from-[#0755da] via-[#0064e7] to-[#0755da] text-white shadow-[0_10px_25px_rgba(0,75,200,.15)] md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($s['trust']['items'] ?? [
                        ['emoji' => '◷', 'title' => 'Snelle service', 'subtitle' => "Vaak klaar terwijl je wacht\nof binnen 24 uur"],
                        ['emoji' => '€', 'title' => 'Eerlijke prijzen', 'subtitle' => "Geen verrassingen\nHeldere tarieven vooraf"],
                        ['emoji' => '♢', 'title' => 'Gegarandeerd', 'subtitle' => "Op reparaties &\nsoftware oplossingen"],
                        ['emoji' => '♟', 'title' => 'Persoonlijke hulp', 'subtitle' => "We nemen de tijd\nvoor jouw probleem"],
                    ] as $ti)
                        <div class="flex items-center gap-5 border-white/15 px-7 py-5 {{ !$loop->last ? 'xl:border-r' : '' }} max-xl:border-b xl:border-b-0">
                            <div class="grid h-[53px] w-[53px] shrink-0 place-items-center rounded-full border-2 border-white text-[25px] {{ $ti['title'] == 'Eerlijke prijzen' ? 'bg-white text-[#1264df]' : '' }}">{{ $ti['emoji'] ?? '' }}</div>
                            <div>
                                <div class="text-[12px] font-black">{{ $ti['title'] ?? '' }}</div>
                                <p class="mt-1 text-[9px] leading-4 text-blue-100 whitespace-pre-line">{{ $ti['subtitle'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- FINAL CTA AREA --}}
        <section id="contact" class="pb-12 pt-5">
            <div class="mx-auto max-w-[1500px] px-5 lg:px-8">
                <div class="grid gap-4 lg:grid-cols-[.9fr_1.4fr_.7fr]">

                    <div class="rounded-[11px] border border-slate-200 bg-white p-6 shadow-[0_6px_25px_rgba(0,40,100,.05)]">
                        <div class="flex gap-5">
                            <div class="text-[40px] text-[#1264df]">▣</div>
                            <div>
                                <h3 class="text-[15px] font-black">
                                    {{ $s['final']['remote_title'] ?? 'Probleem zonder bezoek oplossen?' }}
                                </h3>
                                <p class="mt-3 text-[10px] leading-5 text-slate-600 whitespace-pre-line">{{ $s['final']['remote_text'] ?? "Veel software-, e-mail- en Windowsproblemen\nkunnen we veilig op afstand oplossen." }}</p>
                                <a href="/reparatie-aanmelden"
                                   class="mt-5 inline-flex items-center gap-7 rounded-md border border-[#1264df] px-5 py-2.5 sm:px-7 sm:py-4 text-[14px] sm:text-[15px] font-bold text-[#1264df] transition hover:bg-[#f3f7ff]">
                                    Remote hulp aanvragen
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-[11px] border border-slate-200 bg-white shadow-[0_6px_25px_rgba(0,40,100,.05)]">
                        <div class="relative z-10 w-full px-7 py-6 md:w-[58%]">
                            <h3 class="text-[19px] font-black">
                                {{ $s['final']['contact_title'] ?? 'Nog vragen? Wij helpen je graag!' }}
                            </h3>
                            <p class="mt-2 text-[10px] text-slate-600">
                                {{ $s['final']['contact_subtitle'] ?? 'Bel, WhatsApp of kom langs in onze winkel in Apeldoorn.' }}
                            </p>
                            <div class="mt-5 space-y-3 text-[10px] font-semibold">
                                <div class="flex items-center gap-3">
                                    <span class="text-[17px] text-[#1264df]">☎</span>
                                    {{ $s['final']['contact_phone'] ?? '055 203 21 45' }}
                                    <span class="ml-5 text-[#15ae56]">●</span>
                                    WhatsApp ons
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="text-[17px] text-[#1264df]">●</span>
                                    <span class="whitespace-pre-line">{{ $s['final']['contact_address'] ?? "Laan van de Mensenrechten 400\n7331 VZ Apeldoorn" }}</span>
                                </div>
                            </div>
                        </div>
                        <img src="{{ asset($s['final']['contact_image'] ?? 'assets/img/landing/slimme-pc-shop.jpg') }}" alt="Slimme-PC winkel" class="absolute bottom-0 right-0 hidden h-full w-[45%] object-cover md:block">
                        <div class="absolute inset-y-0 left-[52%] hidden w-[100px] bg-gradient-to-r from-white to-transparent md:block"></div>
                    </div>

                    <div class="rounded-[11px] border border-slate-200 bg-gradient-to-br from-white to-[#f3f7ff] p-6 shadow-[0_6px_25px_rgba(0,40,100,.05)]">
                        <div class="text-[30px] font-black text-[#1264df]">“</div>
                        <p class="mt-1 text-[11px] leading-5 text-[#07153d] whitespace-pre-line">{{ $s['final']['review_text'] ?? "Mijn printer deed niets meer en internet viel steeds weg.\nBinnen 30 minuten alles opgelost!" }}</p>
                        <div class="mt-4 text-[16px] text-[#ffc400]">★★★★★</div>
                        <div class="mt-2 text-[10px] font-medium text-slate-600">{{ $s['final']['review_author'] ?? '– Klant uit Apeldoorn' }}</div>
                    </div>

                </div>
            </div>
        </section>

    </main>

    @include('landing.partials.footer')

    <script>
    const softwareServices = {
        windows: {
            title: "Windows & Software problemen?",
            image: "{{ asset('assets/img/landing/windows-service.jpg') }}",
            imageText: "Installatie • Updates • Drivers<br>Fouten • Trage PC • Software",
            problems: ["Windows start niet of vastlopers","Drivers installeren of bijwerken","Blauw scherm of foutmeldingen","Programma's installeren / verwijderen","Trage computer of lange opstarttijd","Software werkt niet goed","Windows updates problemen","Bestanden kwijt of beschadigd"]
        },
        printer: {
            title: "Printerproblemen?",
            image: "{{ asset('assets/img/landing/printer-service.png') }}",
            imageText: "Installatie • WiFi • Scannen<br>Drivers • Configuratie • Storingen",
            problems: ["Printer wordt niet gevonden","Printer installeren en configureren","Printopdrachten blijven hangen","Printer verbinden met WiFi","Scanner werkt niet","Drivers installeren of herstellen","Printer offline melding","Verbindingsproblemen oplossen"]
        },
        wifi: {
            title: "Internet & WiFi problemen?",
            image: "{{ asset('assets/img/landing/router-service.png') }}",
            imageText: "WiFi • Router • Modem<br>Bereik • Snelheid • Verbinding",
            problems: ["Geen internetverbinding","Trage internetverbinding","WiFi valt steeds weg","Slecht WiFi bereik","Router of modem instellen","Apparaten verbinden met WiFi","WiFi netwerk beveiligen","Internet storing onderzoeken"]
        },
        network: {
            title: "Netwerkproblemen?",
            image: "{{ asset('assets/img/landing/network-service.png') }}",
            imageText: "Netwerk • Bekabeling • Apparaten<br>Router • Delen • Verbinden",
            problems: ["Thuisnetwerk installeren","Computers met elkaar verbinden","Netwerkschijven instellen","Bekabeld netwerk installeren","Netwerkapparaten configureren","NAS of gedeelde opslag instellen","Netwerkproblemen onderzoeken","Draadloze verbinding optimaliseren"]
        },
        email: {
            title: "Problemen met e-mail?",
            image: "{{ asset('assets/img/landing/email-service.png') }}",
            imageText: "Outlook • Gmail • Accounts<br>Synchronisatie • Verzenden • Ontvangen",
            problems: ["E-mailaccount instellen","E-mail werkt niet meer","Kan geen berichten verzenden","Kan geen berichten ontvangen","Outlook problemen","Wachtwoord of account herstellen","Synchronisatie problemen","E-mail overzetten naar nieuw apparaat"]
        },
        cloud: {
            title: "Accounts & Cloud problemen?",
            image: "{{ asset('assets/img/landing/cloud-service.png') }}",
            imageText: "Microsoft • Google • OneDrive<br>Accounts • Cloud • Synchronisatie",
            problems: ["Microsoft account problemen","Google account instellen","OneDrive werkt niet","Cloud synchronisatie herstellen","Bestanden synchroniseren","Account herstellen","Cloud opslag instellen","Bestanden overzetten"]
        },
        security: {
            title: "Computerbeveiliging nodig?",
            image: "{{ asset('assets/img/landing/security-service.png') }}",
            imageText: "Malware • Virussen • Privacy<br>Beveiliging • Controle • Opschonen",
            problems: ["Virussen verwijderen","Malware verwijderen","Ongewenste programma's verwijderen","Computer beveiligen","Browser beveiliging","Privacy instellingen controleren","Beveiligingssoftware installeren","Verdachte meldingen onderzoeken"]
        },
        devices: {
            title: "Problemen met randapparatuur?",
            image: "{{ asset('assets/img/landing/devices-service.png') }}",
            imageText: "Toetsenbord • Muis • Webcam<br>Monitor • USB • Bluetooth",
            problems: ["Toetsenbord werkt niet","Muis werkt niet","Webcam instellen","Monitor aansluiten","USB apparaten werken niet","Bluetooth problemen","Externe schijf aansluiten","Randapparatuur installeren"]
        },
        other: {
            title: "Staat jouw probleem er niet tussen?",
            image: "{{ asset('assets/img/landing/other-it-service.png') }}",
            imageText: "Vertel ons wat er speelt.<br>Wij zoeken samen naar een oplossing.",
            problems: ["Onbekende foutmelding","Computer werkt niet goed","Probleem na een update","Apparaat werkt niet zoals verwacht","Software of hardware conflict","Hulp bij instellingen","Technisch advies nodig","Ander IT-probleem"]
        }
    };
    document.querySelectorAll('.service-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const svc = softwareServices[tab.dataset.service];
            if (!svc) return;
            document.querySelectorAll('.service-tab').forEach(b => {
                b.classList.remove('border-[#1264df]','bg-[#f9fbff]','shadow-[0_5px_18px_rgba(0,70,180,.08)]');
                b.classList.add('border-slate-200','bg-white');
            });
            tab.classList.remove('border-slate-200','bg-white');
            tab.classList.add('border-[#1264df]','bg-[#f9fbff]','shadow-[0_5px_18px_rgba(0,70,180,.08)]');
            const img = document.getElementById('serviceImage');
            const title = document.getElementById('serviceTitle');
            const txt = document.getElementById('serviceImageText');
            const probs = document.getElementById('serviceProblems');
            [img,title,txt,probs].forEach(el => { if(el) el.style.opacity='0'; });
            setTimeout(() => {
                if(title) title.textContent = svc.title;
                if(img) img.src = svc.image;
                if(txt) txt.innerHTML = svc.imageText;
                if(probs) probs.innerHTML = svc.problems.map(p => `<div class="flex items-center gap-3"><span class="text-[#1264df]">◉</span> ${p}</div>`).join('');
                [img,title,txt,probs].forEach(el => { if(el) el.style.opacity='1'; });
            }, 180);
        });
    });
    </script>
    <style>#serviceTitle,#serviceProblems,#serviceImage,#serviceImageText{transition:opacity .18s ease}</style>
@endsection

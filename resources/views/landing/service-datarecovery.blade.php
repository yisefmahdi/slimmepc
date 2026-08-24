@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="overflow-hidden bg-white text-[#0f172a]">

        {{-- HERO --}}
        <section class="hero-data-bg relative overflow-hidden bg-gradient-to-r from-white via-[#f8fbff] to-[#f2f7ff]">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-14 pt-5 pb-10 lg:pt-6 lg:pb-14">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 mb-5 mt-3">
                    <a href="{{ url('/') }}" class="hover:text-slate-700 transition">Home</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ url('/#diensten') }}" class="hover:text-slate-700 transition">Diensten</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-[#07182c] font-semibold">{{ config('cms.pages.datarecovery.label') ?? 'Data Recovery' }}</span>
                </div>

                <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-8 items-center">
                    <div>
                        <div class="text-[#0b63e5] uppercase font-bold text-[11px] mb-4">
                            {{ $s['hero']['badge'] ?? 'Data recovery Apeldoorn' }}
                        </div>

                        <h1 class="font-black tracking-[-0.045em] leading-[1.02] text-[30px] sm:text-[52px] lg:text-[40px]">
                            {{ $s['hero']['title1'] ?? 'Belangrijke bestanden' }}
                            <span class="block text-[#0b63e5]">{{ $s['hero']['title2'] ?? 'kwijt?' }}</span>
                        </h1>

                        <h2 class="text-[22px] lg:text-[28px] font-medium mt-4">
                            {{ $s['hero']['subtitle'] ?? 'Geef je data nog niet op.' }}
                        </h2>

                        <p class="text-gray-700 text-[15px] sm:text-[16px] leading-7 mt-3 max-w-[590px] whitespace-pre-line">{{ $s['hero']['description'] ?? "Wij herstellen gegevens van beschadigde HDD's, SSD's,\nUSB-sticks, geheugenkaarten en meer." }}</p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-8">
                            @foreach ($s['hero']['usps'] ?? [
                                ['icon' => 'clipboard-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Zonder verplichting'],
                                ['icon' => 'badge-check', 'title' => 'Hoge slagingskans', 'subtitle' => 'Geavanceerde technieken'],
                                ['icon' => 'shield-check', 'title' => 'Vertrouwelijk behandeld', 'subtitle' => 'Jouw data blijft privé'],
                                ['icon' => 'clock-3', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak binnen 2–5 dagen'],
                            ] as $usp)
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full border border-[#2d91ff] flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $usp['icon'] ?? 'shield-check' }}" class="w-5 h-5 text-[#0b63e5]"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-[11px]">{{ $usp['title'] ?? '' }}</div>
                                        <div class="text-gray-500 text-[9px] mt-1">{{ $usp['subtitle'] ?? '' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-8">
                            <a href="/reparatie-aanmelden"
                               class="inline-flex items-center justify-center gap-6 bg-[#0b63e5] hover:bg-[#0958ca] text-white px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-bold text-[14px] sm:text-[15px] transition">
                                Data recovery aanvragen
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                            <a href="#proces"
                               class="inline-flex items-center justify-center gap-4 border border-[#0b63e5] text-[#0b2f62] px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-bold text-[14px] sm:text-[15px] hover:bg-blue-50 transition">
                                Hoe werkt het?
                                <i data-lucide="play-circle" class="w-5 h-5 text-[#0b63e5]"></i>
                            </a>
                        </div>
                    </div>

                    <div class="relative min-h-[400px] lg:min-h-[500px]">
                        <div class="absolute left-[2%] top-1/2 -translate-y-1/2 w-[70%]">
                            <img src="{{ asset($s['hero']['hero_image'] ?? 'assets/img/landing/c2bf5922-aa0e-445e-a81a-b4f31a4822da.png') }}" alt="Data recovery harde schijf" class="w-full object-contain">
                        </div>
                        <div class="absolute right-0 top-[7%] w-[210px] space-y-4 hidden lg:block">
                            @foreach ($s['hero']['media'] ?? [
                                ['icon' => 'file-text', 'title' => 'Documenten', 'subtitle' => 'Word, Excel, PDF...'],
                                ['icon' => 'image', 'title' => "Foto's", 'subtitle' => 'JPG, PNG, RAW...'],
                                ['icon' => 'video', 'title' => "Video's", 'subtitle' => 'MP4, MOV, AVI...'],
                                ['icon' => 'cloud', 'title' => 'Back-ups', 'subtitle' => 'Belangrijke kopieën'],
                            ] as $mi)
                                <div class="media-item bg-white border border-gray-200 rounded-xl shadow-card p-4 flex gap-3 items-center">
                                    <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $mi['icon'] ?? 'file-text' }}" class="w-7 h-7 text-[#0b63e5]"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-[13px]">{{ $mi['title'] ?? '' }}</div>
                                        <div class="text-gray-500 text-[10px] mt-1">{{ $mi['subtitle'] ?? '' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- STORAGE DEVICES --}}
        <section class="py-7 bg-[#f8fbff]">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-6">
                    <h2 class="font-black text-[30px] lg:text-[34px]">
                        {{ $s['devices']['title1'] ?? 'Waar staan je' }} <span class="text-[#0b63e5]">{{ $s['devices']['title2'] ?? 'bestanden' }}</span> {{ $s['devices']['title3'] ?? 'op?' }}
                    </h2>
                    <p class="text-gray-600 text-[12px] mt-1">
                        {{ $s['devices']['subtitle'] ?? 'Selecteer het type opslagapparaat' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach ($s['devices']['items'] ?? [
                        ['image' => 'assets/img/landing/hdd.jpeg', 'title' => 'HDD', 'subtitle' => 'Interne harde schijf'],
                        ['image' => 'assets/img/landing/SSD-hard.jpg', 'title' => 'SSD', 'subtitle' => 'Solid State Drive'],
                        ['image' => 'assets/img/landing/group_1477_group.jpeg', 'title' => 'USB-Stick', 'subtitle' => 'Geheugenstick'],
                        ['image' => 'assets/img/landing/micro-sd-kaart.jpg', 'title' => 'SD / MicroSD', 'subtitle' => 'Geheugenkaart'],
                        ['image' => 'assets/img/landing/external-hard-drive.webp', 'title' => 'Externe HDD', 'subtitle' => 'Externe harde schijf'],
                        ['image' => 'assets/img/landing/windows-apple-.jpg', 'title' => 'Laptop / PC', 'subtitle' => 'Systeem problemen'],
                    ] as $di)
                        <a href="/reparatie-aanmelden" class="device-card bg-white border border-gray-200 rounded-xl shadow-card overflow-hidden text-center">
                            <img src="{{ asset('assets/img/landing/' . basename($di['image'] ?? '')) }}" class="w-full h-[140px] object-contain p-4" alt="{{ $di['title'] ?? '' }}">
                            <div class="pb-5">
                                <div class="font-black text-[14px]">{{ $di['title'] ?? '' }}</div>
                                <div class="text-gray-500 text-[10px] mt-1">{{ $di['subtitle'] ?? '' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- PROCESS --}}
        <section id="proces" class="py-8">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="bg-white border border-gray-100 rounded-xl shadow-soft px-6 py-7">
                    <div class="text-center mb-7">
                        <h2 class="font-black text-[29px] lg:text-[33px]">
                            {{ $s['process']['title1'] ?? 'Van beschadiging naar' }} <span class="text-[#0b63e5]">{{ $s['process']['title2'] ?? 'herstel' }}</span>
                        </h2>
                        <p class="text-gray-500 text-[11px] mt-1">
                            {{ $s['process']['subtitle'] ?? 'Zo werken wij aan het terughalen van jouw data' }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr_auto_1fr_.7fr] gap-4 items-center">
                        @foreach ($s['process']['steps'] ?? [
                            ['icon' => 'stethoscope', 'title' => 'Diagnose', 'description' => 'We onderzoeken gratis de schade en de haalbaarheid.'],
                            ['icon' => 'scan-search', 'title' => 'Analyse', 'description' => 'We scannen de schijf op leesbare gegevens.'],
                            ['icon' => 'folder-open', 'title' => 'Herstel', 'description' => 'Bestanden worden veilig gekopieerd naar nieuwe opslag.'],
                            ['icon' => 'shield-check', 'title' => 'Controle', 'description' => 'We controleren de bestanden samen met jou.'],
                            ['icon' => 'cloud-download', 'title' => 'Terug naar jou', 'description' => 'Je ontvangt jouw data veilig terug.'],
                        ] as $step)
                            <div class="text-center">
                                <div class="relative w-[105px] h-[105px] mx-auto rounded-full border border-blue-100 flex items-center justify-center">
                                    <span class="absolute -top-1 left-1 w-7 h-7 rounded-full bg-[#0b63e5] text-white text-[10px] font-bold flex items-center justify-center">{{ $loop->iteration }}</span>
                                    <i data-lucide="{{ $step['icon'] ?? 'shield-check' }}" class="w-10 h-10 text-[#0b63e5]"></i>
                                </div>
                                <h3 class="font-black text-[13px] mt-4">{{ $step['title'] ?? '' }}</h3>
                                <p class="text-gray-600 text-[10px] leading-4 mt-2">{{ $step['description'] ?? '' }}</p>
                            </div>
                            @if (!$loop->last && $loop->iteration < 5)
                                <div class="hidden md:flex items-center gap-2">
                                    <div class="w-10 process-line"></div>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-[#0b63e5]"></i>
                                </div>
                            @endif
                        @endforeach

                        <div class="bg-white border border-gray-200 rounded-xl shadow-card text-center px-4 py-5">
                            <i data-lucide="lock-keyhole" class="w-11 h-11 text-[#0b63e5] mx-auto"></i>
                            <div class="font-black text-[#0b63e5] text-[24px] mt-3">100%</div>
                            <div class="font-bold text-[11px]">Vertrouwelijk</div>
                            <p class="text-gray-500 text-[9px] mt-2">Jouw privacy is onze prioriteit</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- WHAT WE CAN RECOVER --}}
        <section class="pb-8">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-5">
                    <h2 class="font-black text-[29px] lg:text-[33px]">
                        {{ $s['recover']['title1'] ?? 'Wat kunnen wij' }} <span class="text-[#0b63e5]">{{ $s['recover']['title2'] ?? 'herstellen?' }}</span>
                    </h2>
                    <p class="text-gray-500 text-[11px] mt-1">
                        {{ $s['recover']['subtitle'] ?? 'Wij werken met bijna alle opslagmedia en bestandssystemen.' }}
                    </p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-soft px-5 py-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        @foreach ($s['recover']['items'] ?? [
                            ['icon' => 'hard-drive', 'title' => 'HDD (SATA / IDE)', 'subtitle' => 'Alle merken'],
                            ['icon' => 'cpu', 'title' => 'SSD (SATA / NVMe)', 'subtitle' => 'Alle types'],
                            ['icon' => 'usb', 'title' => 'USB-Sticks', 'subtitle' => 'Alle capaciteiten'],
                            ['icon' => 'sd-card', 'title' => 'SD / MicroSD', 'subtitle' => 'Camera, telefoon'],
                            ['icon' => 'server', 'title' => 'NAS / RAID', 'subtitle' => 'RAID 0/1/5/6/10'],
                            ['icon' => 'briefcase', 'title' => 'Externe schijven', 'subtitle' => 'Alle formaten'],
                            ['icon' => 'folder-cog', 'title' => 'Bestandssystemen', 'subtitle' => 'NTFS, exFAT, FAT32, HFS+, APFS'],
                        ] as $ri)
                            <div class="flex gap-3 items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                    <i data-lucide="{{ $ri['icon'] ?? 'hard-drive' }}" class="w-6 h-6 text-[#0b63e5]"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-[10px]">{{ $ri['title'] ?? '' }}</div>
                                    <div class="text-gray-500 text-[8px] mt-1">{{ $ri['subtitle'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- CASES --}}
        <section class="pb-9">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="text-center mb-6">
                    <h2 class="font-black text-[29px] lg:text-[34px]">
                        {{ $s['cases']['title1'] ?? 'Echte data recovery' }} <span class="text-[#0b63e5]">{{ $s['cases']['title2'] ?? 'cases' }}</span>
                    </h2>
                    <p class="text-gray-500 text-[10px] mt-1">
                        {{ $s['cases']['subtitle'] ?? 'Enkele voorbeelden van succesvolle herstelde gegevens.' }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-5">
                    @foreach ($s['cases']['items'] ?? [
                        ['badge' => 'CASE #1021', 'title' => 'HDD maakt geluid', 'description' => 'Defecte harde schijf met mechanische schade.', 'result' => 'Hersteld: 1.2 TB', 'image' => 'assets/img/landing/hdd-2.avif'],
                        ['badge' => 'CASE #0987', 'title' => 'SSD wordt niet herkend', 'description' => 'SSD met controller probleem, geen toegang tot data.', 'result' => 'Hersteld: 480 GB', 'image' => 'assets/img/landing/Externe-schijf-wordt-niet-herkend-door-Windows.webp'],
                        ['badge' => 'CASE #0954', 'title' => 'USB per ongeluk geformatteerd', 'description' => "Belangrijke documenten en foto's teruggehaald.", 'result' => 'Hersteld: 64 GB', 'image' => 'assets/img/landing/group_1477_group.jpeg'],
                    ] as $cs)
                        <article class="case-card border border-gray-200 rounded-xl bg-white shadow-card overflow-hidden">
                            <div class="grid grid-cols-[.95fr_1.05fr]">
                                <img src="{{ asset('assets/img/landing/' . basename($cs['image'] ?? '')) }}" class="w-full h-full object-cover min-h-[190px]" alt="{{ $cs['title'] ?? '' }}">
                                <div class="p-5">
                                    <div class="text-[#0b63e5] font-black text-[9px]">{{ $cs['badge'] ?? '' }}</div>
                                    <h3 class="font-black text-[17px] mt-2">{{ $cs['title'] ?? '' }}</h3>
                                    <p class="text-gray-600 text-[10px] mt-3">{{ $cs['description'] ?? '' }}</p>
                                    <p class="text-green-600 font-bold text-[10px] mt-4">✓ {{ $cs['result'] ?? '' }}</p>
                                    <a href="/reparatie-aanmelden" class="inline-flex items-center gap-2 border border-[#0b63e5] px-4 py-2 rounded-md text-[10px] font-semibold mt-4">Bekijk details <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- TRUST + CTA + FAQ --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="grid lg:grid-cols-[.62fr_1.08fr_.72fr] gap-5">

                    <div class="bg-white border border-gray-200 rounded-xl shadow-card p-6">
                        <div class="flex gap-3 items-center">
                            <i data-lucide="lock-keyhole" class="w-7 h-7 text-[#0b63e5]"></i>
                            <h3 class="font-black text-[#0b3e8c] text-[18px]">{{ $s['trust_cta_faq']['trust_title'] ?? 'Jouw data is bij ons veilig' }}</h3>
                        </div>
                        <div class="space-y-4 mt-6 text-[11px]">
                            @foreach ($s['trust_cta_faq']['trust_items'] ?? [['title'=>'Geen data wordt zonder toestemming gedeeld'],['title'=>'We werken op een beveiligde werkplek'],['title'=>'Jouw data blijft uitsluitend van jou'],['title'=>'Indien niet herstelbaar: geen kosten']] as $ti)
                                <div class="flex gap-3 items-center">
                                    <i data-lucide="check" class="w-4 h-4 text-[#0b63e5]"></i>
                                    {{ $ti['title'] ?? '' }}
                                </div>
                            @endforeach
                        </div>
                        <div class="text-[#0b63e5] font-semibold mt-7 text-[12px]">Transparant &nbsp;•&nbsp; Veilig &nbsp;•&nbsp; Betrouwbaar</div>
                    </div>

                    <div id="aanvragen" class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#081b35] via-[#0c315a] to-[#091c33] min-h-[285px]">
                        <div class="absolute right-0 top-0 bottom-0 w-[46%] max-lg:hidden">
                            <img src="{{ asset($s['trust_cta_faq']['cta_image'] ?? 'assets/img/landing/e6cc3cb7-5aea-460d-a1a9-884318edc64a.png') }}" class="w-full h-full object-cover" alt="">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#0c315a] to-transparent"></div>
                        </div>
                        <div class="relative z-10 text-white p-8 lg:p-9 max-w-[65%] max-lg:max-w-full">
                            <h2 class="font-black text-[29px] lg:text-[34px] leading-tight">
                                {{ $s['trust_cta_faq']['cta_title1'] ?? 'Laat je data herstellen' }} <span class="block text-[#3c9cff]">{{ $s['trust_cta_faq']['cta_title2'] ?? 'door specialisten.' }}</span>
                            </h2>
                            <p class="text-white/80 text-[12px] leading-5 mt-4 whitespace-pre-line">{{ $s['trust_cta_faq']['cta_description'] ?? "Wacht niet langer en vergroot de kans\nop succesvol herstel." }}</p>
                            <a href="/reparatie-aanmelden" class="inline-flex items-center gap-6 bg-[#0b63e5] hover:bg-[#0958ca] px-5 py-2.5 sm:px-7 sm:py-4 rounded-lg font-bold text-[14px] sm:text-[15px] mt-6">
                                Gratis diagnose aanvragen
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                            <a href="tel:0552032145" class="flex items-center gap-3 mt-5 text-white text-[12px]">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                                Bel ons direct: 055 203 21 45
                            </a>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl shadow-card p-6">
                        <h3 class="font-black text-[16px] mb-3">{{ $s['trust_cta_faq']['faq_title'] ?? 'Veelgestelde vragen' }}</h3>
                        @foreach ($s['trust_cta_faq']['faq_items'] ?? [
                            ['question' => 'Wat kost een data recovery?', 'answer' => 'De kosten hangen af van het type opslagmedium en de schade.'],
                            ['question' => 'Hoe lang duurt het herstelproces?', 'answer' => 'Veel onderzoeken en herstellingen duren enkele werkdagen.'],
                            ['question' => 'Welke schijven kunnen jullie herstellen?', 'answer' => 'HDD, SSD, USB, SD-kaarten, externe schijven en diverse RAID-systemen.'],
                            ['question' => 'Is mijn data veilig bij jullie?', 'answer' => 'Wij behandelen alle ontvangen gegevens vertrouwelijk.'],
                            ['question' => 'Wat als de data niet herstelbaar is?', 'answer' => 'Na onderzoek bespreken wij duidelijk de haalbaarheid en mogelijkheden.'],
                        ] as $fi)
                            <div class="faq-item border-b last:border-0">
                                <button class="faq-toggle w-full flex justify-between py-4 text-left text-[11px] font-semibold">
                                    {{ $fi['question'] ?? '' }}
                                    <span class="faq-plus text-xl text-[#0b63e5]">+</span>
                                </button>
                                <div class="faq-content"><p class="pb-4 text-gray-500 text-[10px] leading-5">{{ $fi['answer'] ?? '' }}</p></div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        {{-- BOTTOM USP --}}
        <section class="pb-10">
            <div class="max-w-[1380px] mx-auto px-6 lg:px-14">
                <div class="bg-[#f7faff] border border-gray-100 rounded-xl px-6 py-5">
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach ($s['benefits']['items'] ?? [
                            ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Zonder verplichting'],
                            ['icon' => 'atom', 'title' => 'Geavanceerde technieken', 'subtitle' => 'Professionele tools'],
                            ['icon' => 'badge-check', 'title' => 'Hoge slagingskans', 'subtitle' => 'Jarenlange ervaring'],
                            ['icon' => 'clock', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak binnen 2–5 dagen'],
                            ['icon' => 'shield', 'title' => 'Geen herstel, geen kosten', 'subtitle' => 'Eerlijk & transparant'],
                        ] as $bi)
                            <div class="flex items-center gap-3">
                                <i data-lucide="{{ $bi['icon'] ?? 'shield-check' }}" class="w-7 h-7 text-[#0b63e5]"></i>
                                <div>
                                    <div class="font-bold text-[10px]">{{ $bi['title'] ?? '' }}</div>
                                    <div class="text-gray-500 text-[8px] mt-1">{{ $bi['subtitle'] ?? '' }}</div>
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

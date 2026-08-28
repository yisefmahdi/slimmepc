@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    {{-- Tailwind CDN with brand/slimme palette (same as afspraak.html) — kept inside section since this page was standalone before --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#1264ff',
                            600: '#0759f5',
                            700: '#074ccc',
                            800: '#0b347a',
                            900: '#071f4f',
                            950: '#04142f'
                        },
                        slimme: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#bcdcff',
                            300: '#8ec6ff',
                            400: '#59a6ff',
                            500: '#2f86ff',
                            600: '#0f66f5',
                            700: '#0c52cc',
                            800: '#11429e',
                            900: '#143a7d',
                        },
                    },
                    boxShadow: {
                        soft: '0 12px 40px rgba(15, 58, 120, 0.08)',
                        card: '0 10px 35px rgba(21, 62, 120, 0.08)',
                        blue: '0 14px 30px -10px rgba(15, 102, 245, 0.45)',
                    },
                },
            },
        };
    </script>

    <script src="{{ asset('assets/js/vendor/lucide.min.js') }}"></script>
    <script>window.__lucideOld = window.lucide;</script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>window.__lucideLatest = window.lucide;</script>
    <script>
        window.__lucideRefresh = function () {
            try { if (window.__lucideOld && window.__lucideOld.createIcons) window.__lucideOld.createIcons(); } catch (e) {}
            try { if (window.__lucideLatest && window.__lucideLatest.createIcons) window.__lucideLatest.createIcons(); } catch (e) {}
        };
    </script>

    <style>
        input, textarea, select { outline: none; }

        /* device cards — exact afspraak.html look */
        .device-card {
            transition: border-color .2s ease, background-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }
        .device-card:hover {
            border-color: #1264ff;
            transform: translateY(-2px);
        }
        .device-card.active {
            border-color: #1264ff;
            background: #f8fbff;
            box-shadow: 0 0 0 1px #1264ff;
        }
        .device-card.active svg { color: #1264ff; }
        .device-card.active .device-label { color: #0759f5; }

        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        input[type="date"] { position: relative; }

        /* reparatie-style field error state */
        .field:user-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .12) !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="bg-[#f7faff] text-[#061f4d]">
    <main class="relative overflow-hidden">

    <!-- soft hero background -->
    <div class="absolute inset-x-0 top-0 h-[520px] bg-gradient-to-b from-[#f0f6ff] via-[#f8fbff] to-transparent -z-10"></div>

    <!-- HERO -->
    <section class="max-w-[1390px] mx-auto px-5 sm:px-8 lg:px-10 pt-14 lg:pt-16">
        <div class="grid lg:grid-cols-[1.03fr_.97fr] items-center gap-8 lg:gap-14">

            <!-- LEFT -->
            <div>
                <div class="inline-flex items-center rounded-full bg-[#e4efff] px-3 py-1.5 text-[11px] font-bold uppercase tracking-[.02em] text-[#0759f5]">
                    {{ $s['hero']['badge'] ?? 'Service aan huis' }}
                </div>

                <h1 class="mt-5 text-[38px] sm:text-[46px] lg:text-[51px] leading-[1.05] font-extrabold tracking-[-0.035em] text-[#061f4d]">
                    {{ $s['hero']['title1'] ?? 'Afspraak aan huis' }}
                </h1>

                <p class="mt-5 max-w-[650px] text-[17px] leading-8 text-[#17335f]">
                    {{ $s['hero']['description'] ?? 'Wij komen bij u langs op een tijdstip dat het u uitkomt. Vul het formulier in en wij nemen zo snel mogelijk contact met u op om de afspraak te bevestigen.' }}
                </p>

                <!-- HERO BENEFITS -->
                <div class="mt-8 flex flex-wrap gap-x-9 gap-y-4">
                    @php
                        $heroBenefits = $s['hero']['benefits'] ?? [];
                        $heroSvgs = [
                            '<path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/>',
                            '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
                            '<circle cx="12" cy="12" r="9"/><path d="M15 8.7A4 4 0 1 0 15 15"/><path d="M8 11h6"/><path d="M8 13h5"/>',
                        ];
                        $heroDefaults = ['Deskundige monteurs','Op tijd bij u thuis','Duidelijke prijs vooraf'];
                    @endphp
                    @if (!empty($heroBenefits))
                        @foreach ($heroBenefits as $i => $b)
                            @php $svg = $heroSvgs[$i % count($heroSvgs)]; $title = $b['title'] ?? ($heroDefaults[$i] ?? ''); @endphp
                            <div class="flex items-center gap-3">
                                <div class="text-[#0759f5]">
                                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $svg !!}</svg>
                                </div>
                                <span class="text-[14px] font-semibold text-[#102b55]">{{ $title }}</span>
                            </div>
                        @endforeach
                    @else
                        @foreach ($heroDefaults as $i => $defTitle)
                            @php $svg = $heroSvgs[$i % count($heroSvgs)]; @endphp
                            <div class="flex items-center gap-3">
                                <div class="text-[#0759f5]">
                                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $svg !!}</svg>
                                </div>
                                <span class="text-[14px] font-semibold text-[#102b55]">{{ $defTitle }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- RIGHT HERO IMAGE -->
            <div class="relative flex justify-center lg:justify-end">
                <div class="absolute w-[390px] h-[260px] rounded-full bg-[#d8e9ff]/60 blur-[80px] -z-10"></div>
                <img src="{{ asset($s['hero']['hero_image'] ?? 'assets/img/landing/other-it-service.png') }}"
                     alt="{{ $s['hero']['hero_image_alt'] ?? 'Slimme-PC service aan huis' }}"
                     class="w-full max-w-[620px] h-auto object-contain drop-shadow-[0_25px_25px_rgba(43,83,130,.12)]"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/landing/other-it-service.png') }}';">
            </div>
        </div>
    </section>

    <!-- FORM WRAPPER -->
    <section class="max-w-[1390px] mx-auto px-5 sm:px-8 lg:px-10 mt-10 lg:mt-12">
        <div class="bg-white rounded-[16px] border border-[#e0e9f6] shadow-card overflow-hidden">

            <!-- STEPS -->
            <div class="px-6 sm:px-10 lg:px-16 py-8 border-b border-[#e5edf7]">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto_1fr_auto_1fr] gap-5 lg:gap-7 items-center">
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0 w-[54px] h-[54px] rounded-full flex items-center justify-center text-white font-bold text-[18px] bg-[#0759f5] shadow-[0_7px_18px_rgba(7,89,245,.22)]">1</div>
                        <div>
                            <div class="font-bold text-[15px] text-[#071f4f]">Gegevens</div>
                            <div class="text-[14px] mt-1 text-[#354c70]">Uw contactgegevens</div>
                        </div>
                    </div>
                    <div class="hidden lg:block w-[130px] h-[2px] bg-[#d4dfef]"></div>
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0 w-[54px] h-[54px] rounded-full border-2 border-[#d3dfee] bg-white flex items-center justify-center font-bold text-[18px] text-[#071f4f]">2</div>
                        <div>
                            <div class="font-bold text-[15px] text-[#071f4f]">Apparaat &amp; probleem</div>
                            <div class="text-[14px] mt-1 text-[#354c70]">Wat is het probleem?</div>
                        </div>
                    </div>
                    <div class="hidden lg:block w-[130px] h-[2px] bg-[#d4dfef]"></div>
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0 w-[54px] h-[54px] rounded-full border-2 border-[#d3dfee] bg-white flex items-center justify-center font-bold text-[18px] text-[#071f4f]">3</div>
                        <div>
                            <div class="font-bold text-[15px] text-[#071f4f]">Afspraak</div>
                            <div class="text-[14px] mt-1 text-[#354c70]">Kies datum &amp; tijdstip</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <form id="afspraakForm" action="#" method="POST" class="px-6 sm:px-10 lg:px-16 pt-7 pb-8" novalidate>
                @csrf

                <!-- SECTION 1 -->
                <div class="relative">
                    <div class="flex gap-5 lg:gap-7">
                        <div class="hidden sm:flex flex-shrink-0 w-[54px] h-[54px] rounded-full bg-[#edf4ff] text-[#0759f5] items-center justify-center">
                            <svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-[17px] font-bold text-[#071f4f]">1. Uw gegevens</h2>

                            <div class="mt-6 grid lg:grid-cols-2 gap-x-10 gap-y-5">
                                <div class="space-y-5">
                                    <div>
                                        <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Naam *</label>
                                        <input type="text" name="name" required
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                               placeholder="Voornaam en achternaam">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">E-mailadres *</label>
                                        <input type="email" name="email" required
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                               placeholder="voorbeeld@mail.nl">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Straat *</label>
                                        <input type="text" name="street" required
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                               placeholder="Straatnaam">
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div>
                                        <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Telefoonnummer *</label>
                                        <input type="tel" name="phone" required
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                               placeholder="06 12345678">
                                    </div>
                                    <div class="grid grid-cols-[1.05fr_.95fr] gap-5">
                                        <div>
                                            <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Postcode *</label>
                                            <input type="text" name="postcode" required
                                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm uppercase outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                                   placeholder="1234 AB">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Huisnummer *</label>
                                            <input type="text" name="house_number" required
                                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                                   placeholder="12">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Plaats *</label>
                                        <input type="text" name="city" required value="Apeldoorn"
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                               placeholder="Apeldoorn">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-7 sm:ml-[81px] h-px bg-[#e2eaf5]"></div>
                </div>

                <!-- SECTION 2 -->
                <div class="mt-7">
                    <div class="flex gap-5 lg:gap-7">
                        <div class="hidden sm:flex flex-shrink-0 w-[54px] h-[54px] rounded-full bg-[#edf4ff] text-[#0759f5] items-center justify-center">
                            <svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="11" rx="1"/><path d="M2 19h20"/><path d="M9 15v4"/><path d="M15 15v4"/></svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-[17px] font-bold text-[#071f4f]">2. {{ $s['help']['heading'] ?? 'Waar kunnen we mee helpen?' }}</h2>

                            @if (!empty($s['help']['subtitle']))
                                <p class="mt-2 max-w-[640px] text-[14px] leading-6 text-[#354c70]">{{ $s['help']['subtitle'] }}</p>
                            @endif

                            <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                @php $devices = $s['help']['devices'] ?? []; @endphp
                                @if (!empty($devices))
                                    @foreach ($devices as $i => $d)
                                        <button type="button" onclick="selectDevice(this, '{{ addslashes($d['label']) }}')"
                                                class="device-card {{ $i === 0 ? 'active' : '' }} min-h-[91px] rounded-[7px] border {{ $i === 0 ? 'border-[#1264ff]' : 'border-[#d4deeb]' }} bg-white flex flex-col items-center justify-center gap-2.5">
                                            <i data-lucide="{{ $d['icon'] ?? 'cpu' }}" class="h-[30px] w-[30px]"></i>
                                            <span class="device-label text-[13px] font-semibold">{{ $d['label'] }}</span>
                                        </button>
                                    @endforeach
                                @else
                                    @foreach (['Laptop','PC / Desktop','Printer','Netwerk','Anders'] as $i => $label)
                                        <button type="button" onclick="selectDevice(this, '{{ $label }}')"
                                                class="device-card {{ $i === 0 ? 'active' : '' }} min-h-[91px] rounded-[7px] border {{ $i === 0 ? 'border-[#1264ff]' : 'border-[#d4deeb]' }} bg-white flex flex-col items-center justify-center gap-2.5">
                                            <i data-lucide="cpu" class="h-[30px] w-[30px]"></i>
                                            <span class="device-label text-[13px] font-semibold">{{ $label }}</span>
                                        </button>
                                    @endforeach
                                @endif
                            </div>

                            <input type="hidden" name="device" id="deviceInput" value="{{ (!empty($devices) && isset($devices[0]['label'])) ? addslashes($devices[0]['label']) : 'Laptop' }}">

                            <div id="deviceOtherWrap" class="mt-4 hidden">
                                <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Omschrijf uw apparaat</label>
                                <input type="text" id="deviceOther" name="deviceOther"
                                       class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                       placeholder="Bijv. tablet, smart-tv, gameconsole">
                            </div>

                            <div class="mt-6">
                                <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Omschrijf het probleem *</label>
                                <textarea name="problem" rows="4" required
                                          class="field resize-none w-full min-h-[96px] rounded-2xl border border-slate-200 bg-white p-4 text-sm leading-6 outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                          placeholder="Beschrijf zo duidelijk mogelijk wat het probleem is..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-7 sm:ml-[81px] h-px bg-[#e2eaf5]"></div>
                </div>

                <!-- SECTION 3 -->
                <div class="mt-7">
                    <div class="flex gap-5 lg:gap-7">
                        <div class="hidden sm:flex flex-shrink-0 w-[54px] h-[54px] rounded-full bg-[#edf4ff] text-[#0759f5] items-center justify-center">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M3 10h18"/><path d="M8 14h2"/><path d="M12 14h2"/><path d="M16 14h2"/><path d="M8 18h2"/><path d="M12 18h2"/></svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-[17px] font-bold text-[#071f4f]">3. Wanneer komt het uit?</h2>

                            <div class="mt-6 grid md:grid-cols-2 gap-7">
                                <div>
                                    <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Voorkeursdatum *</label>
                                    <div class="relative">
                                        <input type="date" name="preferred_date" required
                                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 pr-12 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#102b55]">
                                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 text-[13px] font-semibold text-[#102b55]">Voorkeur tijdstip *</label>
                                    <div class="relative">
                                        <select name="preferred_time" required
                                                class="field appearance-none w-full rounded-2xl border border-slate-200 bg-white px-4 pr-11 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                            <option value="">Kies een tijdstip</option>
                                            <option value="09:00 - 11:00">09:00 - 11:00</option>
                                            <option value="11:00 - 13:00">11:00 - 13:00</option>
                                            <option value="13:00 - 15:00">13:00 - 15:00</option>
                                            <option value="15:00 - 17:00">15:00 - 17:00</option>
                                            <option value="17:00 - 19:00">17:00 - 19:00</option>
                                        </select>
                                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2">
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#102b55" stroke-width="2" stroke-linecap="round"><path d="m7 10 5 5 5-5"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- INFO -->
                            <div class="mt-6 rounded-[6px] bg-[#eff5ff] min-h-[49px] px-5 flex items-center gap-3 text-[#174d9f]">
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#0759f5" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg>
                                <span class="text-[13px] font-medium">Wij nemen contact met u op om de afspraak definitief te bevestigen.</span>
                            </div>

                            <!-- BUTTON (reparatie-style) -->
                            <button type="submit" id="afspraakSubmit"
                                    class="mt-6 relative inline-flex min-h-[56px] w-full items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-7 text-sm font-black text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700 disabled:cursor-wait disabled:opacity-70">
                                <span id="afspraakSubmitSpinner" class="hidden">
                                    <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                                </span>
                                <span id="afspraakSubmitLabel">Afspraak aanvragen</span>
                            </button>

                            <!-- PRIVACY -->
                            <div class="mt-5 flex justify-center items-center gap-2.5 text-[#7587a2]">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                <span class="text-[12px]">Uw gegevens zijn veilig en worden niet met derden gedeeld.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- SUCCESS -->
            <div id="afspraakSuccess" class="hidden px-6 sm:px-10 lg:px-16 py-14 text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <i data-lucide="check-circle-2" class="h-9 w-9"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Aanvraag verzonden!</h3>
                <p class="mx-auto mt-2 max-w-md text-slate-600">
                    Bedankt! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.
                    Uw aanvraagnummer is <span id="afspraakNumber" class="font-black text-slimme-600"></span>.
                </p>
                <div class="mx-auto mt-6 max-w-md rounded-2xl border border-slate-100 bg-slate-50 p-4 text-left text-sm text-slate-600">
                    <p class="font-bold text-slate-800">Wat gebeurt er nu?</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>U ontvangt een bevestigingsmail.</li>
                        <li>Wij bellen of mailen u binnen 24 uur voor een definitieve afspraak.</li>
                        <li>Onze monteur komt op het afgesproken tijdstip bij u langs.</li>
                    </ul>
                </div>
                <button type="button" onclick="location.reload()"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-slimme-600 px-6 py-3 text-sm font-black text-white shadow-blue transition hover:bg-slimme-700">
                    Nieuwe aanvraag
                </button>
            </div>
        </div>
    </section>

    <!-- BOTTOM BENEFITS -->
    <section class="max-w-[1390px] mx-auto px-5 sm:px-8 lg:px-10 mt-8 mb-14">
        <div class="rounded-[12px] border border-[#e7eef9] bg-gradient-to-r from-[#f5f9ff] via-white to-[#f5f9ff] shadow-soft px-5 py-5">
                <div class="grid sm:grid-cols-2 xl:grid-cols-4 divide-y sm:divide-y-0 xl:divide-x divide-[#e2eaf6]">
                @php
                    $benefitItems = $s['benefits']['items'] ?? [];
                    $bottomSvgs = [
                        '<path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
                        '<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>',
                        '<path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/>',
                        '',
                    ];
                    $bottomDefaults = [
                        ['Gratis voorrijkosten','Binnen Apeldoorn'],
                        ['Binnen 24 uur reactie','We nemen snel contact op'],
                        ['2 jaar garantie','Op reparaties en onderdelen'],
                        ['5/5 beoordeling','op Google'],
                    ];
                @endphp
                @foreach (($benefitItems ?: $bottomDefaults) as $i => $b)
                    @php
                        $icon  = is_array($b) ? ($b['icon'] ?? '') : '';
                        $title = is_array($b) ? ($b['title'] ?? ($bottomDefaults[$i][0] ?? '')) : $b;
                        $desc  = is_array($b) ? ($b['description'] ?? ($bottomDefaults[$i][1] ?? '')) : '';
                        $isStar = $icon === 'star' || str_contains(strtolower($title), 'beoordeling');
                        $svg   = $bottomSvgs[$i % 4];
                        $thumbSvg = '<path d="M7 10v10"/><path d="M3 10h4v10H3z"/><path d="M7 10l4-7c2 0 3 1 3 3v3h5a2 2 0 0 1 2 2l-2 7a3 3 0 0 1-3 2H7"/>';
                    @endphp
                    <div class="flex items-center gap-4 px-4 py-3">
                        <div class="flex-shrink-0 w-[48px] h-[48px] rounded-full bg-[#eaf2ff] flex items-center justify-center text-[#0759f5]">
                            @if ($isStar)
                                <svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $thumbSvg !!}</svg>
                            @elseif ($icon)
                                <i data-lucide="{{ $icon }}" class="h-[29px] w-[29px]"></i>
                            @else
                                <svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $svg !!}</svg>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-[13px] text-[#071f4f]">{{ $title }}</div>
                            @if ($isStar)
                                <div class="mt-1 flex items-center gap-1.5">
                                    <div class="text-[#ffb400] text-[14px] tracking-[-1px] leading-none">★★★★★</div>
                                    <span class="text-[11px] text-[#526784]">{{ $desc }}</span>
                                </div>
                            @else
                                <div class="mt-1 text-[12px] text-[#526784]">{{ $desc }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    </main>
    </div>

    @include('landing.partials.footer')

<script>
    (function () {
        const d = new Date();
        const iso = d.toISOString().split('T')[0];
        const el = document.getElementById('preferred_date');
        if (el) el.min = iso;
    })();

    function selectDevice(element, device) {
        document.querySelectorAll('.device-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');

        const input = document.getElementById('deviceInput');
        const otherWrap = document.getElementById('deviceOtherWrap');
        const other = document.getElementById('deviceOther');

        if (device === 'Anders') {
            otherWrap.classList.remove('hidden');
            input.value = other.value || '';
        } else {
            otherWrap.classList.add('hidden');
            if (other) other.value = '';
            input.value = device;
        }
    }

    document.getElementById('deviceOther')?.addEventListener('input', function () {
        document.getElementById('deviceInput').value = this.value;
    });

    const form = document.getElementById('afspraakForm');
    const btn = document.getElementById('afspraakSubmit');
    const btnLabel = document.getElementById('afspraakSubmitLabel');
    const btnSpinner = document.getElementById('afspraakSubmitSpinner');
    const messages = ['Bezig met verzenden…', 'Gegevens worden gecontroleerd…', 'Afspraak wordt ingepland…'];
    let msgTimer = null;

    if (form) {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.reportValidity()) return;

        const device = document.getElementById('deviceInput').value.trim();
        if (!device) {
            alert('Kies een apparaat of vul "Anders" in.');
            return;
        }

        btn.disabled = true;
        if (btnSpinner) btnSpinner.classList.remove('hidden');
        let i = 0;
        if (btnLabel) btnLabel.textContent = messages[0];
        msgTimer = setInterval(() => {
            i = (i + 1) % messages.length;
            if (btnLabel) btnLabel.textContent = messages[i];
        }, 2000);

        const fd = new FormData(form);
        const json = Object.fromEntries(fd.entries());
        delete json.deviceOther;

        try {
            const res = await fetch('{{ route('afspraak.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '',
                },
                body: JSON.stringify(json),
            });
            const data = await res.json();
            clearInterval(msgTimer);

            if (res.ok && data.success) {
                form.classList.add('hidden');
                document.getElementById('afspraakSuccess')?.classList.remove('hidden');
                const numEl = document.getElementById('afspraakNumber');
                if (numEl) numEl.textContent = data.afspraak_number;
                if (window.__lucideRefresh) window.__lucideRefresh();
            } else {
                throw new Error(data.message || 'Er ging iets mis.');
            }
        } catch (err) {
            clearInterval(msgTimer);
            if (btn) btn.disabled = false;
            if (btnSpinner) btnSpinner.classList.add('hidden');
            if (btnLabel) btnLabel.textContent = 'Afspraak aanvragen';
            alert(err.message || 'Er ging iets mis. Probeer het opnieuw.');
        }
    });
    }

    if (window.__lucideRefresh) window.__lucideRefresh();
</script>
@endsection

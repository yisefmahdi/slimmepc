<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Afspraak aan huis | Slimme-PC</title>
    <meta name="description" content="Maak een afspraak aan huis bij Slimme-PC. Deskundige monteurs komen bij u langs in Apeldoorn en omgeving.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef4ff',
                            100: '#d9e6ff',
                            500: '#3b6ef6',
                            600: '#0759f5',
                            700: '#0a47c2',
                        },
                        slimme: {
                            50:  '#eef6ff',
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
                        soft: '0 10px 40px -12px rgba(2, 8, 23, 0.12)',
                        card: '0 18px 50px -20px rgba(2, 8, 23, 0.18)',
                        blue: '0 14px 30px -10px rgba(15, 102, 245, 0.45)',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">

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
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased">
@include('landing.partials.header')

<!-- HERO -->
<section class="relative overflow-hidden bg-gradient-to-b from-slimme-50 to-white pt-10 lg:pt-16">
    <div class="mx-auto max-w-7xl px-5">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div data-aos="fade-right">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-1.5 text-sm font-bold text-brand-600 ring-1 ring-brand-100">
                    <i data-lucide="home" class="h-4 w-4"></i>
                    {{ $s['hero']['badge'] ?? 'Service aan huis' }}
                </span>

                <h1 class="mt-5 text-4xl font-black leading-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    {{ $s['hero']['title1'] ?? 'Afspraak aan huis' }}
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-600">
                    {{ $s['hero']['description'] ?? '' }}
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    @php $heroBenefits = $s['hero']['benefits'] ?? []; @endphp
                    @foreach ($heroBenefits as $b)
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-soft">
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <i data-lucide="{{ $b['icon'] ?? 'check' }}" class="h-5 w-5"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">{{ $b['title'] ?? '' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $b['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                    @if (empty($heroBenefits))
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-soft">
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <i data-lucide="shield-check" class="h-5 w-5"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Deskundige monteurs</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-soft">
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <i data-lucide="clock" class="h-5 w-5"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Op tijd bij u thuis</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-soft">
                            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <i data-lucide="badge-check" class="h-5 w-5"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Duidelijke prijs vooraf</p>
                        </div>
                    @endif
                </div>
            </div>

            <div data-aos="fade-left" class="relative">
                <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-slimme-600 p-2 shadow-card">
                    <img src="{{ asset($s['hero']['hero_image'] ?? 'assets/img/landing/other-it-service.png') }}"
                         alt="{{ $s['hero']['hero_image_alt'] ?? 'Slimme-PC service aan huis' }}"
                         class="w-full rounded-2xl object-cover"
                         onerror="this.onerror=null;this.src='{{ asset('assets/img/landing/other-it-service.png') }}';">
                </div>
                <div class="absolute -bottom-5 -left-5 hidden rounded-2xl bg-white px-5 py-3 shadow-card sm:block">
                    <p class="text-xs font-semibold text-slate-500">Binnen Apeldoorn</p>
                    <p class="text-lg font-black text-slate-900">Gratis voorrijkosten</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FORM -->
<section class="py-14 lg:py-20">
    <div class="mx-auto max-w-5xl px-5">
        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-card">
            <div class="bg-slimme-600 px-6 py-6 text-white sm:px-10">
                <h2 class="text-2xl font-black sm:text-3xl">Maak een afspraak aan huis</h2>
                <p class="mt-1 text-sm text-blue-100">Vul uw gegevens in en wij nemen spoedig contact met u op.</p>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-white/10 px-3 py-3 text-center">
                        <span class="block text-sm font-black text-white">1</span>
                        <span class="mt-1 block text-xs text-blue-50">Gegevens</span>
                    </div>
                    <div class="rounded-xl bg-white/10 px-3 py-3 text-center">
                        <span class="block text-sm font-black text-white">2</span>
                        <span class="mt-1 block text-xs text-blue-50">Apparaat &amp; probleem</span>
                    </div>
                    <div class="rounded-xl bg-white/10 px-3 py-3 text-center">
                        <span class="block text-sm font-black text-white">3</span>
                        <span class="mt-1 block text-xs text-blue-50">Afspraak</span>
                    </div>
                </div>
            </div>

            <form id="afspraakForm" class="space-y-10 px-6 py-8 sm:px-10" novalidate>
                @csrf

                <!-- STEP 1 -->
                <div>
                    <h3 class="mb-5 flex items-center gap-2 text-lg font-black text-slate-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slimme-50 text-sm font-black text-slimme-600">1</span>
                        Uw gegevens
                    </h3>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Naam *</label>
                            <input type="text" id="name" name="name" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="Uw volledige naam">
                        </div>
                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-slate-800">E-mailadres *</label>
                            <input type="email" id="email" name="email" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="naam@voorbeeld.nl">
                        </div>
                        <div>
                            <label for="street" class="mb-2 block text-sm font-bold text-slate-800">Straat *</label>
                            <input type="text" id="street" name="street" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="Straatnaam">
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-bold text-slate-800">Telefoon *</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="06 - 12 34 56 78">
                        </div>
                        <div>
                            <label for="postcode" class="mb-2 block text-sm font-bold text-slate-800">Postcode *</label>
                            <input type="text" id="postcode" name="postcode" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm uppercase outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="1234 AB">
                        </div>
                        <div>
                            <label for="house_number" class="mb-2 block text-sm font-bold text-slate-800">Huisnummer *</label>
                            <input type="text" id="house_number" name="house_number" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="12">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="city" class="mb-2 block text-sm font-bold text-slate-800">Stad *</label>
                            <input type="text" id="city" name="city" required value="Apeldoorn"
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                   placeholder="Apeldoorn">
                        </div>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div>
                    <h3 class="mb-2 flex items-center gap-2 text-lg font-bold text-slate-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slimme-50 text-sm font-black text-slimme-600">2</span>
                        {{ $s['help']['heading'] ?? 'Waar kunnen we mee helpen?' }}
                    </h3>
                    <p class="mb-5 text-sm text-slate-500">{{ $s['help']['subtitle'] ?? '' }}</p>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5" id="deviceGrid">
                        @php $devices = $s['help']['devices'] ?? []; @endphp
                        @if (!empty($devices))
                            @foreach ($devices as $i => $d)
                                <button type="button" onclick="selectDevice(this, '{{ addslashes($d['label']) }}')"
                                        class="device-card relative flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white p-4 text-center transition hover:border-slimme-400 {{ $i === 0 ? 'border-slimme-500 bg-slimme-50' : '' }}">
                                    <i data-lucide="{{ $d['icon'] ?? 'cpu' }}" class="h-7 w-7 text-slimme-600"></i>
                                    <span class="text-sm font-bold text-slate-700">{{ $d['label'] }}</span>
                                    <span class="device-check absolute right-2 top-2 hidden text-slimme-600">
                                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                                    </span>
                                </button>
                            @endforeach
                        @else
                            @foreach (['Laptop','PC / Desktop','Printer','Netwerk','Anders'] as $i => $label)
                                <button type="button" onclick="selectDevice(this, '{{ $label }}')"
                                        class="device-card relative flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white p-4 text-center transition hover:border-slimme-400 {{ $i === 0 ? 'border-slimme-500 bg-slimme-50' : '' }}">
                                    <i data-lucide="cpu" class="h-7 w-7 text-slimme-600"></i>
                                    <span class="text-sm font-bold text-slate-700">{{ $label }}</span>
                                    <span class="device-check absolute right-2 top-2 hidden text-slimme-600">
                                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                                    </span>
                                </button>
                            @endforeach
                        @endif
                    </div>

                    <input type="hidden" id="deviceInput" name="device" value="{{ (!empty($devices) && isset($devices[0]['label'])) ? addslashes($devices[0]['label']) : 'Laptop' }}">

                    <div id="deviceOtherWrap" class="mt-3 hidden">
                        <label for="deviceOther" class="mb-2 block text-sm font-bold text-slate-800">Omschrijf uw apparaat</label>
                        <input type="text" id="deviceOther" name="deviceOther"
                               class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                               placeholder="Bijv. tablet, smart-tv, gameconsole">
                    </div>

                    <div class="mt-5">
                        <label for="problem" class="mb-2 block text-sm font-bold text-slate-800">Omschrijf uw probleem *</label>
                        <textarea id="problem" name="problem" rows="4" required
                                  class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"
                                  placeholder="Vertel kort wat er aan de hand is, zodat we goed voorbereid langs komen."></textarea>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div>
                    <h3 class="mb-5 flex items-center gap-2 text-lg font-black text-slate-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slimme-50 text-sm font-black text-slimme-600">3</span>
                        Wanneer komt het uit?
                    </h3>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="preferred_date" class="mb-2 block text-sm font-bold text-slate-800">Gewenste datum *</label>
                            <input type="date" id="preferred_date" name="preferred_date" required
                                   class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                        </div>
                        <div>
                            <label for="preferred_time" class="mb-2 block text-sm font-bold text-slate-800">Gewenst tijdstip *</label>
                            <select id="preferred_time" name="preferred_time" required
                                    class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                <option value="">Kies een tijdstip</option>
                                <option value="09:00 - 11:00">09:00 - 11:00</option>
                                <option value="11:00 - 13:00">11:00 - 13:00</option>
                                <option value="13:00 - 15:00">13:00 - 15:00</option>
                                <option value="15:00 - 17:00">15:00 - 17:00</option>
                                <option value="17:00 - 19:00">17:00 - 19:00</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 flex items-start gap-2 rounded-2xl bg-slate-50 p-3 text-xs text-slate-500">
                        <i data-lucide="lock" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"></i>
                        <span>Uw gegevens zijn veilig bij ons en worden alleen gebruikt om uw afspraak te plannen.</span>
                    </div>

                    <div class="mt-5 rounded-2xl border border-slimme-100 bg-slimme-50 p-4 text-sm text-slate-600">
                        <i data-lucide="info" class="mr-1 inline h-4 w-4 text-slimme-600"></i>
                        Wij nemen binnen 24 uur contact met u op om de afspraak te bevestigen.
                    </div>

                    <div class="mt-6">
                        <button type="submit" id="afspraakSubmit"
                                class="inline-flex min-h-[56px] w-full items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-7 text-sm font-black text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700 disabled:cursor-wait disabled:opacity-70 sm:w-auto">
                            <span id="afspraakSubmitSpinner" class="hidden">
                                <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                            </span>
                            <span id="afspraakSubmitLabel">Afspraak aanvragen</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- SUCCESS -->
            <div id="afspraakSuccess" class="hidden px-6 py-14 text-center sm:px-10">
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
    </div>
</section>

<!-- BOTTOM BENEFITS -->
<section class="bg-white py-14">
    <div class="mx-auto max-w-6xl px-5">
        <h2 class="text-center text-2xl font-black text-slate-900 sm:text-3xl">
            {{ $s['benefits']['heading'] ?? 'Waarom kiezen voor Slimme-PC aan huis?' }}
        </h2>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @php $items = $s['benefits']['items'] ?? []; @endphp
            @forelse ($items as $it)
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 text-center shadow-soft">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                        <i data-lucide="{{ $it['icon'] ?? 'check' }}" class="h-6 w-6"></i>
                    </div>
                    <p class="text-base font-black text-slate-800">{{ $it['title'] ?? '' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $it['description'] ?? '' }}</p>
                </div>
            @empty
                @foreach ([['truck','Gratis voorrijkosten','Binnen Apeldoorn'],['clock','Binnen 24 uur reactie','We nemen snel contact op'],['shield-check','2 jaar garantie','Op reparaties'],['star','5/5 beoordeling','op Google']] as $it)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 text-center shadow-soft">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                            <i data-lucide="{{ $it[0] }}" class="h-6 w-6"></i>
                        </div>
                        <p class="text-base font-black text-slate-800">{{ $it[1] }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $it[2] }}</p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

<script>
    // min date = today
    (function () {
        const d = new Date();
        const iso = d.toISOString().split('T')[0];
        const el = document.getElementById('preferred_date');
        if (el) el.min = iso;
    })();

    function selectDevice(el, label) {
        document.querySelectorAll('.device-card').forEach(c => {
            c.classList.remove('border-slimme-500', 'bg-slimme-50');
            c.classList.add('border-slate-200');
            const chk = c.querySelector('.device-check');
            if (chk) chk.classList.add('hidden');
        });
        el.classList.add('border-slimme-500', 'bg-slimme-50');
        el.classList.remove('border-slate-200');
        const chk = el.querySelector('.device-check');
        if (chk) chk.classList.remove('hidden');

        const input = document.getElementById('deviceInput');
        const otherWrap = document.getElementById('deviceOtherWrap');
        const other = document.getElementById('deviceOther');

        if (label === 'Anders') {
            otherWrap.classList.remove('hidden');
            input.value = other.value || '';
        } else {
            otherWrap.classList.add('hidden');
            other.value = '';
            input.value = label;
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

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.reportValidity()) return;

        const device = document.getElementById('deviceInput').value.trim();
        if (!device) {
            alert('Kies een apparaat of vul "Anders" in.');
            return;
        }

        btn.disabled = true;
        btnSpinner.classList.remove('hidden');
        let i = 0;
        btnLabel.textContent = messages[0];
        msgTimer = setInterval(() => {
            i = (i + 1) % messages.length;
            btnLabel.textContent = messages[i];
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                },
                body: JSON.stringify(json),
            });
            const data = await res.json();
            clearInterval(msgTimer);

            if (res.ok && data.success) {
                form.classList.add('hidden');
                const success = document.getElementById('afspraakSuccess');
                success.classList.remove('hidden');
                document.getElementById('afspraakNumber').textContent = data.afspraak_number;
            } else {
                throw new Error(data.message || 'Er ging iets mis.');
            }
        } catch (err) {
            clearInterval(msgTimer);
            btn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnLabel.textContent = 'Afspraak aanvragen';
            alert(err.message || 'Er ging iets mis. Probeer het opnieuw.');
        }
    });
</script>

<script>
    if (window.__lucideRefresh) {
        window.__lucideRefresh();
    } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
</script>
</body>
</html>

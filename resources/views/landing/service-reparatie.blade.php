<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparatie aanmelden | Slimme-PC</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons: vendored build has brand icons (facebook/instagram/youtube),
         latest build has newer icons. Load both and refresh from each registry. -->
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

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slimme: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#2563eb',
                            600: '#1557d6',
                            700: '#0f46b7',
                            800: '#0b358e',
                            900: '#071f56',
                            950: '#04132f'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 60px rgba(15, 70, 183, 0.10)',
                        card: '0 10px 35px rgba(15, 70, 183, 0.08)',
                        blue: '0 16px 35px rgba(37, 99, 235, 0.24)'
                    }
                }
            }
        };
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">

    <style>
        html { scroll-behavior: smooth; }

        body {
            background:
                radial-gradient(circle at 85% 4%, rgba(96, 165, 250, .18), transparent 27rem),
                radial-gradient(circle at 4% 20%, rgba(219, 234, 254, .85), transparent 25rem),
                #f8fbff;
        }

        .step-panel { display: none; }
        .step-panel.active { display: block; animation: fadeUp .35s ease; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .choice-card.selected,
        .problem-card.selected {
            border-color: #2563eb;
            background: linear-gradient(145deg, #ffffff 0%, #eff6ff 100%);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .14);
            transform: translateY(-2px);
        }

        .choice-card.selected .select-check,
        .problem-card.selected .select-check {
            opacity: 1;
            transform: scale(1);
        }

        .select-check {
            opacity: 0;
            transform: scale(.7);
            transition: .2s ease;
        }

        .progress-step.active .progress-number {
            color: white;
            background: #1557d6;
            box-shadow: 0 8px 20px rgba(21, 87, 214, .26);
        }

        .progress-step.completed .progress-number {
            color: white;
            background: #16a34a;
        }

        .progress-step.active .progress-label {
            color: #0f46b7;
        }

        .progress-line.completed {
            background: #2563eb;
        }

        input[type="radio"]:checked + label,
        input[type="checkbox"]:checked + label {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .file-drop.dragging {
            border-color: #2563eb;
            background: #eff6ff;
        }
    </style>
</head>

<body class="min-h-screen text-slate-950 antialiased">
@include('landing.partials.header')

<main class="overflow-hidden">

    <!-- HERO -->
    <section class="relative border-b border-blue-100/70 bg-white/70">
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-blue-50/40"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-5 py-12 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:px-10 lg:py-16">
            <div>
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3.5 py-1.5 text-xs font-extrabold uppercase tracking-[.14em] text-slimme-700">
                    <i data-lucide="wrench" class="h-4 w-4"></i>
                    {{ $s['hero']['badge'] ?? 'Reparatie aanmelden' }}
                </div>

                <h1 class="max-w-3xl text-4xl font-black leading-[1.06] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                    {{ $s['hero']['title1'] ?? 'Meld je reparatie' }}
                    <span class="block text-slimme-600">{{ $s['hero']['title2'] ?? 'eenvoudig aan' }}</span>
                </h1>

                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                    {{ $s['hero']['description'] ?? 'Vertel ons wat er aan de hand is. Wij nemen daarna contact met je op met de volgende stap. Aanmelden duurt ongeveer 2 minuten.' }}
                </p>

                <div class="mt-7 flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-slate-700">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="circle-check" class="h-5 w-5 text-slimme-600"></i>
                        Snelle en duidelijke afhandeling
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="circle-check" class="h-5 w-5 text-slimme-600"></i>
                        Reparatie pas na akkoord
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="circle-check" class="h-5 w-5 text-slimme-600"></i>
                        Jouw data is veilig bij ons
                    </span>
                </div>
            </div>

            <!-- Hero visual -->
            <div class="relative hidden min-h-[330px] lg:block">
                <div class="absolute inset-4 rounded-[42px] bg-gradient-to-br from-blue-100 via-white to-blue-200 blur-2xl"></div>

                <div class="absolute right-0 top-0 w-[72%] rounded-[30px] border border-blue-100 bg-white p-6 shadow-soft">
                    <div class="aspect-[16/10] rounded-[22px] bg-gradient-to-br from-slate-950 via-slimme-900 to-slimme-600 p-6">
                        <div class="flex h-full items-center justify-center">
                            @if (!empty($s['hero']['visual_main']))
                                <img src="{{ asset($s['hero']['visual_main']) }}" alt="Reparatie" class="h-full w-full object-contain">
                            @else
                                <i data-lucide="monitor" class="h-32 w-32 text-blue-200/90"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-0 left-4 w-[61%] rounded-[28px] border border-blue-100 bg-white p-5 shadow-soft">
                    <div class="rounded-[20px] bg-blue-50 p-7">
                        <div class="flex h-full items-center justify-center">
                            @if (!empty($s['hero']['visual_devices']))
                                <img src="{{ asset($s['hero']['visual_devices']) }}" alt="Apparaten" class="max-h-[140px] w-auto object-contain">
                            @else
                                <div class="flex items-center justify-center gap-5">
                                    <i data-lucide="laptop" class="h-28 w-28 text-slimme-700"></i>
                                    <i data-lucide="gamepad-2" class="h-20 w-20 text-slimme-600"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-7 right-1 rounded-2xl border border-blue-100 bg-white px-5 py-4 shadow-card">
                    <div class="flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-xl bg-green-100 text-green-700">
                            <i data-lucide="{{ $s['hero']['badge_icon'] ?? 'shield-check' }}" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $s['hero']['badge_title'] ?? 'Veilig aanmelden' }}</p>
                            <p class="text-xs text-slate-500">{{ $s['hero']['badge_subtitle'] ?? 'Binnen ongeveer 2 minuten' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PAGE -->
    <section class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-10 lg:py-12">

        <!-- PROGRESS -->
        <div id="progressBar" class="mb-7 rounded-[26px] border border-blue-100 bg-white p-5 shadow-card sm:p-6">
            <div class="grid grid-cols-5 items-start gap-1">
                <div class="progress-step active flex min-w-0 items-center" data-progress="1">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="progress-number grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-black text-slate-500 transition">1</div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="progress-label truncate text-sm font-black text-slate-900">Apparaat</p>
                            <p class="truncate text-xs text-slate-500">Kies je apparaat</p>
                        </div>
                    </div>
                </div>

                <div class="progress-step flex min-w-0 items-center" data-progress="2">
                    <div class="progress-line mr-2 h-px flex-1 bg-blue-100 transition"></div>
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="progress-number grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-black text-slate-500 transition">2</div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="progress-label truncate text-sm font-black text-slate-900">Probleem</p>
                            <p class="truncate text-xs text-slate-500">Wat is er mis?</p>
                        </div>
                    </div>
                </div>

                <div class="progress-step flex min-w-0 items-center" data-progress="3">
                    <div class="progress-line mr-2 h-px flex-1 bg-blue-100 transition"></div>
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="progress-number grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-black text-slate-500 transition">3</div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="progress-label truncate text-sm font-black text-slate-900">Apparaat</p>
                            <p class="truncate text-xs text-slate-500">Merk & model</p>
                        </div>
                    </div>
                </div>

                <div class="progress-step flex min-w-0 items-center" data-progress="4">
                    <div class="progress-line mr-2 h-px flex-1 bg-blue-100 transition"></div>
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="progress-number grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-black text-slate-500 transition">4</div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="progress-label truncate text-sm font-black text-slate-900">Contact</p>
                            <p class="truncate text-xs text-slate-500">Jouw gegevens</p>
                        </div>
                    </div>
                </div>

                <div class="progress-step flex min-w-0 items-center" data-progress="5">
                    <div class="progress-line mr-2 h-px flex-1 bg-blue-100 transition"></div>
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="progress-number grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-100 text-sm font-black text-slate-500 transition">5</div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="progress-label truncate text-sm font-black text-slate-900">Controle</p>
                            <p class="truncate text-xs text-slate-500">Controleer & verstuur</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 sm:hidden">
                <p id="mobileProgressTitle" class="text-sm font-black text-slimme-700">Stap 1 van 5 — Apparaat</p>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-blue-100">
                    <div id="mobileProgressFill" class="h-full w-1/5 rounded-full bg-slimme-600 transition-all duration-300"></div>
                </div>
            </div>
        </div>

        <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,1fr)_320px]">

            <!-- WIZARD -->
            <div class="rounded-[30px] border border-blue-100 bg-white shadow-soft">
                <form id="repairForm" action="{{ route('reparatie.submit') }}" method="POST" enctype="multipart/form-data" novalidate class="p-5 sm:p-8">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden">

                    <!-- STEP 1 -->
                    <section class="step-panel active" data-step="1">
                        <div class="mb-7">
                            <span class="text-sm font-extrabold text-slimme-600">Stap 1</span>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">{{ $s['devices']['title'] ?? 'Kies je apparaat' }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $s['devices']['subtitle'] ?? 'Welk apparaat wil je laten repareren?' }}</p>
                        </div>

                        <div id="deviceGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4"></div>

                        <p id="deviceError" class="mt-4 hidden text-sm font-semibold text-red-600">
                            Kies eerst een apparaat.
                        </p>

                        <div class="mt-8 flex justify-end">
                            <button type="button" data-next
                                    class="group inline-flex min-h-[52px] items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-6 text-sm font-extrabold text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700">
                                Volgende stap
                                <i data-lucide="arrow-right" class="h-5 w-5 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 2 -->
                    <section class="step-panel" data-step="2">
                        <div class="mb-7">
                            <span class="text-sm font-extrabold text-slimme-600">Stap 2</span>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Wat is het probleem?</h2>
                            <p id="problemHelp" class="mt-2 text-sm leading-6 text-slate-500"></p>
                        </div>

                        <div id="problemGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3"></div>

                        <p id="problemError" class="mt-4 hidden text-sm font-semibold text-red-600">
                            Kies minimaal één probleem.
                        </p>

                        <div class="mt-6">
                            <label for="description" class="mb-2 block text-sm font-black text-slate-900">
                                Beschrijf het probleem in je eigen woorden
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      placeholder="Bijvoorbeeld: de laptop start wel op, maar het scherm blijft zwart..."
                                      class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-slimme-500 focus:ring-4 focus:ring-blue-100"></textarea>
                            <p id="err-description" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-black text-slate-900">Foto’s toevoegen <span class="font-normal text-slate-400">— optioneel</span></label>
                            <label id="dropZone" for="photos"
                                   class="file-drop flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/60 px-5 py-8 text-center transition hover:border-slimme-500 hover:bg-blue-50">
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-slimme-600 shadow-card">
                                    <i data-lucide="image-up" class="h-6 w-6"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">Klik of sleep foto's hierheen</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Maximaal 5 afbeeldingen · JPG, PNG of WEBP</p>
                                </div>
                            </label>
                            <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
                            <div id="photoPreview" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5"></div>
                            <p id="photoError" class="mt-3 hidden text-sm font-semibold text-red-600"></p>
                        </div>

                        <div class="mt-8 flex items-center justify-between gap-3">
                            <button type="button" data-prev
                                    class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                                Terug
                            </button>

                            <button type="button" data-next
                                    class="group inline-flex min-h-[52px] items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-6 text-sm font-extrabold text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700">
                                Volgende stap
                                <i data-lucide="arrow-right" class="h-5 w-5 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 3 -->
                    <section class="step-panel" data-step="3">
                        <div class="mb-7">
                            <span class="text-sm font-extrabold text-slimme-600">Stap 3</span>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Apparaatgegevens</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Vul alleen de gegevens in die je op het apparaat kunt vinden.
                            </p>
                        </div>

                        <div class="rounded-[24px] border border-blue-100 bg-blue-50/45 p-5">
                            <h3 class="flex items-center gap-2 text-base font-black text-slate-950">
                                <i data-lucide="cpu" class="h-5 w-5 text-slimme-600"></i>
                                Apparaatgegevens
                            </h3>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="brand" class="mb-2 block text-sm font-bold text-slate-800">Merk *</label>
                                    <input id="brand" name="brand" type="text" placeholder="Bijv. HP, Apple, Lenovo"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-brand" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                                <div>
                                    <label for="model" class="mb-2 block text-sm font-bold text-slate-800">Model *</label>
                                    <input id="model" name="model" type="text" placeholder="Bijv. MacBook Pro A2338"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-model" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="serial" class="mb-2 block text-sm font-bold text-slate-800">
                                        Serienummer <span class="font-normal text-slate-400">— optioneel</span>
                                    </label>
                                    <input id="serial" name="serial" type="text" placeholder="Serienummer van het apparaat"
                                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <fieldset class="rounded-[24px] border border-slate-200 p-5">
                                <legend class="px-2 text-sm font-black text-slate-900">
                                    Staan er belangrijke gegevens op?
                                </legend>

                                <div class="mt-3 space-y-2">
                                    <div>
                                        <input id="data_yes" name="data_importance" value="Ja, gegevens behouden" type="radio" class="peer sr-only">
                                        <label for="data_yes" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="grid h-5 w-5 place-items-center rounded-full border border-blue-300 peer-checked:bg-blue-600"></span>
                                            Ja, gegevens moeten behouden blijven
                                        </label>
                                    </div>
                                    <div>
                                        <input id="data_no" name="data_importance" value="Nee" type="radio" class="peer sr-only">
                                        <label for="data_no" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="h-5 w-5 rounded-full border border-blue-300"></span>
                                            Nee
                                        </label>
                                    </div>
                                    <div>
                                        <input id="data_unknown" name="data_importance" value="Weet ik niet" type="radio" class="peer sr-only">
                                        <label for="data_unknown" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="h-5 w-5 rounded-full border border-blue-300"></span>
                                            Weet ik niet
                                        </label>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="rounded-[24px] border border-slate-200 p-5">
                                <legend class="px-2 text-sm font-black text-slate-900">
                                    Eerder geopend of gerepareerd?
                                </legend>

                                <div class="mt-3 space-y-2">
                                    <div>
                                        <input id="opened_yes" name="opened_before" value="Ja" type="radio" class="peer sr-only">
                                        <label for="opened_yes" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="h-5 w-5 rounded-full border border-blue-300"></span>
                                            Ja
                                        </label>
                                    </div>
                                    <div>
                                        <input id="opened_no" name="opened_before" value="Nee" type="radio" class="peer sr-only">
                                        <label for="opened_no" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="h-5 w-5 rounded-full border border-blue-300"></span>
                                            Nee
                                        </label>
                                    </div>
                                    <div>
                                        <input id="opened_unknown" name="opened_before" value="Weet ik niet" type="radio" class="peer sr-only">
                                        <label for="opened_unknown" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold transition hover:bg-blue-50">
                                            <span class="h-5 w-5 rounded-full border border-blue-300"></span>
                                            Weet ik niet
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        
                        <p id="detailsError" class="mt-5 hidden rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"></p>

                        <div class="mt-8 flex items-center justify-between gap-3">
                            <button type="button" data-prev
                                    class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                                Terug
                            </button>

                            <button type="button" data-next
                                    class="group inline-flex min-h-[52px] items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-6 text-sm font-extrabold text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700">
                                Naar contactgegevens
                                <i data-lucide="arrow-right" class="h-5 w-5 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 4 -->
                    <section class="step-panel" data-step="4">
                        <div class="mb-7">
                            <span class="text-sm font-extrabold text-slimme-600">Stap 4</span>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Contact & voorkeur</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Vertel ons hoe we je het beste kunnen bereiken.
                            </p>
                        </div>

<div class="mt-5 rounded-[24px] border border-blue-100 bg-blue-50/45 p-5">
                            <h3 class="flex items-center gap-2 text-base font-black text-slate-950">
                                <i data-lucide="user-round" class="h-5 w-5 text-slimme-600"></i>
                                Contactgegevens
                            </h3>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-2 block text-sm font-bold text-slate-800">Naam *</label>
                                    <input id="name" name="name" type="text" autocomplete="name"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-name" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                                <div>
                                    <label for="email" class="mb-2 block text-sm font-bold text-slate-800">E-mail *</label>
                                    <input id="email" name="email" type="email" autocomplete="email"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-email" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                                <div>
                                    <label for="phone" class="mb-2 block text-sm font-bold text-slate-800">Telefoonnummer *</label>
                                    <input id="phone" name="phone" type="tel" autocomplete="tel"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-phone" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                                <div>
                                    <label for="postcode" class="mb-2 block text-sm font-bold text-slate-800">Postcode *</label>
                                    <input id="postcode" name="postcode" type="text" autocomplete="postal-code"
                                           placeholder="1234 AB"
                                           class="field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm uppercase outline-none transition focus:border-slimme-500 focus:ring-4 focus:ring-blue-100">
                                    <p id="err-postcode" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <fieldset class="rounded-[24px] border border-slate-200 p-5">
                                <legend class="px-2 text-sm font-black text-slate-900">Hoe wil je verder?</legend>

                                <div class="mt-3 space-y-2">
                                    <div>
                                        <input id="delivery_shop" name="delivery_method" value="Naar de winkel brengen" type="radio" class="peer sr-only">
                                        <label for="delivery_shop" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-blue-50">
                                            <i data-lucide="store" class="mt-0.5 h-5 w-5 text-slimme-600"></i>
                                            <span>
                                                <span class="block text-sm font-bold">Ik breng het apparaat naar de winkel</span>
                                                <span class="mt-0.5 block text-xs text-slate-500">Mheenvelden 40D, Apeldoorn</span>
                                            </span>
                                        </label>
                                    </div>

                                    <div>
                                        <input id="delivery_advice" name="delivery_method" value="Eerst telefonisch advies" type="radio" class="peer sr-only">
                                        <label for="delivery_advice" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-blue-50">
                                            <i data-lucide="phone-call" class="mt-0.5 h-5 w-5 text-slimme-600"></i>
                                            <span>
                                                <span class="block text-sm font-bold">Ik wil eerst telefonisch advies</span>
                                                <span class="mt-0.5 block text-xs text-slate-500">We nemen contact met je op.</span>
                                            </span>
                                        </label>
                                    </div>

                                    <div>
                                        <input id="delivery_pickup" name="delivery_method" value="Ophalen / bezorgen" type="radio" class="peer sr-only">
                                        <label for="delivery_pickup" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-blue-50">
                                            <i data-lucide="truck" class="mt-0.5 h-5 w-5 text-slimme-600"></i>
                                            <span>
                                                <span class="block text-sm font-bold">Ophalen / bezorgen aanvragen</span>
                                                <span class="mt-0.5 block text-xs text-slate-500">Beschikbaarheid wordt bevestigd.</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="rounded-[24px] border border-slate-200 p-5">
                                <legend class="px-2 text-sm font-black text-slate-900">Voorkeur voor contact</legend>

                                <div class="mt-3 grid gap-2">
                                    <div>
                                        <input id="contact_whatsapp" name="contact_preference" value="WhatsApp" type="radio" class="peer sr-only">
                                        <label for="contact_whatsapp" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold transition hover:bg-blue-50">
                                            <i data-lucide="message-circle" class="h-5 w-5 text-green-600"></i>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div>
                                        <input id="contact_phone" name="contact_preference" value="Telefoon" type="radio" class="peer sr-only">
                                        <label for="contact_phone" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold transition hover:bg-blue-50">
                                            <i data-lucide="phone" class="h-5 w-5 text-slimme-600"></i>
                                            Telefoon
                                        </label>
                                    </div>
                                    <div>
                                        <input id="contact_email" name="contact_preference" value="E-mail" type="radio" class="peer sr-only">
                                        <label for="contact_email" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold transition hover:bg-blue-50">
                                            <i data-lucide="mail" class="h-5 w-5 text-slimme-600"></i>
                                            E-mail
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <p id="contactError" class="mt-5 hidden rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"></p>

                        <div class="mt-8 flex items-center justify-between gap-3">
                            <button type="button" data-prev
                                    class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                                Terug
                            </button>

                            <button type="button" data-next
                                    class="group inline-flex min-h-[52px] items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-6 text-sm font-extrabold text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700">
                                Controleer aanvraag
                                <i data-lucide="arrow-right" class="h-5 w-5 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 5 -->
                    <section class="step-panel" data-step="5">
                        <div class="mb-7">
                            <span class="text-sm font-extrabold text-slimme-600">Stap 5</span>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Controleer je aanvraag</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Controleer de gegevens voordat je de reparatie aanmeldt.
                            </p>
                        </div>

                        <div id="summary" class="space-y-4"></div>

                        <div class="mt-6">
                            <input id="privacy" name="privacy" type="checkbox" class="peer sr-only">
                            <label for="privacy"
                                   class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm leading-6 transition hover:bg-blue-50">
                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded border border-blue-300 text-white peer-checked:bg-slimme-600">
                                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                </span>
                                <span>
                                    Ik ga akkoord met de
                                    <a href="/privacyverklaring" class="font-bold text-slimme-600 hover:underline">privacyverklaring</a>
                                    en begrijp dat een definitieve prijs vaak pas na diagnose kan worden gegeven.
                                </span>
                            </label>
                        </div>

                        <p id="privacyError" class="mt-3 hidden text-sm font-semibold text-red-600">
                            Je moet akkoord gaan met de privacyverklaring.
                        </p>

                        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="button" data-prev
                                    class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                                Gegevens wijzigen
                            </button>

                            <button id="submitButton" type="submit"
                                    class="group inline-flex min-h-[56px] items-center justify-center gap-3 rounded-2xl bg-slimme-600 px-7 text-sm font-black text-white shadow-blue transition hover:-translate-y-0.5 hover:bg-slimme-700 disabled:cursor-wait disabled:opacity-70">
                                <span>Reparatieaanvraag versturen</span>
                                <i data-lucide="send" class="h-5 w-5 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </section>

                    <!-- SUCCESS -->
                    <section id="successScreen" class="hidden py-5 text-center sm:py-10">
                        <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-green-100 text-green-700">
                            <i data-lucide="badge-check" class="h-11 w-11"></i>
                        </div>

                        <span class="mt-6 inline-flex rounded-full bg-green-50 px-4 py-2 text-xs font-black uppercase tracking-[.14em] text-green-700">
                            Aanmelding ontvangen
                        </span>

                        <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                            Je reparatie is aangemeld
                        </h2>

                        <p class="mt-3 text-slate-500">Bewaar je aanmeldnummer voor verdere communicatie.</p>

                        <div class="mx-auto mt-5 max-w-sm rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Aanmeldnummer</p>
                            <p id="repairNumber" class="mt-1 text-2xl font-black tracking-wide text-slimme-700"></p>
                        </div>

                        <div class="mx-auto mt-8 max-w-2xl rounded-[24px] border border-slate-200 bg-white p-5 text-left">
                            <h3 class="text-base font-black text-slate-950">Wat gebeurt er nu?</h3>
                            <ol class="mt-5 space-y-4">
                                <li class="flex gap-3">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-black text-slimme-700">1</span>
                                    <p class="text-sm leading-6 text-slate-600">Je ontvangt direct een bevestiging per e-mail.</p>
                                </li>
                                <li class="flex gap-3">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-black text-slimme-700">2</span>
                                    <p class="text-sm leading-6 text-slate-600">Breng het apparaat naar Slimme-PC of wacht op ons contact.</p>
                                </li>
                                <li class="flex gap-3">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-black text-slimme-700">3</span>
                                    <p class="text-sm leading-6 text-slate-600">Wij onderzoeken het probleem en sturen advies en een prijsopgave.</p>
                                </li>
                                <li class="flex gap-3">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-black text-slimme-700">4</span>
                                    <p class="text-sm leading-6 text-slate-600">Wij repareren pas nadat jij akkoord hebt gegeven.</p>
                                </li>
                            </ol>
                        </div>

                        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                            <a href="https://maps.google.com/?q=Slimme-PC+Apeldoorn"
                               target="_blank"
                               class="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-2xl bg-slimme-600 px-6 text-sm font-black text-white shadow-blue transition hover:bg-slimme-700">
                                <i data-lucide="map-pin" class="h-5 w-5"></i>
                                Route naar Slimme-PC
                            </a>

                            <button id="downloadButton" type="button"
                                    class="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white px-6 text-sm font-black text-slimme-700 transition hover:bg-blue-50">
                                <i data-lucide="download" class="h-5 w-5"></i>
                                Aanmelding downloaden
                            </button>

                            <button id="resetButton" type="button"
                                    class="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                                <i data-lucide="rotate-ccw" class="h-5 w-5"></i>
                                Nieuwe aanmelding
                            </button>
                        </div>
                    </section>

                </form>
            </div>

            <!-- SIDEBAR -->
            <aside class="space-y-5 lg:sticky lg:top-6">
                <div class="rounded-[28px] border border-blue-100 bg-white p-6 shadow-card">
                    <h2 class="text-xl font-black tracking-tight text-slate-950">Waarom aanmelden?</h2>

                    <div class="mt-6 space-y-5">
                        <div class="flex gap-4">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                                <i data-lucide="clipboard-check" class="h-6 w-6"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Duidelijke registratie</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">We weten direct om welk apparaat en probleem het gaat.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                                <i data-lucide="zap" class="h-6 w-6"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Snellere verwerking</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">Je reparatie komt direct en volledig in ons systeem.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                                <i data-lucide="badge-euro" class="h-6 w-6"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Reparatie pas na akkoord</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">Je ontvangt altijd eerst advies en een prijsopgave.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                                <i data-lucide="lock-keyhole" class="h-6 w-6"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Jouw data is veilig</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">We behandelen jouw apparaat en gegevens met zorg.</p>
                            </div>
                        </div>
                    </div>

                    <div class="my-6 h-px bg-slate-200"></div>

                    <h3 class="text-base font-black text-slate-950">Liever direct contact?</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Bel of stuur ons een WhatsApp-bericht.</p>

                    <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <a href="https://wa.me/31552032145"
                           class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-green-300 bg-green-50 px-3 text-xs font-black text-green-700 transition hover:bg-green-100">
                            <i data-lucide="message-circle" class="h-4 w-4"></i>
                            WhatsApp
                        </a>
                        <a href="tel:+31552032145"
                           class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 text-xs font-black text-slimme-700 transition hover:bg-blue-100">
                            <i data-lucide="phone" class="h-4 w-4"></i>
                            055 203 21 45
                        </a>
                    </div>
                </div>

                <div class="rounded-[28px] bg-gradient-to-br from-slimme-900 to-slimme-700 p-6 text-white shadow-blue">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/10">
                            <i data-lucide="headphones" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black">Vragen? We helpen je graag.</h3>
                            <p class="mt-1 text-xs leading-5 text-blue-100">
                                Neem gerust contact met ons op wanneer je niet zeker weet wat je moet kiezen.
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <!-- TRUST STRIP -->
        <div class="mt-8 grid gap-4 rounded-[28px] border border-blue-100 bg-white p-5 shadow-card sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                    <i data-lucide="store" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900">Breng of stuur op</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Afgeven in Apeldoorn of veilig opsturen.</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                    <i data-lucide="search-check" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900">Duidelijke diagnose</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Je ontvangt vooraf duidelijkheid over eventuele diagnosekosten.</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                    <i data-lucide="badge-euro" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900">Prijsopgave vooraf</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Geen reparatie zonder jouw akkoord.</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-50 text-slimme-600">
                    <i data-lucide="wrench" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900">Reparatie & testen</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Na reparatie wordt alles grondig getest.</p>
                </div>
            </div>
        </div>

        <!-- PROCESS -->
        <section class="mt-8 rounded-[30px] border border-blue-100 bg-white p-6 shadow-card sm:p-8">
            <div class="max-w-2xl">
                <span class="text-sm font-extrabold text-slimme-600">Van aanmelding tot klaar</span>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Hoe werkt het?</h2>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <div class="relative text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-blue-50 text-slimme-600">
                        <i data-lucide="file-pen-line" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-3 text-sm font-black">Aanmelden</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Meld je reparatie aan via het formulier.</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-blue-50 text-slimme-600">
                        <i data-lucide="search" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-3 text-sm font-black">Diagnose</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">We onderzoeken het apparaat en stellen het probleem vast.</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-blue-50 text-slimme-600">
                        <i data-lucide="clipboard-list" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-3 text-sm font-black">Advies & prijs</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Je ontvangt advies en een duidelijke prijsopgave.</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-blue-50 text-slimme-600">
                        <i data-lucide="wrench" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-3 text-sm font-black">Reparatie</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Na akkoord repareren we met kwaliteitsonderdelen.</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-green-50 text-green-700">
                        <i data-lucide="circle-check-big" class="h-7 w-7"></i>
                    </div>
                    <h3 class="mt-3 text-sm font-black">Klaar</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Je apparaat wordt getest en is klaar voor gebruik.</p>
                </div>
            </div>
        </section>

    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.__lucideRefresh();

        const _cmsDevices = @json($s['devices']['items'] ?? []);
        const devices = Array.isArray(_cmsDevices) && _cmsDevices.length ? _cmsDevices : [
            { id: 'laptop', label: 'Laptop', icon: 'laptop' },
            { id: 'mac', label: 'MacBook / iMac', icon: 'monitor' },
            { id: 'desktop', label: 'Desktop PC', icon: 'pc-case' },
            { id: 'console', label: 'PlayStation / Xbox', icon: 'gamepad-2' },
            { id: 'tablet', label: 'iPad / Tablet', icon: 'tablet' },
            { id: 'storage', label: 'HDD / SSD', icon: 'hard-drive' },
            { id: 'network', label: 'Printer / Netwerk', icon: 'router' },
            { id: 'other', label: 'Anders', icon: 'ellipsis' }
        ];

        const problemOptions = {
            laptop: ['Start niet', 'Geen beeld', 'Laadt niet', 'Wordt warm', 'Scherm kapot', 'Batterijprobleem', 'Vloeistofschade', 'Traag', 'Anders'],
            mac: ['Start niet', 'Geen beeld', 'Laadt niet', 'Wordt warm', 'Scherm kapot', 'Batterijprobleem', 'Vloeistofschade', 'Traag', 'Anders'],
            desktop: ['Start niet', 'Geen beeld', 'Valt uit', 'Wordt warm', 'Maakt geluid', 'Windows probleem', 'Traag', 'Upgrade nodig', 'Anders'],
            console: ['Geen beeld / HDMI', 'Wordt heet', 'Valt uit', 'Start niet', 'Ventilator', 'Controller / USB', 'Disc probleem', 'Netwerkprobleem', 'Anders'],
            tablet: ['Scherm kapot', 'Laadt niet', 'Start niet', 'Batterijprobleem', 'Waterschade', 'Knoppen defect', 'Softwareprobleem', 'Traag', 'Anders'],
            storage: ['Niet herkend', 'Data kwijt', 'Maakt geluid', 'Per ongeluk gewist', 'Beschadigd', 'Bestanden openen niet', 'Anders'],
            network: ['Geen internet', 'Instabiele verbinding', 'Printer werkt niet', 'WiFi bereik slecht', 'Installatie nodig', 'Netwerk beveiligen', 'Anders'],
            other: ['Start niet', 'Werkt niet goed', 'Beschadigd', 'Softwareprobleem', 'Onderhoud nodig', 'Anders']
        };

        const state = {
            currentStep: 1,
            device: null,
            problems: [],
            files: [],
            repairNumber: null
        };

        const deviceGrid = document.getElementById('deviceGrid');
        const problemGrid = document.getElementById('problemGrid');
        const problemHelp = document.getElementById('problemHelp');
        const panels = [...document.querySelectorAll('.step-panel')];
        const form = document.getElementById('repairForm');
        const progressBar = document.getElementById('progressBar');

        function renderDevices() {
            deviceGrid.innerHTML = devices.map(device => `
                <button type="button"
                        class="choice-card relative min-h-[150px] rounded-[22px] border border-slate-200 bg-white p-4 text-center transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-card"
                        data-device="${device.id}">
                    <span class="select-check absolute right-3 top-3 grid h-7 w-7 place-items-center rounded-full bg-slimme-600 text-white shadow">
                        <i data-lucide="check" class="h-4 w-4"></i>
                    </span>

                    <span class="mx-auto grid h-20 w-20 place-items-center rounded-[22px] bg-gradient-to-br from-blue-50 to-blue-100 text-slimme-700">
                        <i data-lucide="${device.icon}" class="h-10 w-10"></i>
                    </span>

                    <span class="mt-4 block text-sm font-black text-slate-900">${device.label}</span>
                </button>
            `).join('');

            window.__lucideRefresh();

            document.querySelectorAll('[data-device]').forEach(button => {
                button.addEventListener('click', () => {
                    state.device = button.dataset.device;
                    state.problems = [];

                    document.querySelectorAll('[data-device]').forEach(card => card.classList.remove('selected'));
                    button.classList.add('selected');
                    document.getElementById('deviceError').classList.add('hidden');
                    renderProblems();
                });
            });
        }

        function renderProblems() {
            const selectedDevice = devices.find(device => device.id === state.device);
            const options = problemOptions[state.device] || problemOptions.other;

            problemHelp.textContent = `Kies wat het beste past bij jouw ${selectedDevice?.label || 'apparaat'}. Je kunt meerdere opties kiezen.`;

            problemGrid.innerHTML = options.map((problem, index) => `
                <button type="button"
                        class="problem-card relative min-h-[92px] rounded-2xl border border-slate-200 bg-white px-4 py-4 text-left transition duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50"
                        data-problem="${problem}">
                    <span class="select-check absolute right-3 top-3 grid h-6 w-6 place-items-center rounded-full bg-slimme-600 text-white">
                        <i data-lucide="check" class="h-3.5 w-3.5"></i>
                    </span>

                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-blue-50 text-slimme-600">
                        <i data-lucide="${problemIcon(problem)}" class="h-5 w-5"></i>
                    </span>

                    <span class="mt-3 block pr-5 text-sm font-black text-slate-900">${problem}</span>
                </button>
            `).join('');

            window.__lucideRefresh();

            document.querySelectorAll('[data-problem]').forEach(button => {
                button.addEventListener('click', () => {
                    const problem = button.dataset.problem;
                    button.classList.toggle('selected');

                    if (state.problems.includes(problem)) {
                        state.problems = state.problems.filter(item => item !== problem);
                    } else {
                        state.problems.push(problem);
                    }

                    document.getElementById('problemError').classList.add('hidden');
                });
            });
        }

        function problemIcon(problem) {
            const text = problem.toLowerCase();
            if (text.includes('beeld') || text.includes('scherm')) return 'monitor-x';
            if (text.includes('laadt') || text.includes('batterij')) return 'battery-warning';
            if (text.includes('warm') || text.includes('heet')) return 'thermometer-sun';
            if (text.includes('water') || text.includes('vloeistof')) return 'droplets';
            if (text.includes('traag')) return 'gauge';
            if (text.includes('start')) return 'power';
            if (text.includes('hdmi') || text.includes('usb')) return 'cable';
            if (text.includes('ventilator') || text.includes('geluid')) return 'fan';
            if (text.includes('data') || text.includes('bestanden')) return 'database';
            if (text.includes('internet') || text.includes('wifi') || text.includes('netwerk')) return 'wifi';
            if (text.includes('printer')) return 'printer';
            if (text.includes('software') || text.includes('windows')) return 'app-window';
            if (text.includes('upgrade')) return 'rocket';
            return 'circle-help';
        }

        function goToStep(step) {
            state.currentStep = step;

            panels.forEach(panel => {
                panel.classList.toggle('active', Number(panel.dataset.step) === step);
            });

            updateProgress();
            progressBar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            window.__lucideRefresh();
        }

        function updateProgress() {
            document.querySelectorAll('[data-progress]').forEach(item => {
                const step = Number(item.dataset.progress);
                item.classList.toggle('active', step === state.currentStep);
                item.classList.toggle('completed', step < state.currentStep);

                const line = item.querySelector('.progress-line');
                if (line) line.classList.toggle('completed', step <= state.currentStep);
            });

            const titles = ['Apparaat', 'Probleem', 'Apparaatgegevens', 'Contact', 'Controle'];
            document.getElementById('mobileProgressTitle').textContent =
                `Stap ${state.currentStep} van 5 — ${titles[state.currentStep - 1]}`;
            document.getElementById('mobileProgressFill').style.width = `${state.currentStep * 20}%`;
        }

        function validateStep(step) {
            if (step === 1 && !state.device) {
                document.getElementById('deviceError').classList.remove('hidden');
                return false;
            }

            if (step === 2) {
                if (state.problems.length === 0) {
                    document.getElementById('problemError').classList.remove('hidden');
                    return false;
                }

                const desc = document.getElementById('description');
                const descErr = document.getElementById('err-description');
                desc.classList.remove('border-red-400', 'ring-4', 'ring-red-100');
                descErr.classList.add('hidden');
                if (!desc.value.trim()) {
                    desc.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    descErr.textContent = 'Vul een beschrijving in van het probleem.';
                    descErr.classList.remove('hidden');
                    return false;
                }
            }

            if (step === 3) {
                const deviceFields = [
                    document.getElementById('brand'),
                    document.getElementById('model')
                ];
                let valid = true;

                deviceFields.forEach(field => {
                    field.classList.remove('border-red-400', 'ring-4', 'ring-red-100');
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    }
                });

                const data = document.querySelector('input[name="data_importance"]:checked');
                const opened = document.querySelector('input[name="opened_before"]:checked');
                if (!data || !opened) valid = false;

                const error = document.getElementById('detailsError');
                if (!valid) {
                    error.textContent = 'Vul merk en model in en maak bij beide vragen een keuze.';
                    error.classList.remove('hidden');
                    return false;
                }
                error.classList.add('hidden');
            }

            if (step === 4) {
                const contactFields = [
                    document.getElementById('name'),
                    document.getElementById('email'),
                    document.getElementById('phone'),
                    document.getElementById('postcode')
                ];
                let valid = true;
                let message = '';

                contactFields.forEach(field => {
                    field.classList.remove('border-red-400', 'ring-4', 'ring-red-100');
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    }
                });

                const email = document.getElementById('email');
                if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                    valid = false;
                    email.classList.add('border-red-400', 'ring-4', 'ring-red-100');
                    message = 'Vul een geldig e-mailadres in.';
                }

                const delivery = document.querySelector('input[name="delivery_method"]:checked');
                const contact = document.querySelector('input[name="contact_preference"]:checked');
                if (!delivery || !contact) valid = false;

                const error = document.getElementById('contactError');
                if (!valid) {
                    error.textContent = message || 'Vul alle verplichte contactgegevens in en maak beide keuzes.';
                    error.classList.remove('hidden');
                    return false;
                }
                error.classList.add('hidden');
            }

            return true;
        }

        document.querySelectorAll('[data-next]').forEach(button => {
            button.addEventListener('click', () => {
                if (!validateStep(state.currentStep)) return;

                if (state.currentStep === 4) buildSummary();
                goToStep(Math.min(5, state.currentStep + 1));
            });
        });

        document.querySelectorAll('[data-prev]').forEach(button => {
            button.addEventListener('click', () => {
                goToStep(Math.max(1, state.currentStep - 1));
            });
        });

        function getRadioValue(name) {
            return document.querySelector(`input[name="${name}"]:checked`)?.value || 'Niet ingevuld';
        }

        function buildSummary() {
            const selectedDevice = devices.find(device => device.id === state.device)?.label || '';
            const sections = [
                {
                    icon: 'cpu',
                    title: 'Apparaat',
                    items: [
                        ['Type', selectedDevice],
                        ['Merk', document.getElementById('brand').value],
                        ['Model', document.getElementById('model').value],
                        ['Serienummer', document.getElementById('serial').value || 'Niet opgegeven']
                    ]
                },
                {
                    icon: 'triangle-alert',
                    title: 'Probleem',
                    items: [
                        ['Geselecteerd', state.problems.join(', ')],
                        ['Omschrijving', document.getElementById('description').value || 'Geen extra omschrijving'],
                        ['Foto’s', `${state.files.length} toegevoegd`]
                    ]
                },
                {
                    icon: 'database',
                    title: 'Data & historie',
                    items: [
                        ['Belangrijke gegevens', getRadioValue('data_importance')],
                        ['Eerder geopend', getRadioValue('opened_before')]
                    ]
                },
                {
                    icon: 'user-round',
                    title: 'Contact & voorkeur',
                    items: [
                        ['Naam', document.getElementById('name').value],
                        ['E-mail', document.getElementById('email').value],
                        ['Telefoon', document.getElementById('phone').value],
                        ['Postcode', document.getElementById('postcode').value],
                        ['Vervolg', getRadioValue('delivery_method')],
                        ['Contactvoorkeur', getRadioValue('contact_preference')]
                    ]
                }
            ];

            document.getElementById('summary').innerHTML = sections.map(section => `
                <div class="overflow-hidden rounded-[22px] border border-slate-200">
                    <div class="flex items-center gap-3 border-b border-slate-200 bg-blue-50/70 px-5 py-4">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-white text-slimme-600 shadow-sm">
                            <i data-lucide="${section.icon}" class="h-5 w-5"></i>
                        </span>
                        <h3 class="text-sm font-black text-slate-950">${section.title}</h3>
                    </div>

                    <dl class="divide-y divide-slate-100 px-5">
                        ${section.items.map(([label, value]) => `
                            <div class="grid gap-1 py-3 sm:grid-cols-[165px_1fr] sm:gap-5">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">${label}</dt>
                                <dd class="break-words text-sm font-semibold leading-6 text-slate-700">${escapeHtml(value)}</dd>
                            </div>
                        `).join('')}
                    </dl>
                </div>
            `).join('');

            window.__lucideRefresh();
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // Uploads
        const photosInput = document.getElementById('photos');
        const preview = document.getElementById('photoPreview');
        const dropZone = document.getElementById('dropZone');
        const photoError = document.getElementById('photoError');

        function processFiles(fileList) {
            const accepted = [...fileList].filter(file => ['image/jpeg', 'image/png', 'image/webp'].includes(file.type));
            const combined = [...state.files, ...accepted];

            if (combined.length > 5) {
                photoError.textContent = 'Je kunt maximaal 5 afbeeldingen toevoegen.';
                photoError.classList.remove('hidden');
                return;
            }

            state.files = combined;
            photoError.classList.add('hidden');
            renderFilePreview();
        }

        photosInput.addEventListener('change', event => {
            processFiles(event.target.files);
            photosInput.value = '';
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, event => {
                event.preventDefault();
                dropZone.classList.add('dragging');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, event => {
                event.preventDefault();
                dropZone.classList.remove('dragging');
            });
        });

        dropZone.addEventListener('drop', event => processFiles(event.dataTransfer.files));

        function renderFilePreview() {
            preview.innerHTML = '';

            state.files.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = event => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50';
                    wrapper.innerHTML = `
                        <img src="${event.target.result}" alt="" class="aspect-square w-full object-cover">
                        <button type="button"
                                data-remove-file="${index}"
                                class="absolute right-1.5 top-1.5 grid h-7 w-7 place-items-center rounded-full bg-slate-950/75 text-white backdrop-blur transition hover:bg-red-600"
                                aria-label="Foto verwijderen">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    `;
                    preview.appendChild(wrapper);
                    window.__lucideRefresh();

                    wrapper.querySelector('[data-remove-file]').addEventListener('click', () => {
                        state.files.splice(index, 1);
                        renderFilePreview();
                    });
                };

                reader.readAsDataURL(file);
            });
        }

        // Submit via Laravel backend
        form.addEventListener('submit', event => {
            event.preventDefault();

            clearServerErrors();

            if (!document.getElementById('privacy').checked) {
                document.getElementById('privacyError').classList.remove('hidden');
                goToStep(5);
                return;
            }
            document.getElementById('privacyError').classList.add('hidden');

            for (let s = 1; s <= 4; s++) {
                if (!validateStep(s)) {
                    goToStep(s);
                    return;
                }
            }

            const submitButton = document.getElementById('submitButton');
            const originalLabel = submitButton.querySelector('span').textContent;
            submitButton.disabled = true;
            submitButton.querySelector('span').textContent = 'Aanvraag verzenden...';

            const fd = new FormData(form);
            fd.delete('photos[]');
            if (state.device) fd.set('device', state.device);
            state.problems.forEach(p => fd.append('problems[]', p));
            state.files.forEach(f => fd.append('photos[]', f));

            fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
                .then(async response => {
                    if (response.ok) return response.json();
                    if (response.status === 422) {
                        const data = await response.json();
                        showServerErrors(data.errors || {});
                        throw new Error('validation');
                    }
                    throw new Error('server');
                })
                .then(data => {
                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.success('Bedankt! Uw aanvraag is succesvol verzonden.');
                    }
                    document.getElementById('repairNumber').textContent = data.repair_number || state.repairNumber;
                    panels.forEach(p => p.classList.remove('active'));
                    document.getElementById('successScreen').classList.remove('hidden');
                    progressBar.classList.add('hidden');
                    const aside = document.querySelector('aside');
                    if (aside) aside.classList.add('hidden');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    window.__lucideRefresh();
                })
                .catch(err => {
                    if (err.message !== 'validation') {
                        if (window.SlimmePC && window.SlimmePC.toast) {
                            window.SlimmePC.toast.error('Er ging iets mis bij het verzenden. Probeer het opnieuw.');
                        } else {
                            alert('Er ging iets mis bij het verzenden. Probeer het opnieuw.');
                        }
                    }
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.querySelector('span').textContent = originalLabel;
                });
        });

        function clearServerErrors() {
            ['deviceError','problemError','photoError','detailsError','contactError','privacyError','err-brand','err-model','err-name','err-email','err-phone','err-postcode','err-description']
                .forEach(id => { const el = document.getElementById(id); if (el) el.classList.add('hidden'); });
            ['brand','model','name','email','phone','postcode']
                .forEach(id => { const el = document.getElementById(id); if (el) el.classList.remove('border-red-400','ring-4','ring-red-100'); });
        }

        function showServerErrors(errors) {
            const map = {
                device: 'deviceError',
                problems: 'problemError',
                description: 'err-description',
                photos: 'photoError',
                brand: 'err-brand',
                model: 'err-model',
                serial: 'err-model',
                data_importance: 'detailsError',
                opened_before: 'detailsError',
                name: 'err-name',
                email: 'err-email',
                phone: 'err-phone',
                postcode: 'err-postcode',
                delivery_method: 'contactError',
                contact_preference: 'contactError',
                privacy: 'privacyError'
            };
            const stepFor = { device:1, problems:2, description:2, photos:2, brand:3, model:3, serial:3, data_importance:3, opened_before:3, name:4, email:4, phone:4, postcode:4, delivery_method:4, contact_preference:4, privacy:5 };
            let earliest = 99;

            Object.keys(errors).forEach(key => {
                const flat = key.replace(/\.\d+$/, '').replace(/\[\]$/, '');
                const elId = map[flat] || map[key];
                const msgs = errors[key];
                const message = Array.isArray(msgs) ? msgs[0] : msgs;

                if (elId) {
                    const el = document.getElementById(elId);
                    if (el) { el.textContent = message; el.classList.remove('hidden'); }
                }
                if (['brand','model','name','email','phone','postcode'].includes(flat)) {
                    const inp = document.getElementById(flat);
                    if (inp) inp.classList.add('border-red-400','ring-4','ring-red-100');
                }
                if (stepFor[flat] && stepFor[flat] < earliest) earliest = stepFor[flat];
            });

            if (earliest < 99) goToStep(earliest);
        }

        function createRepairNumber() {
            const year = new Date().getFullYear();
            const random = String(Math.floor(Math.random() * 90000) + 10000);
            return `SP-${year}-${random}`;
        }

        document.getElementById('downloadButton').addEventListener('click', () => {
            const selectedDevice = devices.find(device => device.id === state.device)?.label || '';
            const content = [
                'SLIMME-PC REPARATIEAANMELDING',
                '--------------------------------',
                `Aanmeldnummer: ${state.repairNumber}`,
                `Datum: ${new Date().toLocaleString('nl-NL')}`,
                '',
                `Apparaat: ${selectedDevice}`,
                `Merk: ${document.getElementById('brand').value}`,
                `Model: ${document.getElementById('model').value}`,
                `Serienummer: ${document.getElementById('serial').value || 'Niet opgegeven'}`,
                `Probleem: ${state.problems.join(', ')}`,
                `Omschrijving: ${document.getElementById('description').value || 'Geen extra omschrijving'}`,
                `Data behouden: ${getRadioValue('data_importance')}`,
                `Eerder geopend: ${getRadioValue('opened_before')}`,
                '',
                `Naam: ${document.getElementById('name').value}`,
                `E-mail: ${document.getElementById('email').value}`,
                `Telefoon: ${document.getElementById('phone').value}`,
                `Postcode: ${document.getElementById('postcode').value}`,
                `Vervolg: ${getRadioValue('delivery_method')}`,
                `Contactvoorkeur: ${getRadioValue('contact_preference')}`
            ].join('\n');

            const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${state.repairNumber}.txt`;
            link.click();
            URL.revokeObjectURL(url);
        });

        document.getElementById('resetButton').addEventListener('click', () => {
            window.location.reload();
        });

        renderDevices();
        updateProgress();
    });
</script>

@include('landing.partials.footer')

<script src="{{ asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/design.js') }}?v={{ filemtime(public_path('assets/js/design.js')) }}"></script>
<script src="{{ asset('assets/js/landing.js') }}?v={{ filemtime(public_path('assets/js/landing.js')) }}"></script>
</body>
</html>

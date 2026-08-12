<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Beheer' }} | {{ config('app.name', 'Slimme-PC') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        {{-- Page loader styles --}}
        <style>
            #admin-loader { position: fixed; inset: 0; z-index: 9999; pointer-events: none; }
            #admin-loader .loader-top-bar { position: fixed; top: 0; left: 0; right: 0; height: 3px; overflow: hidden; background: rgba(7,91,232,.12); opacity: 0; transition: opacity .2s ease; }
            #admin-loader.loader-visible .loader-top-bar { opacity: 1; }
            #admin-loader .loader-top-bar::after { content: ''; display: block; height: 100%; width: 40%; border-radius: 0 9999px 9999px 0; background: linear-gradient(90deg, #075be8, #3b82f6, #84cc16); animation: loader-slide 1.1s ease-in-out infinite; }
            #admin-loader .loader-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--c-page, #f8fafc) 72%, transparent); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); opacity: 0; transition: opacity .25s ease; }
            #admin-loader.loader-visible { pointer-events: auto; }
            #admin-loader.loader-visible .loader-overlay { opacity: 1; }
            #admin-loader .loader-card { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 30px 46px; border-radius: 24px; background: var(--c-card, #fff); border: 1px solid rgba(148,163,184,.3); box-shadow: 0 25px 70px -20px rgba(15,23,42,.28); opacity: 0; transform: translateY(6px) scale(.97); transition: opacity .2s ease, transform .25s ease; }
            #admin-loader.loader-visible .loader-card { opacity: 1; transform: none; }
            #admin-loader .loader-logo { height: 46px; width: auto; object-fit: contain; }
            #admin-loader .loader-spinner { width: 44px; height: 44px; border-radius: 50%; border: 3px solid rgba(7,91,232,.18); border-top-color: #075be8; animation: loader-spin .7s linear infinite; }
            #admin-loader .loader-text { margin: 0; font-size: 13px; font-weight: 600; letter-spacing: .02em; color: var(--c-muted, #64748b); }
            @keyframes loader-spin { to { transform: rotate(360deg); } }
            @keyframes loader-slide { from { transform: translateX(-130%); } to { transform: translateX(330%); } }
            @media (prefers-reduced-motion: reduce) { #admin-loader * { animation: none !important; } }
        </style>

        {{-- Scripts / Styles --}}
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        <script src="{{ asset('assets/js/vendor/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/axios.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/alpine.min.js') }}"></script>
        <script src="{{ asset('assets/js/design.js') }}"></script>
        <script src="{{ asset('assets/js/admin/klanten.js') }}"></script>
        <script src="{{ asset('assets/js/admin/content.js') }}?v={{ filemtime(public_path('assets/js/admin/content.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/loader.js') }}?v={{ filemtime(public_path('assets/js/admin/loader.js')) }}"></script>
    </head>

    <body class="font-sans antialiased" style="background-color: var(--c-page)" x-data="{ sidebarOpen: false }">
        {{-- Page loader (navigation between pages) --}}
        <div id="admin-loader" aria-hidden="true">
            <div class="loader-top-bar"></div>
            <div class="loader-overlay">
                <div class="loader-card">
                    <img src="{{ asset(\App\Support\Cms::page('home')['header']['logo_image'] ?? 'assets/img/logo.webp') }}" alt="" class="loader-logo">
                    <span class="loader-spinner"></span>
                    <p class="loader-text">Laden...</p>
                </div>
            </div>
        </div>

        <script>
            (function () {
                var KEY = 'adminPageNav';
                var loader = document.getElementById('admin-loader');
                var flag = false;
                try {
                    flag = sessionStorage.getItem(KEY) === '1';
                    sessionStorage.removeItem(KEY);
                } catch (e) {}
                if (!flag || !loader) return;

                loader.classList.add('loader-visible');

                var done = false;
                function finish() {
                    if (done) return;
                    done = true;
                    loader.classList.remove('loader-visible');
                }
                if (document.readyState === 'complete') {
                    setTimeout(finish, 200);
                } else {
                    window.addEventListener('load', function () {
                        setTimeout(finish, 150);
                    });
                }
                setTimeout(finish, 6000);
            })();
        </script>
        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-[#0b1638] transition-transform duration-300 lg:translate-x-0"
        >
            {{-- Logo --}}
            <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset(\App\Support\Cms::page('home')['header']['logo_image'] ?? 'assets/img/logo.webp') }}" alt="Slimme-PC" class="h-10 w-auto object-contain">
                    <span class="text-base font-extrabold tracking-tight text-white">
                        Slimme-PC <span class="block text-[11px] font-semibold uppercase tracking-widest text-blue-400">Beheer</span>
                    </span>
                </a>

                <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="Menu sluiten">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">
                <p class="mb-3 px-4 text-[11px] font-bold uppercase tracking-widest text-slate-500">Overzicht</p>

                <x-admin.sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.13 1.13 0 0 1 1.592 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                    </x-slot>
                    🧭 Dashboard
                </x-admin.sidebar-link>

                <p class="mb-3 mt-8 px-4 text-[11px] font-bold uppercase tracking-widest text-slate-500">Beheer</p>

                {{-- 1. Home-page Dropdown (Split CMS) --}}
                <div x-data="{ open: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>🏠 Home-page</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs">
                        <a href="{{ route('admin.content.design.edit') }}" 
                           class="block py-1.5 hover:text-white transition {{ request()->routeIs('admin.content.design.edit') ? 'text-white font-bold' : 'text-slate-400' }}">
                            Ontwerp &amp; SEO
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'header']) }}" 
                           class="block py-1.5 hover:text-white transition {{ request()->routeIs('admin.content.section.edit') && request()->route('section') === 'header' ? 'text-white font-bold' : 'text-slate-400' }}">
                            Header
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'hero']) }}" 
                           class="block py-1.5 hover:text-white transition {{ request()->routeIs('admin.content.section.edit') && request()->route('section') === 'hero' ? 'text-white font-bold' : 'text-slate-400' }}">
                            Hero
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'why']) }}" 
                           class="block py-1.5 hover:text-white transition {{ request()->routeIs('admin.content.section.edit') && request()->route('section') === 'why' ? 'text-white font-bold' : 'text-slate-400' }}">
                            Waarom voor ons kiezen
                        </a>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Services <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Webshop <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Footer <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                    </div>
                </div>

                {{-- 2. Diensten Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>🛠️ Diensten</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Hardware-afspraak <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Hardware afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Software-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Dataherstelen-afdeeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Website-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Netwerk-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Pcbouwen-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 3. Pages Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>📄 Pages</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Hardware-photos <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 4. Webshop Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>🛒 Webshop</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Category-producten <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Producten <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">SliderImage <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Licentiecodes <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 5. Website-aanvragen Link --}}
                <span class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-medium text-slate-500 cursor-not-allowed">
                    <span>🌐 Website-aanvragen</span>
                    <span class="rounded-full bg-white/5 px-2 py-0.5 text-[9px] font-bold text-slate-500">Binnenkort</span>
                </span>

                {{-- 6. Contact-klanten Link --}}
                <span class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-medium text-slate-500 cursor-not-allowed">
                    <span>📬 Contact-klanten</span>
                    <span class="rounded-full bg-white/5 px-2 py-0.5 text-[9px] font-bold text-slate-500">Binnenkort</span>
                </span>

                {{-- 7. Bestellings Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>📝 Bestellings</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Bestellings <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Facturen <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 8. Afspraken Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>📅 Afspraken</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Algemeen afspraken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Facturen van afspraken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 9. Abonnement Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>📄 Abonnement</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Klanten-lijst <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Abonnement-beheren <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 10. Prijs-instellingen Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>🧰 Prijs-instellingen</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Prijs-beheren <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 11. Users-beheren Dropdown (Active: Klanten) --}}
                <div x-data="{ open: {{ request()->routeIs('admin.klanten.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>👥 Users-beheren</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs">
                        <a href="{{ route('admin.klanten.index') }}" 
                           class="block py-1.5 hover:text-white transition {{ request()->routeIs('admin.klanten.*') ? 'text-white font-bold' : 'text-slate-400' }}">
                            Klanten
                        </a>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Send Emails <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Klantenlijst <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Popup Melding <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Algemene Voorwaarden <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block py-1.5 text-slate-500 cursor-not-allowed">
                            Privacybeleid <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                    </div>
                </div>

                {{-- 12. Manual Invoices Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>📄 Manual-invoices</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Hardware <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Bevestging-mail <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 13. Live Chat Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>💬 Live-chat</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Vragen-antwoorden <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block py-1.5 cursor-not-allowed">Inbox-Live <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 14. Database site Dropdown --}}
                <div x-data="{ open: false }">
                    <button type="button" @click="open = !open" 
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(203,213,225,0.85)">
                        <span class="flex items-center gap-3">
                            <span>💾 Database site</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="pl-11 pr-2 py-1 space-y-1.5 text-xs text-slate-500">
                        <span class="block py-1.5 cursor-not-allowed">Backup-maken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>
            </nav>
            </nav>

            {{-- Sidebar footer --}}
            <div class="border-t border-white/10 p-4">
                <form method="POST" action="{{ route('logout') }}" data-loading>
                    @csrf
                    <button type="submit" data-loading class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-400 transition duration-200 hover:bg-red-500/10 hover:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Uitloggen
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex min-h-screen flex-col lg:pl-72">
            {{-- Topbar --}}
            <header class="sticky top-0 z-20 border-b backdrop-blur-xl"
                    style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2)">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                                class="rounded-lg p-2 transition hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                                aria-label="Menu openen">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6" style="color: var(--c-heading)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <h1 class="text-lg font-bold tracking-tight sm:text-xl" style="color: var(--c-heading)">
                            {{ $title ?? 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-theme-toggle class="border-slate-200 bg-white/80 shadow-sm dark:border-slate-700 dark:bg-slate-900/80" />

                        {{-- User dropdown --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open"
                                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/80 py-1.5 pe-3 ps-1.5 shadow-sm transition hover:border-blue-300 dark:border-slate-700 dark:bg-slate-900/80 dark:hover:border-blue-500/50">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-[#075be8] to-[#064bd7] text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden text-sm font-semibold sm:block" style="color: var(--c-heading)">
                                    {{ Auth::user()->name }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" style="color: var(--c-muted)">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute end-0 z-50 mt-2 w-56 rounded-2xl border py-2 shadow-lg"
                                 style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.25)">
                                <div class="border-b px-4 py-2" style="border-color: rgba(148, 163, 184, 0.15)">
                                    <p class="truncate text-sm font-bold" style="color: var(--c-heading)">{{ Auth::user()->name }}</p>
                                    <p class="truncate text-xs" style="color: var(--c-muted)">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="mt-1 block px-4 py-2 text-sm transition hover:bg-slate-100 dark:hover:bg-slate-800" style="color: var(--c-heading)">
                                    Profiel
                                </a>

                                <form method="POST" action="{{ route('logout') }}" data-loading>
                                    @csrf
                                    <button type="submit" data-loading class="block w-full px-4 py-2 text-start text-sm text-red-500 transition hover:bg-red-50 dark:hover:bg-red-950/40">
                                        Uitloggen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="px-4 pb-6 sm:px-6 lg:px-8">
                <p class="text-center text-xs" style="color: var(--c-muted)">
                    &copy; {{ date('Y') }} Slimme-PC Beheer. Alle rechten voorbehouden.
                </p>
            </footer>
        </div>
    </body>
</html>

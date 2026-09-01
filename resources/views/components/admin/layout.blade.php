<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Beheer' }} | {{ config('app.name', 'Slimme-PC') }}</title>

        <link rel="icon" href="{{ asset(\App\Support\Cms::page('home')['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}">

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
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
        <script src="{{ asset('assets/js/vendor/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/axios.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/alpine.min.js') }}"></script>
        <script src="{{ asset('assets/js/admin/table-loading.js') }}?v={{ filemtime(public_path('assets/js/admin/table-loading.js')) }}"></script>
        <script src="{{ asset('assets/js/design.js') }}?v={{ filemtime(public_path('assets/js/design.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/klanten.js') }}?v={{ filemtime(public_path('assets/js/admin/klanten.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/contact-inbox.js') }}?v={{ filemtime(public_path('assets/js/admin/contact-inbox.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/content.js') }}?v={{ filemtime(public_path('assets/js/admin/content.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/loader.js') }}?v={{ filemtime(public_path('assets/js/admin/loader.js')) }}"></script>
        <script src="{{ asset('assets/js/vendor/lucide.min.js') }}?v={{ filemtime(public_path('assets/js/vendor/lucide.min.js')) }}"></script>
        <script src="{{ asset('assets/js/admin/icon-picker.js') }}?v={{ filemtime(public_path('assets/js/admin/icon-picker.js')) }}"></script>
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
                        Slimme-PC <span class="block text-[11px] font-semibold uppercase tracking-widest text-blue-100">Beheer</span>
                    </span>
                </a>

                <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-blue-100 transition hover:bg-white/20 hover:text-white lg:hidden" aria-label="Menu sluiten">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="sidebar-scroll flex-1 space-y-1.5 overflow-y-auto px-1 py-6">
                <p class="mb-3 px-4 text-[11px] font-bold uppercase tracking-widest text-blue-100">Overzicht</p>

                <x-admin.sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                    </x-slot>
                    Dashboard
                </x-admin.sidebar-link>

                <p class="mb-3 mt-8 px-4 text-[11px] font-bold uppercase tracking-widest text-blue-100">Beheer</p>

                {{-- 1. Home-page Dropdown (Split CMS) --}}
                <div x-data="{ open: {{ (request()->routeIs('admin.content.design.edit') || (request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home')) ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span>Home-page</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.content.design.edit') }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.design.edit') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Ontwerp &amp; SEO
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'header']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home' && request()->route('section') === 'header' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Header
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'hero']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home' && request()->route('section') === 'hero' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Hero
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'why']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home' && request()->route('section') === 'why' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Waarom voor ons kiezen
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'services']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home' && request()->route('section') === 'services' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Services
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'home', 'section' => 'footer']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'home' && request()->route('section') === 'footer' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Footer
                        </a>
                    </div>
                </div>

                {{-- 1b. Tarieven Dropdown --}}
                <div x-data="{ open: {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'tarieven' ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                            <span>Tarieven</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.content.section.edit', ['page' => 'tarieven', 'section' => 'hero']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'tarieven' && request()->route('section') === 'hero' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Hero
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'tarieven', 'section' => 'pricing']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'tarieven' && request()->route('section') === 'pricing' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Tarieven &amp; Prijzen
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'tarieven', 'section' => 'extra']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'tarieven' && request()->route('section') === 'extra' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Algemene &amp; Zakelijke tarieven
                        </a>
                    </div>
                </div>

                {{-- 1c. Contact Dropdown --}}
                @php
                    $inboxNewCount = \App\Models\ContactSubmission::where('status', 'new')->count();
                    $repairNewCount = \App\Models\RepairSubmission::where('status', 'new')->count();
                @endphp
                <div x-data="{ open: {{ request()->routeIs('admin.contact-inbox.*') || (request()->routeIs('admin.content.section.edit') && request()->route('page') === 'contact') ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span>Contact</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.contact-inbox.index') }}"
                           class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.contact-inbox.*') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                </svg>
                                Inbox
                            </span>
                            <span id="sidebarInboxBadge" class="{{ $inboxNewCount > 0 ? '' : 'hidden' }} inline-flex min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">{{ min($inboxNewCount, 99) }}</span>
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'contact', 'section' => 'hero']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'contact' && request()->route('section') === 'hero' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Hero
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'contact', 'section' => 'gegevens']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'contact' && request()->route('section') === 'gegevens' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Contactgegevens
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'contact', 'section' => 'formulier']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'contact' && request()->route('section') === 'formulier' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Contactformulier
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'contact', 'section' => 'locatie']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'contact' && request()->route('section') === 'locatie' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Locatie
                        </a>
                    </div>
                </div>

                {{-- 1d. Over ons Dropdown --}}
                <div x-data="{ open: {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span>Over ons</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'hero']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'hero' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Hero
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'meet']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'meet' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Meet Mo
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'why']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'why' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Waarom klanten terugkomen
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'werkplaats']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'werkplaats' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Binnen in onze werkplaats
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'reis']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'reis' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Onze reis
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'reviews']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'reviews' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Wat klanten zeggen
                        </a>
                        <a href="{{ route('admin.content.section.edit', ['page' => 'overons', 'section' => 'trust']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === 'overons' && request()->route('section') === 'trust' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Trust (onderaan)
                        </a>
                    </div>
                </div>

                {{-- 2. Diensten Dropdown --}}
                @php
                    $activeSvcPage = request()->route('page');
                    $isSvcPage = in_array($activeSvcPage, array_values(config('cms.service_slugs')), true) || $activeSvcPage === 'reparatie';
                    // Only show service pages that have actual content (so unbuilt services stay out of the admin nav).
                    // De-duplicate by pageKey so alias slugs (e.g. macbook-reparatie -> mac) don't create duplicate sidebar entries.
                    $svcVisiblePages = [];
                    $seenSvc = [];
                    foreach (array_values(config('cms.service_slugs')) as $pageKey) {
                        if (!isset($seenSvc[$pageKey]) && \App\Models\ContentBlock::where('page', $pageKey)->exists()) {
                            $svcVisiblePages[] = $pageKey;
                            $seenSvc[$pageKey] = true;
                        }
                    }
                @endphp
                <div x-data="{ open: {{ $isSvcPage ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-diensten') !== null) { this.open = localStorage.getItem('nav-diensten') === '1'; } }, toggle() { this.open = !this.open; localStorage.setItem('nav-diensten', this.open ? '1' : '0'); } }" class="space-y-1">
                    <button type="button" @click="toggle()"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                            </svg>
                            <span>Diensten</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-[13px] text-white">
                        {{-- Reparatie aanmelden — always first --}}
                        @php
                            $repPageKey = 'reparatie';
                            $repPage = config("cms.pages.{$repPageKey}");
                            $repLabel = $repPage['label'] ?? 'Reparatie aanmelden';
                            $repSections = $repPage['sections'] ?? [];
                        @endphp
                             <div x-data="{ svc: {{ ($activeSvcPage === $repPageKey || request()->routeIs('admin.reparatie-aanmeldingen.*')) ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-svc-{{$repPageKey}}') !== null) { this.svc = localStorage.getItem('nav-svc-{{$repPageKey}}') === '1'; } }, toggle() { this.svc = !this.svc; localStorage.setItem('nav-svc-{{$repPageKey}}', this.svc ? '1' : '0'); } }" class="space-y-1">
                            <a href="{{ route('admin.reparatie-aanmeldingen.index') }}"
                               class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.reparatie-aanmeldingen.*') ? 'bg-white/15 text-white font-bold' : 'text-white hover:bg-white/10 hover:text-white' }}">
                                <span>Aanmeldingen</span>
                                <span id="sidebarRepairBadge" class="{{ $repairNewCount > 0 ? '' : 'hidden' }} inline-flex min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">{{ min($repairNewCount, 99) }}</span>
                            </a>
                             <button type="button" @click="toggle()"
                                    class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-1.5 text-left font-semibold text-white hover:bg-white/10 hover:text-white">
                                <span>{{ $repLabel }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                     class="h-3.5 w-3.5 shrink-0 transition-transform duration-200" :class="svc ? 'rotate-180' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="svc" x-cloak x-transition class="space-y-1 border-l border-white/15 ml-1 pl-3">
                                @foreach ($repSections as $sectionKey => $sectionCfg)
                                    <a href="{{ route('admin.content.section.edit', ['page' => $repPageKey, 'section' => $sectionKey]) }}"
                                       class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === $repPageKey && request()->route('section') === $sectionKey ? 'bg-white/15 text-white font-bold' : 'text-white hover:bg-white/10 hover:text-white' }}">
                                        {{ $sectionCfg['label'] ?? $sectionKey }}
                                    </a>
                                @endforeach
                            </div>


                        </div>

                        @foreach ($svcVisiblePages as $pageKey)
                            @php
                                $svcPage = config("cms.pages.{$pageKey}");
                                $svcLabel = $svcPage['label'] ?? $pageKey;
                                $svcSections = $svcPage['sections'] ?? [];
                            @endphp
                            <div x-data="{ svc: {{ $activeSvcPage === $pageKey ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-svc-{{$pageKey}}') !== null) { this.svc = localStorage.getItem('nav-svc-{{$pageKey}}') === '1'; } }, toggle() { this.svc = !this.svc; localStorage.setItem('nav-svc-{{$pageKey}}', this.svc ? '1' : '0'); } }" class="space-y-1">
                                <button type="button" @click="toggle()"
                                        class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-1.5 text-left font-semibold text-white hover:bg-white/10 hover:text-white">
                                    <span>{{ $svcLabel }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                         class="h-3.5 w-3.5 shrink-0 transition-transform duration-200" :class="svc ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="svc" x-cloak x-transition class="space-y-1 border-l border-white/15 ml-1 pl-3">
                                    @foreach ($svcSections as $sectionKey => $sectionCfg)
                                        <a href="{{ route('admin.content.section.edit', ['page' => $pageKey, 'section' => $sectionKey]) }}"
                                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.content.section.edit') && request()->route('page') === $pageKey && request()->route('section') === $sectionKey ? 'bg-white/15 text-white font-bold' : 'text-white hover:bg-white/10 hover:text-white' }}">
                                            {{ $sectionCfg['label'] ?? $sectionKey }}
                                        </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>









                {{-- 🛒 Webshop Dropdown --}}
                <div x-data="{ open: {{ request()->routeIs('admin.webshop.*') ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-webshop') !== null) { this.open = localStorage.getItem('nav-webshop') === '1'; } }, toggle() { this.open = !this.open; localStorage.setItem('nav-webshop', this.open ? '1' : '0'); } }" class="space-y-1">
                    <button type="button" @click="toggle()"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 00-3.75-3.75v0A3.75 3.75 0 008.25 6v4.5m11.25 0h-15A2.25 2.25 0 002.25 12.75v6A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25v-6A2.25 2.25 0 0019.5 10.5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75h6" />
                            </svg>
                            <span>Webshop</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.webshop.categories.index') }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.webshop.categories.*') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Categorieën
                        </a>
                        <a href="{{ route('admin.webshop.products.index') }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.webshop.products.*') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Producten
                        </a>
                    </div>
                </div>

                {{-- Afspraak aan huis Dropdown --}}
                <div x-data="{ open: {{ (request()->routeIs('admin.afspraak-aanvragen.*') || (request()->routeIs('admin.content.section.edit') && request()->route('page') === 'afspraak')) ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-afspraak') !== null) { this.open = localStorage.getItem('nav-afspraak') === '1'; } }, toggle() { this.open = !this.open; localStorage.setItem('nav-afspraak', this.open ? '1' : '0'); } }" class="space-y-1">
                    @php
                        $afspraakNewCount = \App\Models\AfspraakSubmission::where('status', 'new')->count();
                        $afspraakPage = config('cms.pages.afspraak');
                        $afspraakSections = $afspraakPage['sections'] ?? [];
                    @endphp
                    <button type="button" @click="toggle()"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                                <path d="M9 16l2 2 4-4"></path>
                            </svg>
                            <span>Afspraak</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-6 space-y-1.5 border-l-2 border-white/30 pl-4 py-1 text-xs">
                        <a href="{{ route('admin.afspraak-aanvragen.index') }}"
                           class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.afspraak-aanvragen.*') ? 'bg-white/10 font-bold text-white shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            <span>Aanvragen</span>
                            <span id="sidebarAfspraakBadge" class="{{ $afspraakNewCount > 0 ? '' : 'hidden' }} inline-flex min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">{{ min($afspraakNewCount, 99) }}</span>
                        </a>
                        @foreach ($afspraakSections as $sectionKey => $sectionCfg)
                            <a href="{{ route('admin.content.section.edit', ['page' => 'afspraak', 'section' => $sectionKey]) }}"
                               class="block rounded-lg px-3 py-2 transition {{ (request()->routeIs('admin.content.section.edit') && request()->route('page') === 'afspraak' && request()->route('section') === $sectionKey) ? 'bg-white/15 font-bold text-white' : 'text-white hover:bg-white/10 hover:text-white' }}">
                                {{ $sectionCfg['label'] ?? $sectionKey }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Bevestiging-mail Dropdown --}}
                <div x-data="{ open: {{ request()->routeIs('admin.bevestiging-mail.*') ? 'true' : 'false' }}, init() { if (localStorage.getItem('nav-bevestiging') !== null) { this.open = localStorage.getItem('nav-bevestiging') === '1'; } }, toggle() { this.open = !this.open; localStorage.setItem('nav-bevestiging', this.open ? '1' : '0'); } }" class="space-y-1">
                    <button type="button" @click="toggle()"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span>Bevestiging-mail</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.bevestiging-mail.hardware.index') }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.bevestiging-mail.hardware.*') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Hardware
                        </a>
                        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'laptop']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.bevestiging-mail.ontvangst.*') && request()->input('type', 'laptop') === 'laptop' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Laptops-PC
                        </a>
                        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'ipad_iphone']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.bevestiging-mail.ontvangst.*') && request()->input('type') === 'ipad_iphone' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            iPad-iPhone
                        </a>
                        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'playstation_xbox']) }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.bevestiging-mail.ontvangst.*') && request()->input('type') === 'playstation_xbox' ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            PlayStation-Xbox
                        </a>
                    </div>
                </div>

                {{-- 11. Users-beheren Dropdown (Active: Klanten) --}}
                <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.724v-1.224A4.5 4.5 0 0013.5 13H10.5A4.5 4.5 0 006 17.5v1.224M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            <span>Users-beheren</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1.5 py-1 text-xs">
                        <a href="{{ route('admin.users.index') }}"
                           class="block rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-blue-50 hover:bg-white/15 hover:text-white' }}">
                            Users
                        </a>
                    </div>
                </div>

            </nav>

            <script>
                // Sidebar accordion: open one dropdown closes others, keeps active page open
                    document.addEventListener('DOMContentLoaded', () => {
                    const nav = document.querySelector('nav.sidebar-scroll');
                    if (!nav || typeof Alpine === 'undefined') return;
                    nav.addEventListener('click', (e) => {
                        const btn = e.target.closest('button');
                        if (!btn) return;
                        const dropdown = btn.closest('[x-data]');
                        if (!dropdown) return;
                        if (!dropdown.matches('nav.sidebar-scroll > [x-data]')) return;
                        // check if this button toggles open (contains open or toggle)
                        const clickAttr = btn.getAttribute('@click') || btn.getAttribute('x-on:click') || '';
                        if (!clickAttr.includes('open') && !clickAttr.includes('toggle')) return;
                        setTimeout(() => {
                            try {
                                const data = Alpine.$data(dropdown);
                                if (data && data.open) {
                                    document.querySelectorAll('nav.sidebar-scroll > [x-data]').forEach(el => {
                                        if (el === dropdown) return;
                                        try {
                                            const d = Alpine.$data(el);
                                            if (d && typeof d.open !== 'undefined') d.open = false;
                                        } catch {}
                                    });
                                }
                            } catch {}
                        }, 0);
                    });
                });
            </script>

            {{-- Sidebar footer --}}
            <div class="border-t border-white/10 p-4">
                <form method="POST" action="{{ route('logout') }}" data-loading>
                    @csrf
                    <button type="submit" data-loading class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-blue-50 transition duration-200 hover:bg-white/10 hover:text-red-300">
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
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                                class="rounded-lg p-2 transition hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                                aria-label="Menu openen">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6" style="color: var(--c-heading)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <h1 class="hidden min-w-0 truncate text-lg font-bold tracking-tight sm:block sm:text-xl" style="color: var(--c-heading)">
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
            <main class="flex-1 px-3 py-5 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            {{-- Footer (hidden on the full-height inbox page so it never scrolls) --}}
            @if (!request()->routeIs('admin.contact-inbox.index'))
            <footer class="px-3 pb-5 sm:px-6 lg:px-8">
                <p class="text-center text-xs" style="color: var(--c-muted)">
                    &copy; {{ date('Y') }} Slimme-PC Beheer. Alle rechten voorbehouden.
                </p>
            </footer>
            @endif
        </div>
        @stack('scripts')

        {{-- Global Flash Messages (Toast) --}}
        @if (session('success') || session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const msg = @json(session('success') ?? session('status'));
                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.success(msg);
                    }
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.error(@json(session('error')));
                    }
                });
            </script>
        @endif
    </body>
</html>


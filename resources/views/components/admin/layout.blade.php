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
                                Inbox / Berichten
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

                {{-- 2. Diensten Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
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
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Hardware-afspraak <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Hardware afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Software-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Dataherstelen-afdeeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Website-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Netwerk-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Pcbouwen-afdeling <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 3. Pages Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Pages</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Hardware-photos <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 4. Webshop Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span>Webshop</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Category-producten <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Producten <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">SliderImage <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Licentiecodes <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 5. Website-aanvragen Link --}}
                <span class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-medium text-blue-100 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.25m0 0A11.959 11.959 0 0112 16.5c-3.162 0-6.033-1.23-8.156-3.228m0 0A8.96 8.96 0 013 12c0-.778.099-1.533.284-2.25" />
                        </svg>
                        <span>Website-aanvragen</span>
                    </span>
                    <span class="rounded-full bg-white/20 px-2 py-0.5 text-[9px] font-bold text-white">Binnenkort</span>
                </span>

                {{-- 6. Contact-klanten Link --}}
                <span class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-medium text-blue-100 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span>Contact-klanten</span>
                    </span>
                    <span class="rounded-full bg-white/20 px-2 py-0.5 text-[9px] font-bold text-white">Binnenkort</span>
                </span>

                {{-- 7. Bestellings Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 6h.008v.008H6.75V21Z" />
                            </svg>
                            <span>Bestellings</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Bestellings <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Facturen <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 8. Afspraken Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-1.35h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span>Afspraken</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Algemeen afspraken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Facturen van afspraken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 9. Abonnement Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.375 3.375 0 016.338 0z" />
                            </svg>
                            <span>Abonnement</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Klanten-lijst <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Abonnement-beheren <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 10. Prijs-instellingen Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.932 6.932 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.332.183-.582.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            </svg>
                            <span>Prijs-instellingen</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Prijs-beheren <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
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
                        <span class="block rounded-lg px-3 py-1.5 text-blue-100 cursor-not-allowed hover:bg-white/15">
                            Send Emails <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block rounded-lg px-3 py-1.5 text-blue-100 cursor-not-allowed hover:bg-white/15">
                            Klantenlijst <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block rounded-lg px-3 py-1.5 text-blue-100 cursor-not-allowed hover:bg-white/15">
                            Popup Melding <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block rounded-lg px-3 py-1.5 text-blue-100 cursor-not-allowed hover:bg-white/15">
                            Algemene Voorwaarden <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                        <span class="block rounded-lg px-3 py-1.5 text-blue-100 cursor-not-allowed hover:bg-white/15">
                            Privacybeleid <small class="text-[10px] font-normal italic opacity-60">(Binnenkort)</small>
                        </span>
                    </div>
                </div>

                {{-- 12. Manual Invoices Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Manual-invoices</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Hardware <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Bevestging-mail <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 13. Live Chat Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.242c0 .969-.616 1.814-1.5 2.097M18.75 9.75h-10.5a2.25 2.25 0 00-2.25 2.25v4.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-4.5a2.25 2.25 0 00-2.25-2.25z" />
                            </svg>
                            <span>Live-chat</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Vragen-antwoorden <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Inbox-Live <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>

                {{-- 14. Database site Dropdown --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button type="button" @click="open = !open"
                            class="group flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white"
                            style="color: rgba(255,255,255,0.95)">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 1.036-.84 1.875-1.875 1.875h-12.75c-1.035 0-1.875-.84-1.875-1.875m16.5 0v11.25c0 1.035-.84 1.875-1.875 1.875h-12.75c-1.035 0-1.875-.84-1.875-1.875V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0h-16.5" />
                            </svg>
                            <span>Database site</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: rgba(203,213,225,0.5)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="border-l-2 border-white/30 ml-6 pl-4 space-y-1 py-1 text-xs text-blue-100">
                        <span class="block rounded-lg px-3 py-1.5 cursor-not-allowed hover:bg-white/5">Backup-maken <small class="text-[10px] opacity-60">(Binnenkort)</small></span>
                    </div>
                </div>
            </nav>
                    </div>
                </div>

            </nav>
            </nav>

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

            {{-- Footer --}}
            <footer class="px-3 pb-5 sm:px-6 lg:px-8">
                <p class="text-center text-xs" style="color: var(--c-muted)">
                    &copy; {{ date('Y') }} Slimme-PC Beheer. Alle rechten voorbehouden.
                </p>
            </footer>
        </div>
    </body>
</html>


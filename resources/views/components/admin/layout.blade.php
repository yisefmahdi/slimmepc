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
            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
                <p class="mb-3 px-4 text-[11px] font-bold uppercase tracking-widest text-slate-500">Overzicht</p>

                <x-admin.sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.13 1.13 0 0 1 1.592 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                    </x-slot>
                    Dashboard
                </x-admin.sidebar-link>

                <p class="mb-3 mt-8 px-4 text-[11px] font-bold uppercase tracking-widest text-slate-500">Beheer</p>

                <x-admin.sidebar-link :href="route('admin.klanten.index')" :active="request()->routeIs('admin.klanten.*')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </x-slot>
                    Klanten
                </x-admin.sidebar-link>

                <x-admin.sidebar-link :href="route('admin.content.index')" :active="request()->routeIs('admin.content.*')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM12 12h.007v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 18h.007v.008H3.75V18Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </x-slot>
                    Website inhoud
                </x-admin.sidebar-link>

                <a href="#" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white" style="color: rgba(203,213,225,0.85)" title="Binnenkort beschikbaar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 6h.008v.008H6.75V21Z" />
                        </svg>
                        Bestellingen
                    <span class="ms-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold text-slate-400">Binnenkort</span>
                </a>

                <a href="#" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white" style="color: rgba(203,213,225,0.85)" title="Binnenkort beschikbaar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                    </svg>
                    Reparaties
                    <span class="ms-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold text-slate-400">Binnenkort</span>
                </a>

                <a href="#" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white" style="color: rgba(203,213,225,0.85)" title="Binnenkort beschikbaar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    Producten
                    <span class="ms-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold text-slate-400">Binnenkort</span>
                </a>

                <a href="#" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white" style="color: rgba(203,213,225,0.85)" title="Binnenkort beschikbaar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Instellingen
                    <span class="ms-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-bold text-slate-400">Binnenkort</span>
                </a>
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

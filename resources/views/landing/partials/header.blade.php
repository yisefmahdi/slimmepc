@php
    $navLinks = $c['header']['nav_links'] ?? [];

    $currentPath = ltrim(request()->path(), '/');
    foreach ($navLinks as &$navLink) {
        $navLink['active'] = false;
        $navUrlPath = ltrim((string) ($navLink['url'] ?? ''), '/');
        if ($navUrlPath === $currentPath) {
            $navLink['active'] = true;
        }
    }
    unset($navLink);

    $navBefore = array_slice($navLinks, 0, 2);
    $navAfter  = array_slice($navLinks, 2);

    // Diensten dropdown: list every service page; mark as ready only when it has content.
    $servicePages = config('cms.service_slugs', []);
    $svcIcons = [
        'laptopreparatie' => 'laptop',
        'pcreparatie'     => 'monitor',
        'mac'             => 'apple',
        'macbook'         => 'apple',
        'datarecovery'    => 'database',
        'ipad'            => 'tablet',
        'moederbord'      => 'cpu',
        'software'        => 'panels-top-left',
        'netwerk'         => 'wifi',
        'console'         => 'gamepad-2',
    ];
    $svcReady = [];
    foreach ($servicePages as $slug => $pageKey) {
        $svcReady[$slug] = \App\Models\ContentBlock::where('page', $pageKey)->exists();
    }
    // De-duplicate by pageKey so alias slugs (e.g. macbook-reparatie -> mac) don't render twice in the dropdown.
    $seenKeys = [];
    $uniqueServicePages = [];
    foreach ($servicePages as $slug => $pageKey) {
        if (!isset($seenKeys[$pageKey])) {
            $uniqueServicePages[$slug] = $pageKey;
            $seenKeys[$pageKey] = true;
        }
    }
    $servicePages = $uniqueServicePages;
@endphp

<header id="navbar" class="
        sticky top-0 z-50
        border-b border-white/10
        bg-gradient-to-r
        from-brand-950 via-brand-800 to-brand-600
        shadow-navbar
    ">
    <div class="
            mx-auto flex min-h-[72px] max-w-[1500px]
            items-center justify-between
            gap-5 px-4
            sm:px-6 lg:px-8
        ">

        <!-- Logo -->
        <a href="/" class="
                flex shrink-0 items-center gap-2.5
                rounded-2xl
                focus:outline-none
                focus:ring-2 focus:ring-white/40
            ">
            <div class="
                    flex h-[52px] w-[52px] items-center justify-center
                    overflow-hidden rounded-2xl
                    bg-white/10
                    ring-1 ring-white/15
                ">
                <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}" alt="{{ $c['header']['logo_text'] ?? 'Slimme-PC' }}" class="h-11 w-11 object-contain" decoding="async">
            </div>

            <div class="hidden sm:block">
                <div class="
                        text-lg font-extrabold
                        tracking-tight text-white
                    ">
                    {{ $c['header']['logo_text'] ?? 'SLIMME-PC' }}
                </div>

                <div class="
                        mt-0.5 text-[11px]
                        font-medium text-blue-100/80
                    ">
                    {{ $c['header']['tagline'] ?? '' }}
                </div>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="
                hidden flex-1 items-stretch
                justify-center gap-1
                xl:flex
            " aria-label="Hoofdnavigatie">

            @foreach ($navBefore as $link)
                <a href="{{ $link['url'] ?? '#' }}" class="
                        group relative flex min-w-[82px]
                        flex-col items-center justify-center
                        gap-1 rounded-2xl px-3 py-3
                        {{ !empty($link['active']) ? 'bg-white/10 text-white ring-1 ring-white/10' : 'text-blue-50 hover:bg-white/10 hover:text-white' }}
                        transition
                    ">
                    <i data-lucide="{{ $link['icon'] ?? 'circle' }}" class="h-5 w-5"></i>

                    <span class="text-xs font-semibold">
                        {{ $link['label'] ?? '' }}
                    </span>

                    @if (!empty($link['active']))
                        <span class="
                            absolute -bottom-[1px]
                            h-[3px] w-8
                            rounded-full bg-blue-300
                        "></span>
                    @endif
                </a>
            @endforeach

            <!-- Webshop dropdown -->
            <div class="desktop-dropdown-wrapper relative">
                <button type="button" class="
                        flex min-w-[96px]
                        flex-col items-center justify-center
                        gap-1 rounded-2xl
                        px-3 py-3
                        text-blue-50
                        transition
                        hover:bg-white/10
                        hover:text-white
                    ">
                    <i data-lucide="shopping-bag" class="h-5 w-5"></i>

                    <span class="flex items-center gap-1 text-xs font-semibold">
                        Webshop

                        <i data-lucide="chevron-down" class="h-3.5 w-3.5"></i>
                    </span>
                </button>

                <div class="
                        desktop-dropdown absolute
                        left-1/2 top-full
                        mt-3 w-[430px]
                        -translate-x-1/2
                        rounded-2xl
                        border border-slate-200
                        bg-white p-3
                        shadow-menu
                    ">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($c['header']['webshop_dropdown'] ?? [] as $item)
                            <a href="{{ $item['url'] ?? '#' }}" class="
                                    flex items-center gap-3
                                    rounded-xl p-3
                                    transition
                                    hover:bg-brand-50
                                ">
                                <span class="
                                        flex h-10 w-10
                                        items-center justify-center
                                        rounded-xl
                                        bg-brand-100
                                        text-brand-700
                                    ">
                                    <i data-lucide="{{ $item['icon'] ?? 'shopping-bag' }}" class="h-5 w-5"></i>
                                </span>

                                <span>
                                    <strong class="block text-sm text-slate-900">
                                        {{ $item['label'] ?? '' }}
                                    </strong>

                                    <small class="text-xs text-slate-500">
                                        {{ $item['subtitle'] ?? '' }}
                                    </small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Diensten dropdown -->
            <div class="desktop-dropdown-wrapper relative">
                <button type="button" class="
                        flex min-w-[94px]
                        flex-col items-center justify-center
                        gap-1 rounded-2xl
                        px-3 py-3
                        text-blue-50
                        transition
                        hover:bg-white/10
                        hover:text-white
                    ">
                    <i data-lucide="wrench" class="h-5 w-5"></i>

                    <span class="flex items-center gap-1 text-xs font-semibold">
                        Diensten

                        <i data-lucide="chevron-down" class="h-3.5 w-3.5"></i>
                    </span>
                </button>

                <div class="
                        desktop-dropdown absolute
                        left-1/2 top-full
                        mt-3 w-[520px]
                        -translate-x-1/2
                        rounded-2xl
                        border border-slate-200
                        bg-white p-3
                        shadow-menu
                    ">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($servicePages as $slug => $pageKey)
                            @php
                                $ready = $svcReady[$slug] ?? false;
                                $svcLabel = config("cms.pages.{$pageKey}.label") ?? $pageKey;
                                $svcIcon = $svcIcons[$pageKey] ?? 'wrench';
                            @endphp
                            @if ($ready)
                                <a href="{{ url('/diensten/'.$slug) }}" class="flex gap-3 rounded-xl p-3 transition hover:bg-brand-50">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700">
                                        <i data-lucide="{{ $svcIcon }}" class="h-5 w-5"></i>
                                    </span>
                                    <span>
                                        <strong class="block text-sm text-slate-900">{{ $svcLabel }}</strong>
                                        <small class="text-xs text-slate-500">Direct repareren</small>
                                    </span>
                                </a>
                            @else
                                <div class="flex gap-3 rounded-xl p-3 opacity-60">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                        <i data-lucide="{{ $svcIcon }}" class="h-5 w-5"></i>
                                    </span>
                                    <span>
                                        <strong class="block text-sm text-slate-700">{{ $svcLabel }}</strong>
                                        <small class="mt-0.5 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Binnenkort</small>
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach ($navAfter as $link)
                <a href="{{ $link['url'] ?? '#' }}" class="
                        group relative flex min-w-[82px]
                        flex-col items-center justify-center
                        gap-1 rounded-2xl px-3 py-3
                        {{ !empty($link['active']) ? 'bg-white/10 text-white ring-1 ring-white/10' : 'text-blue-50 hover:bg-white/10 hover:text-white' }}
                        transition
                    ">
                    <i data-lucide="{{ $link['icon'] ?? 'circle' }}" class="h-5 w-5"></i>

                    <span class="text-xs font-semibold">
                        {{ $link['label'] ?? '' }}
                    </span>

                    @if (!empty($link['active']))
                        <span class="
                            absolute -bottom-[1px]
                            h-[3px] w-8
                            rounded-full bg-blue-300
                        "></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <!-- Right Actions -->
        <div class="flex shrink-0 items-center gap-1 sm:gap-2">

            <button id="openSearch" type="button" class="
                    flex h-11 w-11 items-center
                    justify-center rounded-xl
                    text-white transition
                    hover:bg-white/10
                " aria-label="Zoeken">
                <i data-lucide="search" class="h-5 w-5"></i>
            </button>

            <a href="/wishlist" class="
                    relative hidden h-11 w-11
                    items-center justify-center
                    rounded-xl text-white
                    transition hover:bg-white/10
                    sm:flex
                " aria-label="Favorieten">
                <i data-lucide="heart" class="h-5 w-5"></i>

                <span class="
                        absolute right-0 top-0
                        flex h-4 min-w-4
                        items-center justify-center
                        rounded-full bg-brand-accent
                        px-1 text-[9px]
                        font-extrabold
                        text-brand-950
                    ">
                    {{ $c['header']['wishlist_count'] ?? 0 }}
                </span>
            </a>

            <a href="/cart" class="
                    relative flex h-11 w-11
                    items-center justify-center
                    rounded-xl text-white
                    transition hover:bg-white/10
                " aria-label="Winkelwagen">
                <i data-lucide="shopping-cart" class="h-5 w-5"></i>

                <span class="
                        absolute right-0 top-0
                        flex h-4 min-w-4
                        items-center justify-center
                        rounded-full bg-brand-accent
                        px-1 text-[9px]
                        font-extrabold
                        text-brand-950
                    ">
                    {{ $c['header']['cart_count'] ?? 0 }}
                </span>
            </a>

            <div class="hidden h-8 w-px bg-white/20 lg:block"></div>

            @guest
                <a href="{{ route('login') }}" class="
                        hidden h-11 items-center gap-2
                        rounded-xl bg-brand-accent
                        px-4 py-3
                        text-sm font-extrabold
                        text-brand-950
                        transition
                        hover:bg-brand-accent/90
                        lg:flex
                    ">
                    <i data-lucide="user-round" class="h-5 w-5"></i>
                    <span>Account</span>
                </a>
            @endguest

            @auth
                <details class="group relative hidden lg:block">
                    <summary class="
                            flex h-11 cursor-pointer items-center
                            gap-2 rounded-xl bg-brand-accent
                            px-4 py-3 text-sm
                            font-extrabold text-brand-950
                            transition hover:bg-brand-accent/90
                            list-none [&::-webkit-details-marker]:hidden
                        ">
                        <i data-lucide="user-round" class="h-5 w-5 shrink-0"></i>
                        <span class="max-w-[150px] truncate">{{ Auth::user()->name }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 transition-transform duration-200 group-open:rotate-180"></i>
                    </summary>

                    <div class="
                            absolute right-0 top-full z-50
                            mt-2 w-56 overflow-hidden
                            rounded-2xl border border-slate-200
                            bg-white p-1.5
                            shadow-[0_20px_50px_rgba(15,23,42,.18)]
                        ">
                        <a href="{{ route('profile.edit') }}" class="
                                flex items-center gap-2.5
                                rounded-xl px-3.5 py-2.5
                                text-sm font-semibold text-slate-700
                                transition hover:bg-blue-50 hover:text-blue-700
                            ">
                            <i data-lucide="settings2" class="h-4 w-4 text-brand-primary"></i>
                            Mijn account
                        </a>

                        <div class="my-1 h-px bg-slate-200"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="
                                    flex w-full items-center gap-2.5
                                    rounded-xl px-3.5 py-2.5
                                    text-sm font-semibold text-red-600
                                    transition hover:bg-red-50
                                ">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                                Uitloggen
                            </button>
                        </form>
                    </div>
                </details>
            @endauth

            <button id="openMobileMenu" type="button" class="
                    flex h-11 w-11
                    items-center justify-center
                    rounded-xl bg-white/10
                    text-white
                    ring-1 ring-white/10
                    transition hover:bg-white/15
                    xl:hidden
                " aria-label="Menu openen">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile overlay -->
<div id="mobileOverlay" class="
        mobile-overlay fixed inset-0 z-[60]
        bg-brand-950/75
        backdrop-blur-sm
    "></div>

<!-- Mobile menu -->
<aside id="mobileDrawer" class="
        mobile-drawer fixed bottom-0 left-0 top-0
        z-[70] w-[88%] max-w-[390px]
        overflow-y-auto bg-white
        shadow-2xl
    ">
    <div class="
            sticky top-0 z-10
            flex items-center justify-between
            border-b border-slate-200
            bg-white px-5 py-4
        ">
        <div class="flex items-center gap-3">
            <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}" alt="{{ $c['header']['logo_text'] ?? 'Slimme-PC' }}" class="h-12 w-12 object-contain" decoding="async">

            <div>
                <div class="font-extrabold text-brand-950">
                    {{ $c['header']['logo_text'] ?? 'SLIMME-PC' }}
                </div>

                <div class="text-[10px] text-slate-500">
                    {{ $c['header']['tagline'] ?? '' }}
                </div>
            </div>
        </div>

        <button id="closeMobileMenu" type="button" class="
                flex h-10 w-10
                items-center justify-center
                rounded-xl bg-slate-100
                text-slate-700
            ">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    <div class="p-4">
        <a href="/afspraak" class="
                mb-5 flex items-center
                justify-center gap-2
                rounded-xl bg-brand-accent
                px-4 py-3.5
                font-extrabold text-brand-950
            ">
            <i data-lucide="calendar-check" class="h-5 w-5"></i>
            Afspraak maken
        </a>

        <nav class="space-y-1">
            @foreach ($navBefore as $link)
                <a href="{{ $link['url'] ?? '#' }}" class="
                        flex items-center gap-3
                        rounded-xl px-4 py-3
                        {{ !empty($link['active']) ? 'bg-brand-50 font-semibold text-brand-800' : 'font-medium text-slate-700 hover:bg-slate-100' }}
                    ">
                    <i data-lucide="{{ $link['icon'] ?? 'circle' }}" class="h-5 w-5"></i>
                    {{ $link['label'] ?? '' }}
                </a>
            @endforeach

            <button type="button" data-accordion="mobileWebshop" class="
                    flex w-full items-center
                    justify-between rounded-xl
                    px-4 py-3
                    font-medium text-slate-700
                    hover:bg-slate-100
                ">
                <span class="flex items-center gap-3">
                    <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                    Webshop
                </span>

                <i data-lucide="chevron-down" class="accordion-icon h-4 w-4 transition"></i>
            </button>

            <div id="mobileWebshop" class="hidden space-y-1 pb-2 pl-12">
                @foreach ($c['header']['webshop_dropdown'] ?? [] as $item)
                    <a href="{{ $item['url'] ?? '#' }}"
                        class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100">
                        {{ $item['label'] ?? '' }}
                    </a>
                @endforeach
            </div>

            <button type="button" data-accordion="mobileServices" class="
                    flex w-full items-center
                    justify-between rounded-xl
                    px-4 py-3
                    font-medium text-slate-700
                    hover:bg-slate-100
                ">
                <span class="flex items-center gap-3">
                    <i data-lucide="wrench" class="h-5 w-5"></i>
                    Diensten
                </span>

                <i data-lucide="chevron-down" class="accordion-icon h-4 w-4 transition"></i>
            </button>

                <div id="mobileServices" class="hidden space-y-1 pb-2 pl-12">
                    @foreach ($servicePages as $slug => $pageKey)
                        @php
                            $ready = $svcReady[$slug] ?? false;
                            $svcLabel = config("cms.pages.{$pageKey}.label") ?? $pageKey;
                        @endphp
                        @if ($ready)
                            <a href="{{ url('/diensten/'.$slug) }}"
                                class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100">
                                {{ $svcLabel }}
                            </a>
                        @else
                            <div class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-400">
                                <span>{{ $svcLabel }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold">Binnenkort</span>
                            </div>
                        @endif
                    @endforeach
                </div>

            @foreach ($navAfter as $link)
                <a href="{{ $link['url'] ?? '#' }}" class="
                        flex items-center gap-3
                        rounded-xl px-4 py-3
                        {{ !empty($link['active']) ? 'bg-brand-50 font-semibold text-brand-800' : 'font-medium text-slate-700 hover:bg-slate-100' }}
                    ">
                    <i data-lucide="{{ $link['icon'] ?? 'circle' }}" class="h-5 w-5"></i>
                    {{ $link['label'] ?? '' }}
                </a>
            @endforeach
        </nav>

        <div class="
                mt-5 grid grid-cols-3
                gap-2 border-t
                border-slate-200 pt-5
            ">
            @guest
                <a href="{{ route('login') }}" class="
                        flex flex-col items-center gap-2
                        rounded-xl bg-slate-100
                        px-2 py-3 text-xs
                        font-semibold text-slate-700
                    ">
                    <i data-lucide="user-round" class="h-5 w-5"></i>
                    Account
                </a>
            @endguest

            @auth
                <a href="{{ route('profile.edit') }}" class="
                        flex flex-col items-center gap-2
                        rounded-xl bg-slate-100
                        px-2 py-3 text-xs
                        font-semibold text-slate-700
                    ">
                    <i data-lucide="user-round" class="h-5 w-5"></i>
                    <span class="max-w-full truncate">{{ Auth::user()->name }}</span>
                </a>
            @endauth

            <a href="/wishlist" class="
                    flex flex-col items-center gap-2
                    rounded-xl bg-slate-100
                    px-2 py-3 text-xs
                    font-semibold text-slate-700
                ">
                <i data-lucide="heart" class="h-5 w-5"></i>
                Favorieten
            </a>

            <a href="/cart" class="
                    flex flex-col items-center gap-2
                    rounded-xl bg-slate-100
                    px-2 py-3 text-xs
                    font-semibold text-slate-700
                ">
                <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                Winkelwagen
            </a>
        </div>

        @auth
            <div class="mt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="
                            flex w-full items-center justify-center gap-2
                            rounded-xl border border-red-200
                            bg-red-50 px-4 py-3 text-sm
                            font-bold text-red-600
                            transition hover:bg-red-100
                        ">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Uitloggen
                    </button>
                </form>
            </div>
        @endauth
    </div>
</aside>

<!-- Search -->
<div id="searchOverlay" class="
        search-overlay fixed inset-0 z-[80]
        flex items-start justify-center
        bg-brand-950/80
        px-4 pt-24
        backdrop-blur-sm
    ">
    <div class="
            w-full max-w-2xl
            rounded-3xl bg-white
            p-5 shadow-2xl
            sm:p-7
        ">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    Zoeken
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Zoek naar producten of diensten.
                </p>
            </div>

            <button id="closeSearch" type="button" class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-xl bg-slate-100
                ">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form action="/zoeken" class="mt-6">
            <div class="relative">
                <i data-lucide="search" class="
                        absolute left-4 top-1/2
                        h-5 w-5 -translate-y-1/2
                        text-slate-400
                    "></i>

                <input id="searchInput" type="search" name="q" placeholder="{{ $c['header']['search_placeholder'] ?? 'Bijvoorbeeld: laptop reparatie' }}" class="
                        h-14 w-full rounded-2xl
                        border border-slate-300
                        bg-slate-50
                        pl-12 pr-28
                        outline-none
                        focus:border-brand-500
                        focus:ring-4
                        focus:ring-brand-500/10
                    ">

                <button type="submit" class="
                        absolute right-2 top-2
                        h-10 rounded-xl
                        bg-brand-700 px-5
                        font-bold text-white
                    ">
                    Zoeken
                </button>
            </div>
        </form>
    </div>
</div>


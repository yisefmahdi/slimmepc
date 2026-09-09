@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/js/vendor/alpine.min.js') }}" defer></script>

    <style>
        .webshop-root, .webshop-root button, .webshop-root input, .webshop-root select, .webshop-root textarea {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        }
        .webshop-root .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .65s ease, transform .65s cubic-bezier(.2,.75,.2,1);
        }
        .webshop-root .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }
        .account-card {
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }
        .account-card:hover {
            border-color: #bfdbfe;
            box-shadow: 0 18px 42px rgba(15,23,42,.08);
        }
        .account-link {
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .account-link:hover {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }
    </style>

    <main class="webshop-root bg-[#f8fafc] text-slate-900">

        {{-- HERO --}}
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid lg:grid-cols-[1fr_auto] items-center gap-5 min-h-[220px] py-7">
                    <div class="reveal">
                        <div class="text-[12px] text-slate-500 flex items-center gap-2">
                            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            <span class="text-slate-700">Mijn account</span>
                        </div>
                        <span class="mt-4 inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.08em] text-blue-600">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Mijn account
                        </span>
                        <h1 class="mt-2 text-[38px] sm:text-[44px] font-black tracking-[-.04em] leading-none text-[#0b1734]">
                            Hallo, {{ strtok(auth()->user()->name, ' ') }}
                        </h1>
                        <p class="mt-4 max-w-[420px] text-[14px] sm:text-[15px] leading-6 text-slate-500">
                            Beheer je gegevens, wachtwoord en bekijk je bestellingen.
                        </p>
                    </div>
                    <div class="reveal">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-trust-card">
                            <div class="flex gap-3">
                                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <i data-lucide="user-round" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-[13px] font-extrabold break-all">
                                        {{ auth()->user()->email }}
                                    </h3>
                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        Lid sinds {{ auth()->user()->created_at->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid gap-6 lg:grid-cols-[1fr_320px]">

                    {{-- FORMS --}}
                    <div class="space-y-6">
                        <div class="account-card reveal rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>

                        <div class="account-card reveal rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                            @include('profile.partials.update-password-form')
                        </div>

                        <div class="account-card reveal rounded-2xl border border-rose-100 bg-white p-5 sm:p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                    {{-- SIDE NAV --}}
                    <aside class="space-y-6">
                        <div class="reveal rounded-2xl border border-slate-200 bg-white p-4 lg:sticky lg:top-6">
                            <p class="px-2 text-[11px] font-bold uppercase tracking-[.08em] text-slate-400">Snel naar</p>
                            <nav class="mt-2 space-y-1.5">
                                <a href="{{ route('account.orders.index') }}" class="account-link flex items-center gap-3 rounded-xl border border-transparent px-3 py-3 text-[13px] font-bold text-slate-700">
                                    <span class="flex w-9 h-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                        <i data-lucide="package" class="w-4 h-4"></i>
                                    </span>
                                    Mijn bestellingen
                                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-slate-300"></i>
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="account-link flex items-center gap-3 rounded-xl border border-transparent px-3 py-3 text-[13px] font-bold text-slate-700">
                                    <span class="flex w-9 h-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                        <i data-lucide="heart" class="w-4 h-4"></i>
                                    </span>
                                    Verlanglijstje
                                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-slate-300"></i>
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="account-link flex w-full items-center gap-3 rounded-xl border border-transparent px-3 py-3 text-[13px] font-bold text-rose-600">
                                        <span class="flex w-9 h-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                        </span>
                                        Uitloggen
                                    </button>
                                </form>
                            </nav>
                        </div>

                    </aside>

                </div>
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();

            const revealItems = document.querySelectorAll('.webshop-root .reveal');
            const revealObserver = new IntersectionObserver(
                entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('show');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.05, rootMargin: '0px 0px -10px 0px' }
            );
            revealItems.forEach((element, index) => {
                element.style.transitionDelay = `${Math.min((index % 5) * 40, 160)}ms`;
                revealObserver.observe(element);
            });
            setTimeout(() => {
                revealItems.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight) el.classList.add('show');
                });
            }, 100);
        });
    </script>
@endsection

@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="relative overflow-hidden bg-gradient-to-b from-white via-[#f8fbff] to-[#eef5ff]">
        <div class="pointer-events-none absolute -left-32 top-10 h-[420px] w-[420px] rounded-full bg-blue-400/15 blur-[130px]"></div>
        <div class="pointer-events-none absolute -right-32 bottom-10 h-[420px] w-[420px] rounded-full bg-cyan-300/15 blur-[140px]"></div>

        <div class="relative mx-auto flex min-h-[68vh] max-w-[1500px] items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
            <div class="w-full max-w-xl rounded-[28px] border border-blue-100/80 bg-white/80 px-6 py-10 text-center shadow-[0_25px_80px_rgba(37,99,235,.13)] backdrop-blur-xl sm:px-10 sm:py-12">

                <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-3">
                    @if (!empty($c['header']['logo_image'] ?? null))
                        <img src="{{ asset($c['header']['logo_image']) }}"
                             alt="{{ $c['header']['logo_text'] ?? 'Slimme-PC' }}"
                             class="h-20 w-20 rounded-2xl bg-white object-contain p-2 shadow-lg shadow-blue-500/20 ring-1 ring-blue-100">
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-2xl bg-brand-gradient-br text-3xl font-black text-white shadow-lg shadow-blue-500/20">
                            {{ mb_strtoupper(mb_substr($c['header']['logo_text'] ?? 'S', 0, 1)) }}
                        </span>
                    @endif
                    <span>
                        <span class="block text-lg font-extrabold tracking-tight text-brand-heading">
                            {{ $c['header']['logo_text'] ?? 'SLIMME-PC' }}
                        </span>
                        @if (!empty($c['header']['tagline'] ?? null))
                            <span class="mt-0.5 block text-xs font-medium text-slate-500">
                                {{ $c['header']['tagline'] }}
                            </span>
                        @endif
                    </span>
                </a>

                <div class="gradient-text mt-6 text-7xl font-black tracking-tight sm:text-8xl">403</div>

                <h1 class="mt-3 text-2xl font-extrabold text-brand-heading sm:text-3xl">
                    Geen toegang tot deze pagina
                </h1>
                <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-500 sm:text-[15px]">
                    Deze pagina is niet beschikbaar voor uw account. Log in met een account dat wel toegang heeft,
                    of ga terug naar de homepage.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ url('/') }}"
                       class="bg-brand-gradient-btn inline-flex h-12 items-center gap-2 rounded-xl px-7 text-sm font-extrabold text-white">
                        <i data-lucide="home" class="h-5 w-5"></i>
                        Naar de homepage
                    </a>
                    <button type="button" onclick="history.back()"
                            class="inline-flex h-12 items-center gap-2 rounded-xl border border-slate-200 bg-white px-7 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="arrow-left" class="h-5 w-5"></i>
                        Ga terug
                    </button>
                </div>

                <div class="mt-8 flex items-center justify-center gap-2 text-xs text-slate-400">
                    <i data-lucide="shield-check" class="h-4 w-4"></i>
                    <span>Foutcode 403 &middot; <a href="{{ url('/contact') }}" class="font-semibold text-brand-700 hover:underline">Neem contact met ons op</a> als u denkt dat dit een fout is.</span>
                </div>
            </div>
        </div>
    </main>

    @include('landing.partials.footer')
@endsection

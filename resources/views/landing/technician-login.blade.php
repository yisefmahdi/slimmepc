@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        <section class="relative min-h-screen overflow-hidden bg-[#f7faff] px-4 py-8">
            <div class="pointer-events-none absolute -left-48 top-20 h-[520px] w-[520px] rounded-full bg-blue-200/35 blur-[120px]"></div>
            <div class="pointer-events-none absolute -right-48 top-32 h-[500px] w-[500px] rounded-full bg-sky-100/70 blur-[120px]"></div>
            <div class="pointer-events-none absolute bottom-[-180px] left-[12%] h-[420px] w-[420px] rounded-full bg-blue-100/60 blur-[100px]"></div>

            <div class="relative z-10 flex min-h-[calc(100vh-4rem)] items-center justify-center">
                <div class="w-full max-w-[560px] rounded-[28px] border border-white/80 bg-white/95 px-6 py-7 shadow-[0_25px_80px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:px-8 sm:py-8">

                    <div class="mb-6 flex justify-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}" alt="Slimme-PC" class="w-[100px] object-contain">
                        </a>
                    </div>

                    <div class="mb-7 text-center">
                        <h1 class="text-3xl font-bold tracking-tight text-[#071b46] sm:text-[34px]">Monteur login</h1>
                        <p class="mt-2 text-sm text-slate-500 sm:text-[15px]">Log in en vul het klantnummer van de klant in</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('technician.login.submit') }}" method="POST" class="grid grid-cols-1 gap-x-7 gap-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-[#071b46]">E-mailadres *</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                </span>
                                <input id="email" name="email" type="email" autocomplete="email" required placeholder="monteur@slimme-pc.nl" value="{{ old('email') }}"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-[#071b46]">Wachtwoord *</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><rect x="5" y="10" width="14" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                                </span>
                                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Wachtwoord"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            </div>
                        </div>

                        <div>
                            <label for="klantnummer" class="mb-1.5 block text-sm font-medium text-[#071b46]">Klantnummer *</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><path d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                </span>
                                <input id="klantnummer" name="klantnummer" type="text" required placeholder="SMP-XXXXX000000" value="{{ old('klantnummer') }}"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm uppercase text-[#071b46] outline-none transition placeholder:normal-case placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Nieuwe klant? <a href="{{ route('register') }}?nieuwe-klant=1" target="_blank" rel="noopener" class="font-semibold text-blue-600 hover:underline">Maak hier een account aan</a>.</p>
                        </div>

                        <button type="submit" data-loading data-loading-text="Bezig met inloggen..."
                            class="mt-1 flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-semibold text-white shadow-[0_12px_28px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_16px_32px_rgba(0,91,234,0.32)] disabled:opacity-60">
                            <span>Inloggen</span>
                        </button>
                    </form>

                </div>
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
    @include('landing.partials.ai-chat')
@endsection

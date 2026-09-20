@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        <section class="relative min-h-screen overflow-hidden bg-[#f7faff] px-4 py-8">

            <!-- Achtergrond effecten -->
            <div class="pointer-events-none absolute -left-48 top-20 h-[520px] w-[520px] rounded-full bg-blue-200/35 blur-[120px]"></div>
            <div class="pointer-events-none absolute -right-48 top-32 h-[500px] w-[500px] rounded-full bg-sky-100/70 blur-[120px]"></div>
            <div class="pointer-events-none absolute bottom-[-180px] left-[12%] h-[420px] w-[420px] rounded-full bg-blue-100/60 blur-[100px]"></div>

            <div class="relative z-10 flex min-h-[calc(100vh-4rem)] items-center justify-center">

                <!-- Form card -->
                <div class="w-full max-w-[760px] rounded-[28px] border border-white/80 bg-white/95 px-6 py-7 shadow-[0_25px_80px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:px-8 sm:py-8">

                    <!-- Logo -->
                    <div class="mb-6 flex justify-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}" alt="Slimme-PC" class="w-[100px] object-contain">
                        </a>
                    </div>

                    <!-- Titel -->
                    <div class="mb-7 text-center">
                        <h1 class="text-3xl font-bold tracking-tight text-[#071b46] sm:text-[34px]">Lid worden</h1>
                        <p class="mt-2 text-sm text-slate-500 sm:text-[15px]">Meld je eenvoudig aan als klant van Slimme-PC</p>
                        <p class="mx-auto mt-3 inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-1.5 text-sm font-bold text-blue-700">
                            €{{ number_format($price, 2, ',', '.') }} per jaar
                            <span class="font-medium text-blue-500">(incl. btw)</span>
                        </p>
                    </div>

                    @if ($errors->has('price') || $errors->has('payment'))
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $errors->first('price') ?: $errors->first('payment') }}
                        </div>
                    @endif

                    <!-- Formulier -->
                    <form action="{{ route('lidmaatschap.store') }}" method="POST" data-loading>
                        @csrf

                        <div class="grid grid-cols-1 gap-x-7 gap-y-5 md:grid-cols-2">

                            <!-- Lidmaatschap -->
                            <div>
                                <label for="membership" class="mb-1.5 block text-sm font-medium text-[#071b46]">Klant lidmaatschap *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
                                    </span>
                                    <select id="membership" name="customer_type" required
                                        class="h-[46px] w-full appearance-none rounded-xl border border-slate-300 bg-white pl-11 pr-10 text-sm text-slate-600 outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                        <option value="" selected disabled>Hoe wil je lid worden?</option>
                                        <option value="particulier" {{ old('customer_type') === 'particulier' ? 'selected' : '' }}>Particulier</option>
                                        <option value="zakelijk" {{ old('customer_type') === 'zakelijk' ? 'selected' : '' }}>Zakelijk</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path d="m6 9 6 6 6-6"></path></svg>
                                    </span>
                                </div>
                                @error('customer_type')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Geslacht -->
                            <div>
                                <label for="gender" class="mb-1.5 block text-sm font-medium text-[#071b46]">Geslacht *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><circle cx="10" cy="14" r="5"></circle><path d="M14 10l5-5"></path><path d="M15 5h4v4"></path></svg>
                                    </span>
                                    <select id="gender" name="customer_gender" required
                                        class="h-[46px] w-full appearance-none rounded-xl border border-slate-300 bg-white pl-11 pr-10 text-sm text-slate-600 outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                        <option value="" selected disabled>Kies uw geslacht</option>
                                        <option value="man" {{ old('customer_gender') === 'man' ? 'selected' : '' }}>Man</option>
                                        <option value="vrouw" {{ old('customer_gender') === 'vrouw' ? 'selected' : '' }}>Vrouw</option>
                                        <option value="anders" {{ old('customer_gender') === 'anders' ? 'selected' : '' }}>Anders</option>
                                        <option value="niet-zeggen" {{ old('customer_gender') === 'niet-zeggen' ? 'selected' : '' }}>Zeg ik liever niet</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path d="m6 9 6 6 6-6"></path></svg>
                                    </span>
                                </div>
                                @error('customer_gender')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Naam -->
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-[#071b46]">Voornaam & Achternaam *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
                                    </span>
                                    <input id="name" name="name" type="text" autocomplete="name" required placeholder="Voer uw naam in" value="{{ old('name') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-[#071b46]">E-mailadres *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                                    </span>
                                    <input id="email" name="customer_email" type="email" autocomplete="email" required placeholder="Voer uw e-mailadres in" value="{{ old('customer_email') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('customer_email')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Telefoon -->
                            <div>
                                <label for="phone" class="mb-1.5 block text-sm font-medium text-[#071b46]">Telefoon *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"></path></svg>
                                    </span>
                                    <input id="phone" name="customer_phone" type="tel" autocomplete="tel" required placeholder="0612345678" value="{{ old('customer_phone') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('customer_phone')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Adres -->
                            <div>
                                <label for="address" class="mb-1.5 block text-sm font-medium text-[#071b46]">Het adres *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5 10.5V21h14V10.5"></path><path d="M9 21v-6h6v6"></path></svg>
                                    </span>
                                    <input id="address" name="customer_address" type="text" autocomplete="street-address" required placeholder="Straat en huisnummer" value="{{ old('customer_address') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('customer_address')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Postcode -->
                            <div>
                                <label for="postcode" class="mb-1.5 block text-sm font-medium text-[#071b46]">Postcode *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><path d="M20.59 13.41 11 3.83V3H4v7h.83l9.58 9.59a2 2 0 0 0 2.82 0l3.36-3.36a2 2 0 0 0 0-2.82Z"></path><circle cx="7.5" cy="6.5" r=".5" fill="currentColor"></circle></svg>
                                    </span>
                                    <input id="postcode" name="postcode" type="text" autocomplete="postal-code" required placeholder="1234 AB" value="{{ old('postcode') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm uppercase text-[#071b46] outline-none transition placeholder:normal-case placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('postcode')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Stad -->
                            <div>
                                <label for="city" class="mb-1.5 block text-sm font-medium text-[#071b46]">Stad *</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]"><path d="M4 21V9l6-3v15"></path><path d="M10 21V3l10 4v14"></path><path d="M7 12h.01"></path><path d="M7 15h.01"></path><path d="M14 9h.01"></path><path d="M17 10h.01"></path><path d="M14 13h.01"></path><path d="M17 14h.01"></path><path d="M14 17h.01"></path><path d="M17 18h.01"></path></svg>
                                    </span>
                                    <input id="city" name="city" type="text" autocomplete="address-level2" required placeholder="Uw stad" value="{{ old('city') }}"
                                        class="h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                </div>
                                @error('city')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>

                        </div>

                        <!-- Voorwaarden -->
                        <label class="mt-6 flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="terms" value="1" required
                                class="mt-[2px] h-5 w-5 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-200">
                            <span class="text-sm leading-5 text-slate-700">
                                Ik ga akkoord met de
                                <a href="{{ route('voorwaarden') }}" target="_blank" rel="noopener" class="font-semibold text-blue-600 hover:underline">voorwaarden</a>
                                en het
                                <a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="font-semibold text-blue-600 hover:underline">privacybeleid</a>.
                            </span>
                        </label>
                        @error('terms')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror

                        <!-- Submit -->
                        <button type="submit" data-loading data-loading-text="Bezig met aanmelden..."
                            class="mt-6 flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-semibold text-white shadow-[0_12px_28px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_16px_32px_rgba(0,91,234,0.32)] disabled:opacity-60">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path d="M15 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8" cy="7" r="4"></circle><path d="M19 8v6"></path><path d="M22 11h-6"></path></svg>
                            <span>Aanmelden & betalen — €{{ number_format($price, 2, ',', '.') }}</span>
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

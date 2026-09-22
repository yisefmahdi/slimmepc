<x-guest-layout>
    <x-auth-card>
        {{-- Logo --}}
        <div class="mb-5 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-5 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="text-[26px] font-bold tracking-tight" style="color: var(--c-heading)">
                Monteur login
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Log in en vul het klantnummer van de klant in
            </p>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 fade-in-up" style="animation-delay: 120ms">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 fade-in-up" style="animation-delay: 120ms">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('technician.login.submit') }}" method="POST" data-loading class="grid grid-cols-1 gap-y-5 fade-in-up" style="animation-delay: 160ms">
            @csrf

            <div>
                <x-input-label for="email" :compact="true">E-mailadres</x-input-label>
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    autofocus
                    placeholder="monteur@slimme-pc.nl"
                    :value="old('email')"
                    :compact="true"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                    </x-slot>
                </x-text-input>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :compact="true">Wachtwoord</x-input-label>
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    placeholder="Wachtwoord"
                    toggle
                    :compact="true"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>
                    </x-slot>
                </x-text-input>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="klantnummer" :compact="true">Klantnummer</x-input-label>
                <x-text-input
                    id="klantnummer"
                    name="klantnummer"
                    type="text"
                    required
                    placeholder="SLP-000000"
                    :value="old('klantnummer')"
                    :compact="true"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </x-slot>
                </x-text-input>
                <x-input-error :messages="$errors->get('klantnummer')" class="mt-2" />
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Nieuwe klant? <a href="{{ route('register') }}?nieuwe-klant" target="_blank" rel="noopener" class="font-semibold text-blue-600 hover:underline dark:text-blue-400">Maak hier een account aan</a>.</p>
            </div>

            {{-- Submit --}}
            <x-primary-button :compact="true" data-loading-text="Bezig met inloggen...">
                Inloggen
            </x-primary-button>
        </form>
    </x-auth-card>
</x-guest-layout>

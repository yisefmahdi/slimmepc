<x-guest-layout>
    <x-auth-card :max-width="'440px'">
        {{-- Logo --}}
        <div class="mb-7 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-6 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="auth-title">Wachtwoord herstellen</h1>
            <p class="auth-subtitle mt-1">Kies een nieuw wachtwoord voor je account</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.store') }}" data-loading class="space-y-5 fade-in-up" style="animation-delay: 160ms">
            @csrf

            {{-- Password Reset Token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div>
                <x-input-label for="email">E-mailadres</x-input-label>

                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    autofocus
                    placeholder="Vul je e-mailadres in"
                    :value="old('email', $request->email)"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password">Nieuw wachtwoord</x-input-label>

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Kies een nieuw wachtwoord"
                    toggle
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                            <path d="M12 14v3"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm password --}}
            <div>
                <x-input-label for="password_confirmation">Bevestig wachtwoord</x-input-label>

                <x-text-input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Herhaal het wachtwoord"
                    toggle
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                            <path d="M12 14v3"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button data-loading-text="Wachtwoord herstellen...">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                    <path d="M15 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8" cy="7" r="4"></circle>
                    <path d="M19 8v6"></path>
                    <path d="M22 11h-6"></path>
                </svg>
                Wachtwoord herstellen
            </x-primary-button>
        </form>
    </x-auth-card>
</x-guest-layout>


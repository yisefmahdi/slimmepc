<x-guest-layout>
    <x-auth-card :max-width="'440px'">
        {{-- Logo --}}
        <div class="mb-7 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-6 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="auth-title">Bevestig je wachtwoord</h1>
            <p class="auth-subtitle mt-1">
                Dit is een beveiligd gedeelte van de applicatie. Bevestig je wachtwoord voordat je verder gaat.
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.confirm') }}" data-loading class="space-y-5 fade-in-up" style="animation-delay: 160ms">
            @csrf

            <div>
                <x-input-label for="password">Wachtwoord</x-input-label>

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    autofocus
                    placeholder="Vul je wachtwoord in"
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

            <x-primary-button data-loading-text="Bevestigen...">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Bevestigen
            </x-primary-button>
        </form>
    </x-auth-card>
</x-guest-layout>

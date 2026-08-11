<x-guest-layout>
    <x-auth-card :max-width="'440px'">
        {{-- Logo --}}
        <div class="mb-7 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-6 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="auth-title">Wachtwoord vergeten?</h1>
            <p class="auth-subtitle mt-1">
                Geen probleem. Laat ons je e-mailadres weten en we sturen je een link om je wachtwoord te herstellen.
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status :status="session('status')" />

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" data-loading class="space-y-5 fade-in-up" style="animation-delay: 160ms">
            @csrf

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
                    :value="old('email')"
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

            <x-primary-button data-loading-text="Link versturen...">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                    <path d="m22 2-7 20-4-9-9-4Z"></path>
                    <path d="M22 2 11 13"></path>
                </svg>
                Stuur wachtwoord reset link
            </x-primary-button>
        </form>

        {{-- Back to login --}}
        <div class="mt-7 text-center text-sm fade-in-up" style="animation-delay: 240ms; color: var(--c-body)">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 link-primary" data-loading>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M19 12H5"></path>
                    <path d="m11 18-6-6 6-6"></path>
                </svg>
                Terug naar inloggen
            </a>
        </div>
    </x-auth-card>
</x-guest-layout>

<x-guest-layout>
    <x-auth-card>
        {{-- Logo --}}
        <div class="mb-7 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-7 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="auth-title">Welkom terug</h1>
            <p class="auth-subtitle">Log in op je Slimme-PC account</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status :status="session('status')" />

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}" data-loading class="space-y-5 fade-in-up" style="animation-delay: 160ms">
            @csrf

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

            {{-- Password --}}
            <div>
                <x-input-label for="password">Wachtwoord</x-input-label>

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
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

            {{-- Remember + forgot --}}
            <div class="flex items-center justify-between gap-4 text-sm">
                <label class="flex cursor-pointer items-center gap-2.5" style="color: var(--c-heading)">
                    <input
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-200 dark:border-slate-600 dark:bg-slate-800 dark:focus:ring-blue-900/40"
                    >
                    <span>Onthoud mij</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-muted" data-loading>
                        Wachtwoord vergeten?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <x-primary-button data-loading-text="Inloggen...">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                    <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                </svg>
                Inloggen
            </x-primary-button>
        </form>

        {{-- Register link --}}
        <div class="mt-7 text-center text-sm fade-in-up" style="animation-delay: 240ms; color: var(--c-body)">
            Nog geen account?

            <a href="{{ route('register') }}" class="ml-1 inline-flex items-center gap-1.5 link-primary" data-loading>
                Account aanmaken
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M5 12h14"></path>
                    <path d="m13 6 6 6-6 6"></path>
                </svg>
            </a>
        </div>
    </x-auth-card>
</x-guest-layout>


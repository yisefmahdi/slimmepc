<x-guest-layout>
    <x-auth-card :max-width="'440px'">
        {{-- Logo --}}
        <div class="mb-7 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-6 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="auth-title">Verifieer je e-mail</h1>
            <p class="auth-subtitle mt-1">
                Bedankt voor het aanmelden! Klik op de link die we per e-mail hebben gestuurd om je e-mailadres te verifiëren.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div data-alert data-auto-dismiss class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-400 fade-in-up">
                Een nieuwe verificatielink is naar je e-mailadres gestuurd.
            </div>
        @endif

        <div class="space-y-3 fade-in-up" style="animation-delay: 160ms">
            {{-- Resend verification --}}
            <form method="POST" action="{{ route('verification.send') }}" data-loading>
                @csrf

                <x-primary-button data-loading-text="Versturen...">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                        <path d="M12 3v3M5.6 5.6l2.1 2.1M3 12h3"></path>
                        <path d="M21 12a9 9 0 1 1-18 0"></path>
                        <path d="m15 9 6 6-6 6"></path>
                    </svg>
                    Verstuur verificatie-e-mail opnieuw
                </x-primary-button>
            </form>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" data-loading>
                @csrf

                <button type="submit" data-loading class="w-full text-center text-sm font-medium text-slate-500 transition hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
                    Uitloggen
                </button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>

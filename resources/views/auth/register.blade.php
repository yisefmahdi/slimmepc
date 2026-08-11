<x-guest-layout>
    <x-auth-card :max-width="'390px'">
        {{-- Logo --}}
        <div class="mb-5 flex justify-center fade-in-up">
            <x-logo :size="100" />
        </div>

        {{-- Heading --}}
        <div class="mb-5 text-center fade-in-up" style="animation-delay: 80ms">
            <h1 class="text-[26px] font-bold tracking-tight" style="color: var(--c-heading)">
                Account aanmaken
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Maak je Slimme-PC account aan
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('register') }}" data-loading class="space-y-3 fade-in-up" style="animation-delay: 160ms">
            @csrf

            {{-- Name --}}
            <div>
                <x-input-label for="name" :compact="true">Voornaam & Achternaam</x-input-label>

                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    required
                    autofocus
                    placeholder="Vul je volledige naam in"
                    :value="old('name')"
                    :compact="true"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Phone --}}
            <div>
                <x-input-label for="phone" :compact="true">Telefoon</x-input-label>

                <x-text-input
                    id="phone"
                    name="phone"
                    type="tel"
                    autocomplete="tel"
                    placeholder="Vul je telefoonnummer in"
                    :value="old('phone')"
                    :compact="true"
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="email" :compact="true">E-mailadres</x-input-label>

                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    placeholder="Vul je e-mailadres in"
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

            {{-- Street + House number --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <x-input-label for="street" :compact="true">Straat</x-input-label>

                    <x-text-input
                        id="street"
                        name="street"
                        type="text"
                        autocomplete="street-address"
                        placeholder="Straatnaam"
                        :value="old('street')"
                        :compact="true"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"></path>
                            </svg>
                        </x-slot>
                    </x-text-input>

                    <x-input-error :messages="$errors->get('street')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="house_number" :compact="true">Huisnummer</x-input-label>

                    <x-text-input
                        id="house_number"
                        name="house_number"
                        type="text"
                        autocomplete="address-line2"
                        placeholder="123"
                        :value="old('house_number')"
                        :compact="true"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path d="M11.25 21v-5.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-7.5 0H19.5m-14.25 0h1.5M5.25 21h1.5M3.75 21h1.5m4.5-10.5h.008v.008h-.008V10.5Zm3 0h.008v.008h-.008V10.5Zm3 0h.008v.008h-.008V10.5Zm-9 0h.008v.008H6.75V10.5Zm3 3h.008v.008h-.008V13.5Zm3 0h.008v.008h-.008V13.5Zm3 0h.008v.008h-.008V13.5Zm-9 0h.008v.008H6.75V13.5Zm3 3h.008v.008h-.008V16.5Zm3 0h.008v.008h-.008V16.5Zm3 0h.008v.008h-.008V16.5Zm-9 0h.008v.008H6.75V16.5Z"></path>
                            </svg>
                        </x-slot>
                    </x-text-input>

                    <x-input-error :messages="$errors->get('house_number')" class="mt-2" />
                </div>
            </div>

            {{-- Postcode + City --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="postcode" :compact="true">Postcode</x-input-label>

                    <x-text-input
                        id="postcode"
                        name="postcode"
                        type="text"
                        autocomplete="postal-code"
                        placeholder="1234 AB"
                        :value="old('postcode')"
                        :compact="true"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path>
                            </svg>
                        </x-slot>
                    </x-text-input>

                    <x-input-error :messages="$errors->get('postcode')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="city" :compact="true">Stad</x-input-label>

                    <x-text-input
                        id="city"
                        name="city"
                        type="text"
                        autocomplete="address-level2"
                        placeholder="Bijv. Utrecht"
                        :value="old('city')"
                        :compact="true"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                <path d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"></path>
                            </svg>
                        </x-slot>
                    </x-text-input>

                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password" :compact="true">Wachtwoord</x-input-label>

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Kies een wachtwoord"
                    :compact="true"
                    toggle
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

            {{-- Confirm password --}}
            <div>
                <x-input-label for="password_confirmation" :compact="true">Wachtwoord herhalen</x-input-label>

                <x-text-input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Herhaal je wachtwoord"
                    :compact="true"
                    toggle
                >
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <rect x="5" y="10" width="14" height="11" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>
                    </x-slot>
                </x-text-input>

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Terms --}}
            <label class="flex cursor-pointer items-start gap-2 pt-1">
                <input
                    type="checkbox"
                    name="terms"
                    required
                    class="mt-[2px] h-4 w-4 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-200 dark:border-slate-600 dark:bg-slate-800 dark:focus:ring-blue-900/40"
                >

                <span class="text-[12px] leading-[18px]" style="color: var(--c-body)">
                    Ik ga akkoord met de
                    <a href="#" class="font-medium text-blue-600 hover:underline dark:text-blue-400">voorwaarden</a>
                    en het
                    <a href="#" class="font-medium text-blue-600 hover:underline dark:text-blue-400">privacybeleid</a>.
                </span>
            </label>

            {{-- Submit --}}
            <x-primary-button :compact="true" data-loading-text="Account aanmaken...">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]">
                    <path d="M15 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8" cy="7" r="4"></circle>
                    <path d="M19 8v6"></path>
                    <path d="M22 11h-6"></path>
                </svg>
                Account aanmaken
            </x-primary-button>
        </form>

        {{-- Login link --}}
        <p class="mt-5 text-center text-[13px] fade-in-up" style="animation-delay: 240ms; color: var(--c-body)">
            Heb je al een account?

            <a href="{{ route('login') }}" class="ml-1 font-semibold text-blue-600 hover:underline dark:text-blue-400" data-loading>
                Inloggen →
            </a>
        </p>
    </x-auth-card>
</x-guest-layout>

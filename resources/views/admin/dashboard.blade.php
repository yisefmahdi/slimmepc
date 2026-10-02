<x-admin.layout title="Dashboard">
    @php $isTechDash = auth()->user()?->isTechnician() ?? false; @endphp
    @if($isTechDash)
    {{-- Welcome banner --}}
    <div class="mt-1 overflow-hidden rounded-2xl border bg-gradient-to-r from-[#075be8] to-[#064bd7] p-6 text-white shadow-[0_12px_25px_rgba(0,91,234,0.25)] sm:p-8">
        <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Welkom terug, {{ Auth::user()->name }}!</h2>
        <p class="mt-1 text-sm text-blue-100">Je werkplek voor ontvangsten. Kies hieronder een categorie.</p>
    </div>
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'laptop']) }}" class="rounded-2xl border p-6 transition hover:-translate-y-0.5 hover:shadow-lg" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <p class="text-base font-extrabold" style="color: var(--c-heading)">Laptops-PC</p>
            <p class="mt-1 text-xs" style="color: var(--c-muted)">Bekijken, aanmaken en status wijzigen.</p>
            <span class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white">Openen</span>
        </a>
        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'ipad_iphone']) }}" class="rounded-2xl border p-6 transition hover:-translate-y-0.5 hover:shadow-lg" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <p class="text-base font-extrabold" style="color: var(--c-heading)">iPad-iPhone</p>
            <p class="mt-1 text-xs" style="color: var(--c-muted)">Bekijken, aanmaken en status wijzigen.</p>
            <span class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white">Openen</span>
        </a>
        <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => 'playstation_xbox']) }}" class="rounded-2xl border p-6 transition hover:-translate-y-0.5 hover:shadow-lg" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <p class="text-base font-extrabold" style="color: var(--c-heading)">PlayStation-Xbox</p>
            <p class="mt-1 text-xs" style="color: var(--c-muted)">Bekijken, aanmaken en status wijzigen.</p>
            <span class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white">Openen</span>
        </a>
    </div>
    @else
    {{-- Title (like the old system) --}}
    <h2 class="mt-1 text-center text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">Welkom terug, {{ Auth::user()->name }}!</h2>

    {{-- Push permission banner (admins only, shown by push.js when permission is undecided) --}}
    @if(auth()->user()?->isAdmin())
    <div id="pushBanner" style="display:none" class="mx-auto mt-4 flex max-w-3xl flex-wrap items-center justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-3.5">
        <p class="text-sm font-semibold text-blue-900">🔔 Ontvang direct een melding bij elke nieuwe aanvraag, chat of bestelling — ook als je e-mail nog dicht is.</p>
        <div class="flex items-center gap-2">
            <button type="button" id="pushBannerEnable" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Inschakelen</button>
            <button type="button" id="pushBannerLater" class="rounded-xl border border-blue-300 px-4 py-2 text-xs font-bold text-blue-800">Later</button>
        </div>
    </div>
    @endif

    {{-- Stats only (9 small cards, like the old system) --}}
    <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-3">
        <a href="{{ route('admin.users.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Klanten" :value="$stats['customers']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Bestellingen" :value="$stats['orders']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.webshop.products.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Producten" :value="$stats['products']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <x-admin.stat-card compact label="Facturen" :value="$stats['invoices']">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </x-slot>
        </x-admin.stat-card>

        <a href="{{ route('admin.afspraak-aanvragen.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Afspraken" :value="$stats['afspraken']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.chat.inbox.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Berichten-live" :value="$stats['chat_unread']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.lidmaatschap.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Abonnement-Lidworden" :value="$stats['memberships']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.bevestiging-mail.ontvangst.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="DeviceReceipt" :value="$stats['device_receipts']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>

        <a href="{{ route('admin.lidmaatschap.index') }}" class="block rounded-xl">
            <x-admin.stat-card compact label="Actieve abonnementen" :value="$stats['memberships_active']">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-admin.stat-card>
        </a>
    </div>

    {{-- Snelle acties --}}
    <div class="mt-6">
    <x-admin.card title="Snelle acties">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
            <a href="{{ route('admin.reken-machine.index') }}" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5a2.25 2.25 0 0 1 2.25 2.25v12a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25V6a2.25 2.25 0 0 1 2.25-2.25ZM8.25 7.5h7.5M8.25 11.25h.008v.008H8.25v-.008Zm3.75 0h.008v.008h-.008v-.008Zm3.75 0h.008v.008h-.008v-.008ZM8.25 15h.008v.008H8.25V15Zm3.75 0h.008v.008h-.008V15Zm3.75 0h.008v.008h-.008V15Zm-7.5 3.75h.008v.008H8.25v-.008Zm3.75 0h.008v.008h-.008v-.008Zm3.75 0h.008v.008h-.008v-.008Z" />
                    </svg>
                </span>
                <span class="text-xs font-semibold" style="color: var(--c-heading)">Rekenmachine</span>
            </a>

            <a href="{{ route('admin.bevestiging-mail.hardware.create') }}" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
                <span class="text-xs font-semibold" style="color: var(--c-heading)">Handmatig Factuur Aanmaken</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="group relative flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                @if (($stats['orders_new'] ?? 0) > 0)
                    <span class="absolute -right-2 -top-2 flex h-6 items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-extrabold text-white shadow" style="min-width:1.5rem">{{ $stats['orders_new'] }}</span>
                @endif
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                    </svg>
                </span>
                <span class="text-xs font-semibold" style="color: var(--c-heading)">Bestellingen</span>
            </a>

            <a href="{{ route('admin.chat.inbox.index') }}" class="group relative flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                @if (($stats['chat_unread'] ?? 0) > 0)
                    <span class="absolute -right-2 -top-2 flex h-6 items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-extrabold text-white shadow" style="min-width:1.5rem">{{ $stats['chat_unread'] }}</span>
                @endif
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                </span>
                <span class="text-xs font-semibold" style="color: var(--c-heading)">AI Chat</span>
            </a>

            <a href="{{ route('admin.contact-inbox.index') }}" class="group relative flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                @if (($stats['contact_new'] ?? 0) > 0)
                    <span class="absolute -right-2 -top-2 flex h-6 items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-extrabold text-white shadow" style="min-width:1.5rem">{{ $stats['contact_new'] }}</span>
                @endif
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </span>
                <span class="text-xs font-semibold" style="color: var(--c-heading)">Contact</span>
            </a>
        </div>
    </x-admin.card>
    </div>
    @endif
</x-admin.layout>

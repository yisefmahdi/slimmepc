<x-admin.layout title="Mailinglijst">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Mailinglijst</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer handmatige e-mailadressen (Klantenlijst).</p>
            </div>
            <a href="{{ route('admin.mailinglist.send.form') }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
                E-mail verzenden
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Add form --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <form action="{{ route('admin.customerlist.store') }}" method="POST" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="ml-email">E-mail <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="ml-email" name="email" type="email" value="{{ old('email') }}" placeholder="naam@voorbeeld.nl" class="h-10 text-sm" />
                        @error('email')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex-1">
                        <x-input-label for="ml-name">Naam (optioneel)</x-input-label>
                        <x-text-input id="ml-name" name="name" value="{{ old('name') }}" placeholder="Voor- en achternaam" class="h-10 text-sm" />
                    </div>
                    <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Toevoegen
                    </button>
                </form>
            </div>

            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                        <input type="text" id="emailSearch" placeholder="Zoek op e-mail of naam..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Totaal: {{ $emails->count() }}</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width: 640px;">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">#</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">E-mail</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam</th>
                            <th class="w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="emailTableBody">
                        @forelse($emails as $email)
                            <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">{{ $email->id }}</td>
                                <td class="px-3 py-3 text-sm font-semibold" style="color:var(--c-heading)">{{ $email->email }}</td>
                                <td class="px-3 py-3 text-sm" style="color:var(--c-heading)">{{ $email->name ?? '—' }}</td>
                                <td class="w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="askDeleteMailing({{ $email->id }})" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-12 text-center text-sm" style="color: var(--c-muted)">Geen e-mails gevonden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-admin.modal id="mailingDeleteModal" title="E-mail verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je dit adres wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <form id="mailingDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
            </form>
        </x-slot>
    </x-admin.modal>

    <script>
        document.getElementById('emailSearch')?.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('#emailTableBody tr').forEach(row => {
                const email = (row.children[1]?.textContent || '').toLowerCase();
                const name = (row.children[2]?.textContent || '').toLowerCase();
                row.style.display = (email.includes(keyword) || name.includes(keyword)) ? '' : 'none';
            });
        });
        function askDeleteMailing(id) {
            document.getElementById('mailingDeleteForm').action = '{{ url('admin/mailinglijst') }}/' + id;
            window.SlimmePC.modal.open('mailingDeleteModal');
        }
    </script>
</x-admin.layout>

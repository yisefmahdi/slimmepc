<x-admin.layout title="E-mail verzenden">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">E-mail verzenden</h1>
            <p class="text-xs" style="color: var(--c-muted)">Verzend een e-mail naar alle gebruikers. Verzending loopt via de wachtrij in delen van 200.</p>
        </div>
        <x-admin.back-button :href="route('admin.customerlist.index')" label="Klantenlijst" />
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.send.mass.email') }}" class="space-y-6">
        @csrf

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Bericht opstellen</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="messageType">Berichttype <span class="text-red-500">*</span></x-input-label>
                        <select name="message_type" id="messageType" required class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="">-- Kies een type --</option>
                            <option value="special_offer" @selected(old('message_type') === 'special_offer')>Speciale aanbieding</option>
                            <option value="marketing" @selected(old('message_type') === 'marketing')>Marketingbericht</option>
                            <option value="maintenance" @selected(old('message_type') === 'maintenance')>Onderhoudsbericht</option>
                            <option value="important_notice" @selected(old('message_type') === 'important_notice')>Belangrijke mededeling</option>
                        </select>
                        @error('message_type')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="messageContent">Berichtinhoud <span class="text-red-500">*</span></x-input-label>
                        <textarea name="message_content" id="messageContent" rows="6" required placeholder="Typ hier het bericht dat je naar alle gebruikers wilt sturen..." class="form-input w-full text-sm">{{ old('message_content') }}</textarea>
                        @error('message_content')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-8 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] hover:-translate-y-0.5">Versturen</button>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Verzonden berichten</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                    <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                        <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Type</th>
                        <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Bericht</th>
                        <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Datum</th>
                        <th class="w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                            <td class="px-3 py-3 text-sm font-semibold whitespace-nowrap" style="color:var(--c-heading)">{{ $email->message_type }}</td>
                            <td class="px-3 py-3 text-sm" style="color:var(--c-heading)">{{ \Str::limit($email->message_content, 120) }}</td>
                            <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">{{ $email->created_at->format('d-m-Y H:i') }}</td>
                            <td class="w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="askDeleteMassEmail({{ $email->id }})" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-12 text-center text-sm" style="color: var(--c-muted)">Er zijn nog geen verzonden berichten.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($emails->hasPages())
            <div class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)">{{ $emails->links() }}</div>
        @endif
    </div>

    <x-admin.modal id="massEmailDeleteModal" title="Bericht verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je dit bericht wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <form id="massEmailDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
            </form>
        </x-slot>
    </x-admin.modal>

    <script>
        function askDeleteMassEmail(id) {
            document.getElementById('massEmailDeleteForm').action = '{{ url('admin/mass-emails') }}/' + id;
            window.SlimmePC.modal.open('massEmailDeleteModal');
        }
    </script>
</x-admin.layout>

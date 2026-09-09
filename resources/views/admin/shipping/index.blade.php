<x-admin.layout title="Verzendopties">
    <style>
        .apple-switch {
            position: relative;
            display: inline-flex;
            height: 24px;
            width: 44px;
            flex-shrink: 0;
            cursor: pointer;
            border-radius: 9999px;
            border: 2px solid transparent;
            transition: background-color 0.2s ease-in-out;
            outline: none;
            padding: 0;
            vertical-align: middle;
        }
        .apple-switch.is-active {
            background-color: #10b981;
        }
        .apple-switch.is-inactive {
            background-color: #cbd5e1;
        }
        .dark .apple-switch.is-inactive {
            background-color: #475569;
        }
        .apple-switch .apple-knob {
            pointer-events: none;
            display: inline-block;
            height: 20px;
            width: 20px;
            border-radius: 9999px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(0px);
        }
        .apple-switch.is-active .apple-knob {
            transform: translateX(20px);
        }
    </style>
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Verzendopties</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer verzendkosten en gratis-verzendgrenzen. <em>pickup</em> is altijd gratis.</p>
            </div>
            <button onclick="openRateModal()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuwe optie
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="min-w-[170px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam</th>
                            <th class="w-[110px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Slug</th>
                            <th class="w-[100px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Prijs</th>
                            <th class="w-[130px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Gratis vanaf</th>
                            <th class="w-[90px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Actief</th>
                            <th class="w-[100px] min-w-[100px] max-w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="rateRows"></tbody>
                </table>
            </div>
        </div>
    </div>

    <x-admin.modal id="rateModal" title="Verzendoptie" subtitle="Prijs en gratis-grens instellen.">
        <form id="rateForm" class="grid gap-4">
            <input type="hidden" id="rateId">
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Naam *</label>
                <input id="rateName" class="form-input h-11 w-full text-sm" placeholder="Standaard verzending">
            </div>
            <div id="slugWrap">
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Slug * (alleen bij aanmaken)</label>
                <input id="rateSlug" class="form-input h-11 w-full text-sm" placeholder="delivery">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Prijs (€) *</label>
                    <input id="ratePrice" type="number" min="0" step="0.01" class="form-input h-11 w-full text-sm" value="6.95">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Gratis vanaf (€)</label>
                    <input id="rateFree" type="number" min="0" step="0.01" class="form-input h-11 w-full text-sm" placeholder="75">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                <input id="rateActive" type="checkbox" checked class="h-4 w-4"> Actief
            </label>
        </form>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" onclick="saveRate()" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white">Opslaan</button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete --}}
    <x-admin.modal id="rateDeleteModal" title="Verzendoptie verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="deleteRateName" class="font-bold">deze optie</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="rateDeleteConfirm" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script>
        const rateToken = document.querySelector('meta[name=csrf-token]')?.content || '';
        const escRate = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        let pendingRateDelete = null;
        let lastRates = [];

        async function loadRates() {
            const rows = document.getElementById('rateRows');
            rows.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-sm" style="color: var(--c-muted)">Gegevens laden…</td></tr>';
            const res = await fetch('{{ route('admin.shipping.data') }}', { headers: { Accept: 'application/json' } });
            const d = await res.json();
            lastRates = d.data;
            rows.innerHTML = d.data.length ? d.data.map(r => `
                <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                    <td class="px-3 py-3 text-sm font-semibold" style="color:var(--c-heading)">${escRate(r.name)}</td>
                    <td class="px-3 py-3 text-xs" style="color:var(--c-muted)">${escRate(r.slug)}</td>
                    <td class="px-3 py-3 text-sm font-bold whitespace-nowrap" style="color:var(--c-heading)">€${Number(r.price).toFixed(2).replace('.', ',')}</td>
                    <td class="px-3 py-3 text-sm whitespace-nowrap" style="color:var(--c-heading)">${r.free_above ? '€' + Number(r.free_above).toFixed(2).replace('.', ',') : '—'}</td>
                    <td class="px-3 py-3"><button type="button" role="switch" aria-checked="${r.is_active ? 'true' : 'false'}" onclick="toggleRate(${r.id}, this)" class="apple-switch ${r.is_active ? 'is-active' : 'is-inactive'}" title="${r.is_active ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)'}"><span class="apple-knob"></span></button></td>
                    <td class="w-[100px] min-w-[100px] max-w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                        <div class="flex items-center justify-end gap-1">
                            <button type="button" onclick='editRate(${JSON.stringify(r).replace(/'/g, "&#39;")})' title="Bewerken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></button>
                            <button type="button" onclick="askDeleteRate(${r.id})" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                        </div>
                    </td>
                </tr>`).join('') : '<tr><td colspan="6" class="px-6 py-16 text-center"><div class="flex flex-col items-center gap-3"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg></span><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen verzendopties gevonden</p></div></td></tr>';
        }
        function openRateModal() {
            document.getElementById('rateForm').reset();
            document.getElementById('rateId').value = '';
            document.getElementById('slugWrap').style.display = '';
            document.getElementById('ratePrice').value = '6.95';
            window.SlimmePC.modal.open('rateModal');
        }
        function editRate(r) {
            document.getElementById('rateId').value = r.id;
            document.getElementById('rateName').value = r.name;
            document.getElementById('slugWrap').style.display = 'none';
            document.getElementById('ratePrice').value = r.price;
            document.getElementById('rateFree').value = r.free_above || '';
            document.getElementById('rateActive').checked = !!r.is_active;
            window.SlimmePC.modal.open('rateModal');
        }
        async function saveRate() {
            const id = document.getElementById('rateId').value;
            const payload = {
                name: document.getElementById('rateName').value,
                price: document.getElementById('ratePrice').value,
                free_above: document.getElementById('rateFree').value || null,
                is_active: document.getElementById('rateActive').checked,
            };
            let url = '{{ route('admin.shipping.store') }}', method = 'POST';
            if (id) { url = '/admin/shipping/' + id; method = 'PUT'; }
            else payload.slug = document.getElementById('rateSlug').value;
            const res = await fetch(url, { method, headers: { 'X-CSRF-TOKEN': rateToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const d = await res.json().catch(() => ({}));
            window.SlimmePC.toast(d.message || (res.ok ? 'Opgeslagen.' : 'Mislukt.'), res.ok ? 'success' : 'error');
            if (res.ok) { window.SlimmePC.modal.close('rateModal'); loadRates(); }
        }
        async function toggleRate(id, btn) {
            const active = btn.classList.contains('is-active');
            btn.className = `apple-switch ${active ? 'is-inactive' : 'is-active'}`;
            btn.setAttribute('aria-checked', active ? 'false' : 'true');
            const res = await fetch('/admin/shipping/' + id + '/toggle', { method: 'POST', headers: { 'X-CSRF-TOKEN': rateToken, 'Accept': 'application/json' } });
            const d = await res.json().catch(() => ({}));
            if (res.ok) { window.SlimmePC.toast(d.message || 'Bijgewerkt.', 'success'); loadRates(); }
            else {
                btn.className = `apple-switch ${active ? 'is-active' : 'is-inactive'}`;
                btn.setAttribute('aria-checked', active ? 'true' : 'false');
                window.SlimmePC.toast(d.message || 'Mislukt.', 'error');
            }
        }
        function askDeleteRate(id) {
            pendingRateDelete = id;
            const r = lastRates.find(x => String(x.id) === String(id));
            document.getElementById('deleteRateName').textContent = r ? r.name : 'deze optie';
            window.SlimmePC.modal.open('rateDeleteModal');
        }
        document.getElementById('rateDeleteConfirm').addEventListener('click', async () => {
            if (!pendingRateDelete) return;
            const res = await fetch('/admin/shipping/' + pendingRateDelete, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': rateToken, 'Accept': 'application/json' } });
            const d = await res.json().catch(() => ({}));
            window.SlimmePC.toast(d.message || 'Verwijderd.', res.ok ? 'success' : 'error');
            if (res.ok) { window.SlimmePC.modal.close('rateDeleteModal'); loadRates(); }
            pendingRateDelete = null;
        });
        loadRates();
    </script>
</x-admin.layout>

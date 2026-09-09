<x-admin.layout title="Bestellingen">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Bestellingen</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Betaalde webshop-bestellingen beheren: status, factuur, verwijderen.</p>
            </div>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                        <input type="text" id="orderSearch" placeholder="Zoeken op nummer, e-mail of telefoon..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="orderStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-44"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="">Alle statussen</option>
                        <option value="pending">Nieuw</option>
                        <option value="processing">In behandeling</option>
                        <option value="shipped">Verzonden</option>
                        <option value="completed">Afgerond</option>
                        <option value="cancelled">Geannuleerd</option>
                    </select>
                    <select id="payStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-36"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="">Alle betalingen</option>
                        <option value="paid">Betaald</option>
                        <option value="pending">Open</option>
                        <option value="failed">Mislukt</option>
                    </select>
                    <select id="orderPerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-[110px] sm:ml-auto"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="15">15 per pagina</option>
                        <option value="10">10 per pagina</option>
                        <option value="25">25 per pagina</option>
                        <option value="50">50 per pagina</option>
                    </select>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-amber-50 px-3 py-1.5 font-bold text-amber-600 dark:bg-amber-900/30 dark:text-amber-400" id="chipPending">Nieuw: 0</span>
                    <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400" id="chipPaid">Betaald: 0</span>
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="chipTotal">Totaal: 0</span>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="min-w-[130px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Nummer</th>
                            <th class="min-w-[180px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Klant</th>
                            <th class="w-[100px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Totaal</th>
                            <th class="w-[110px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Betaling</th>
                            <th class="w-[100px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Type</th>
                            <th class="w-[140px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[130px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Datum</th>
                            <th class="w-[100px] min-w-[100px] max-w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="orderRows"></tbody>
                </table>
            </div>
            <div id="orderPager" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Delete --}}
    <x-admin.modal id="deleteOrderModal" title="Bestelling verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je bestelling <span id="deleteOrderNumber" class="font-bold"></span> wilt verwijderen (incl. factuur)?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="deleteOrderConfirm" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script>
        (function () {
            const rows = document.getElementById('orderRows');
            const pager = document.getElementById('orderPager');
            const search = document.getElementById('orderSearch');
            const fStatus = document.getElementById('orderStatusFilter');
            const fPay = document.getElementById('payStatusFilter');
            const fPerPage = document.getElementById('orderPerPage');
            let page = 1, timer = null, pendingDelete = null;
            const token = document.querySelector('meta[name=csrf-token]')?.content || '';

            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            const statusMap = {
                pending: ['Nieuw', 'amber'],
                processing: ['In behandeling', 'blue'],
                shipped: ['Verzonden', 'indigo'],
                completed: ['Afgerond', 'emerald'],
                cancelled: ['Geannuleerd', 'red'],
            };
            const pillStyles = {
                amber: ['bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400', 'bg-amber-500'],
                blue: ['bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400', 'bg-blue-500'],
                indigo: ['bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400', 'bg-indigo-500'],
                emerald: ['bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400', 'bg-emerald-500'],
                red: ['bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400', 'bg-red-500'],
                slate: ['bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400', 'bg-slate-500'],
            };
            const dotBadge = (label, color) => {
                const s = pillStyles[color] || pillStyles.slate;
                return `<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ${s[0]}"><span class="h-1.5 w-1.5 rounded-full ${s[1]}"></span>${label}</span>`;
            };

            const payBadge = s => s === 'paid'
                ? dotBadge('Betaald', 'emerald')
                : (s === 'failed' ? dotBadge('Mislukt', 'red') : dotBadge('Open', 'amber'));

            const statusBadge = s => {
                const m = statusMap[s] || [s, 'slate'];
                return dotBadge(m[0], m[1]);
            };

            function renderPagination(p) {
                if (!p || p.last <= 1) { pager.innerHTML = ''; return; }
                let html = `<div class="flex flex-wrap items-center justify-between gap-2 text-xs"><span style="color:var(--c-muted)">Pagina ${p.current} van ${p.last} — ${p.total} resultaten</span><div class="flex gap-1">`;
                for (let i = 1; i <= p.last; i++) {
                    const a = i === p.current;
                    html += `<button data-page="${i}" class="min-w-[36px] rounded-lg px-3 py-1.5 font-bold ${a ? 'bg-blue-600 text-white' : 'border bg-white hover:bg-slate-50'}" style="${a ? '' : 'border-color:var(--c-input-border);color:var(--c-heading)'}">${i}</button>`;
                }
                html += `</div></div>`;
                pager.innerHTML = html;
                pager.querySelectorAll('[data-page]').forEach(b => b.addEventListener('click', () => { page = parseInt(b.getAttribute('data-page'), 10); load(); }));
            }

            async function load() {
                rows.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm" style="color: var(--c-muted)">Gegevens laden…</td></tr>';
                const q = new URLSearchParams({ page, search: search.value, order_status: fStatus.value, payment_status: fPay.value, per_page: fPerPage.value });
                const res = await fetch('{{ route('admin.orders.data') }}?' + q.toString(), { headers: { Accept: 'application/json' } });
                const d = await res.json();
                document.getElementById('chipPending').textContent = 'Nieuw: ' + d.counts.pending;
                document.getElementById('chipPaid').textContent = 'Betaald: ' + d.counts.paid;
                document.getElementById('chipTotal').textContent = 'Totaal: ' + d.counts.total;
                rows.innerHTML = d.data.length ? d.data.map(o => `
                    <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                        <td class="px-3 py-3"><a class="text-sm font-semibold hover:text-blue-600 hover:underline" style="color:var(--c-heading)" href="/admin/orders/${o.id}">${esc(o.order_number)}</a></td>
                        <td class="px-3 py-3"><div class="text-sm font-semibold line-clamp-1" style="color:var(--c-heading)">${esc(o.customer)}</div><div class="text-xs" style="color:var(--c-muted)">${esc(o.email)}</div></td>
                        <td class="px-3 py-3 text-sm font-bold whitespace-nowrap" style="color:var(--c-heading)">€${Number(o.total).toFixed(2).replace('.', ',')}</td>
                        <td class="px-3 py-3">${payBadge(o.payment_status)}</td>
                        <td class="px-3 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold" style="color:var(--c-heading)">${o.shipping_method === 'pickup' ? 'Afhalen' : 'Bezorging'}</span></td>
                        <td class="px-3 py-3">${statusBadge(o.order_status)}</td>
                        <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${esc(o.created_at)}</td>
                        <td class="w-[100px] min-w-[100px] max-w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                            <div class="flex items-center justify-end gap-1">
                                <a href="/admin/orders/${o.id}" title="Openen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg></a>
                                <button type="button" onclick="askDeleteOrder(${o.id}, '${esc(o.order_number)}')" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                            </div>
                        </td>
                    </tr>`).join('') : '<tr><td colspan="8" class="px-6 py-16 text-center"><div class="flex flex-col items-center gap-3"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" /></svg></span><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen bestellingen gevonden</p></div></td></tr>';
                renderPagination(d.pagination);
            }
            window.askDeleteOrder = (id, num) => {
                pendingDelete = id;
                document.getElementById('deleteOrderNumber').textContent = num;
                window.SlimmePC.modal.open('deleteOrderModal');
            };
            document.getElementById('deleteOrderConfirm').addEventListener('click', async () => {
                if (!pendingDelete) return;
                const res = await fetch('/admin/orders/' + pendingDelete, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } });
                const d = await res.json().catch(() => ({}));
                window.SlimmePC.modal.close('deleteOrderModal');
                window.SlimmePC.toast(d.message || 'Verwijderd.', res.ok ? 'success' : 'error');
                pendingDelete = null;
                load();
            });
            [search, fStatus, fPay, fPerPage].forEach(el => el.addEventListener(el === search ? 'input' : 'change', () => {
                clearTimeout(timer); timer = setTimeout(() => { page = 1; load(); }, el === search ? 400 : 0);
            }));
            load();
        })();
    </script>
</x-admin.layout>

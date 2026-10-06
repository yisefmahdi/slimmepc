<x-admin.layout title="Licentiecodes">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Licentiecodes</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer licentiecodes per digitaal product. Codes worden automatisch toegewezen na betaling.</p>
            </div>
            <button type="button" id="licenseAddBtn"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Code toevoegen
            </button>
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
                        <input type="text" id="licenseSearch" placeholder="Zoek op code, product of bestelnummer..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="licenseProductFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-52"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="all">Alle producten</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}{{ $p->is_digital ? '' : ' (fysiek)' }}</option>
                        @endforeach
                    </select>
                    <select id="licenseStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-36"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="all">Alle statussen</option>
                        <option value="available">Beschikbaar</option>
                        <option value="sold">Verkocht</option>
                    </select>
                    <select id="licensePerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-[110px] sm:ml-auto"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="15">15 per pagina</option>
                        <option value="10">10 per pagina</option>
                        <option value="25">25 per pagina</option>
                        <option value="50">50 per pagina</option>
                    </select>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="countTotal">Totaal: 0</span>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400" id="countAvailable">Beschikbaar: 0</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300" id="countSold">Verkocht: 0</span>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width: 720px">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="w-[60px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">ID</th>
                            <th class="min-w-[180px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Product</th>
                            <th class="min-w-[180px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Code</th>
                            <th class="w-[130px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[150px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Bestelling</th>
                            <th class="w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Actie</th>
                        </tr>
                    </thead>
                    <tbody id="licenseTableBody"></tbody>
                </table>
            </div>
            <div id="licensePagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Add --}}
    <x-admin.modal id="licenseFormModal" title="Licentiecode toevoegen" size="sm">
        <form id="licenseForm" class="space-y-4">
            <div>
                <x-input-label for="license_product_id">Product <span class="text-red-500">*</span></x-input-label>
                <select id="license_product_id" name="product_id" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                    <option value="">Selecteer een product</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                    @if($products->isEmpty())
                        <option value="" disabled>Geen digitale producten — markeer eerst een product als digitaal</option>
                    @endif
                </select>
                <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
            </div>
            <div>
                <x-input-label for="license_code">Licentiecode <span class="text-red-500">*</span></x-input-label>
                <x-text-input id="license_code" name="code" placeholder="Bijv. ABCD-1234-EFGH-5678" />
                <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
            </div>
        </form>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="licenseSaveBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white"><span data-btn-label>Opslaan</span></button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete --}}
    <x-admin.modal id="licenseDeleteModal" title="Licentiecode verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je code <span id="deleteLicenseCode" class="font-mono font-bold">—</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="licenseDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script>
    (() => {
        const $ = (s, r=document) => r.querySelector(s);
        const $$ = (s, r=document) => [...r.querySelectorAll(s)];
        const els = {
            search: $('#licenseSearch'), product: $('#licenseProductFilter'), status: $('#licenseStatusFilter'),
            perPage: $('#licensePerPage'), tbody: $('#licenseTableBody'), pagination: $('#licensePagination'),
            countTotal: $('#countTotal'), countAvailable: $('#countAvailable'), countSold: $('#countSold'),
        };
        let state = { page: 1, search: '', product_id: 'all', status: 'all', per_page: 15 };
        let currentData = [];
        let deleteId = null;
        const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
        const escapeHtml = (s) => s==null?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        const showModal = (id) => { const el = document.getElementById(id); if (!el) return; el.classList.remove('hidden'); el.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; };
        const hideModal = (id) => { const el = document.getElementById(id); if (!el) return; el.classList.add('hidden'); el.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
        $$('[data-modal-close]').forEach(btn => btn.addEventListener('click', () => { const modal = btn.closest('[id^="modal-"]'); if (modal) hideModal(modal.id); }));

        async function fetchData() {
            const params = new URLSearchParams({ page: state.page, search: state.search, product_id: state.product_id, status: state.status, per_page: state.per_page });
            const res = await fetch(`/admin/webshop/license-codes/data?${params}`, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Laden mislukt');
            return res.json();
        }

        function renderTable(paginator) {
            currentData = paginator.data;
            if (!paginator.data.length) {
                els.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-16 text-center"><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen licentiecodes gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Voeg de eerste code toe met de knop hierboven.</p></td></tr>`;
                return;
            }
            els.tbody.innerHTML = paginator.data.map(c => {
                const badge = c.status === 'sold'
                    ? `<span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Verkocht</span>`
                    : `<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Beschikbaar</span>`;
                const order = c.order ? `<span class="text-xs font-bold" style="color:var(--c-heading)">${escapeHtml(c.order.order_number)}</span>` : '<span class="text-xs" style="color:var(--c-muted)">—</span>';
                return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                    <td class="px-3 py-3 text-xs font-bold" style="color:var(--c-muted)">#${c.id}</td>
                    <td class="px-3 py-3 text-sm font-semibold" style="color:var(--c-heading)">${escapeHtml(c.product?.title || 'Onbekend')}</td>
                    <td class="px-3 py-3 font-mono text-xs font-bold" style="color:var(--c-heading)">${escapeHtml(c.code)}</td>
                    <td class="px-3 py-3">${badge}</td>
                    <td class="px-3 py-3">${order}</td>
                    <td class="px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                        <button type="button" data-delete="${c.id}" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                    </td>
                </tr>`;
            }).join('');
            $$('#licenseTableBody [data-delete]').forEach(b => b.addEventListener('click', () => openDelete(b.getAttribute('data-delete'))));
        }

        function renderPagination(p) {
            if (!p || p.last_page <= 1) { els.pagination.innerHTML=''; return; }
            let html = `<div class="flex flex-wrap items-center justify-between gap-2 text-xs"><span style="color:var(--c-muted)">Pagina ${p.current_page} van ${p.last_page} — ${p.total} resultaten</span><div class="flex gap-1">`;
            for (let i=1;i<=p.last_page;i++){ const a=i===p.current_page; html+=`<button data-page="${i}" class="min-w-[36px] rounded-lg px-3 py-1.5 font-bold ${a?'bg-blue-600 text-white':'border bg-white hover:bg-slate-50'}" style="${a?'':'border-color:var(--c-input-border);color:var(--c-heading)'}">${i}</button>`; }
            html+=`</div></div>`; els.pagination.innerHTML=html;
            $$('#licensePagination [data-page]').forEach(b=>b.addEventListener('click',()=>{ state.page=parseInt(b.getAttribute('data-page')); load(); }));
        }

        function renderCounts(c) {
            if (els.countTotal) els.countTotal.textContent = `Totaal: ${c.total}`;
            if (els.countAvailable) els.countAvailable.textContent = `Beschikbaar: ${c.available}`;
            if (els.countSold) els.countSold.textContent = `Verkocht: ${c.sold}`;
        }

        async function load() {
            if (window.AdminTable && els.tbody) window.AdminTable.loading(els.tbody, 6);
            try {
                const json = await fetchData();
                renderTable(json.codes);
                renderPagination(json.codes);
                renderCounts(json.counts);
            } catch(e) { els.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-sm" style="color:var(--c-muted)">Fout: ${escapeHtml(e.message)}</td></tr>`; }
        }

        function openDelete(id) {
            const c = currentData.find(x => String(x.id) === String(id));
            deleteId = id;
            $('#deleteLicenseCode').textContent = c ? c.code : '—';
            showModal('modal-licenseDeleteModal');
        }

        $('#licenseAddBtn')?.addEventListener('click', () => showModal('modal-licenseFormModal'));

        $('#licenseSaveBtn')?.addEventListener('click', async () => {
            const form = $('#licenseForm');
            form.querySelectorAll('.field-error').forEach(e => { e.textContent=''; e.classList.add('hidden'); });
            const btn = $('#licenseSaveBtn'); const label = btn.querySelector('[data-btn-label]'); const orig = label.textContent;
            label.textContent = 'Opslaan...'; btn.disabled = true;
            try {
                const res = await fetch('/admin/webshop/license-codes', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ product_id: $('#license_product_id').value, code: $('#license_code').value })
                });
                const json = await res.json();
                if (!res.ok) {
                    if (json.errors) {
                        Object.entries(json.errors).forEach(([field, msgs]) => {
                            const input = form.querySelector(`[name="${field}"]`);
                            const errEl = input?.closest('div')?.querySelector('.field-error');
                            if (errEl) { errEl.textContent = Array.isArray(msgs)?msgs[0]:msgs; errEl.classList.remove('hidden'); }
                        });
                    }
                    throw new Error(json.message || 'Validatie fout');
                }
                hideModal('modal-licenseFormModal');
                form.reset();
                if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message);
                load();
            } catch(err) { if (!err.message.includes('Validatie') && window.SlimmePC) window.SlimmePC.toast.error(err.message); }
            finally { label.textContent = orig; btn.disabled = false; }
        });

        $('#licenseDeleteConfirmBtn')?.addEventListener('click', async () => {
            if (!deleteId) return;
            const btn = $('#licenseDeleteConfirmBtn'); btn.disabled = true;
            try {
                const res = await fetch(`/admin/webshop/license-codes/${deleteId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json(); if (!res.ok) throw new Error(json.message || 'Fout');
                hideModal('modal-licenseDeleteModal');
                if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message);
                load();
            } catch(err) { alert(err.message); }
            finally { btn.disabled = false; }
        });

        let t;
        els.search?.addEventListener('input', e => { clearTimeout(t); t=setTimeout(()=>{ state.search=e.target.value; state.page=1; load(); },350); });
        els.product?.addEventListener('change', e => { state.product_id=e.target.value; state.page=1; load(); });
        els.status?.addEventListener('change', e => { state.status=e.target.value; state.page=1; load(); });
        els.perPage?.addEventListener('change', e => { state.per_page=e.target.value; state.page=1; load(); });

        load();
    })();
    </script>
    @endpush
</x-admin.layout>

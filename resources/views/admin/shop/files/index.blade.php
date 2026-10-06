<x-admin.layout title="Bestanden">
    @php
        $freeGb = $freeBytes > 0 ? number_format($freeBytes / 1024 / 1024 / 1024, 1, ',', '.') : '—';
    @endphp
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Bestanden</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Upload installatiebestanden (geen limiet — vrije ruimte: {{ $freeGb }} GB). Kopieer de link en plak hem bij het product. Alleen kopers met een betaalde bestelling kunnen downloaden.</p>
            </div>
            <button type="button" id="fileUploadBtn"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Bestand uploaden
            </button>
            <input type="file" id="fileInput" class="hidden">
        </div>

        <div id="uploadProgress" class="mb-4 hidden rounded-2xl border p-4" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <div class="flex items-center justify-between gap-3 text-xs">
                <span id="uploadFileName" class="font-bold truncate" style="color: var(--c-heading)"></span>
                <span id="uploadPct" class="font-bold text-blue-600 shrink-0">0%</span>
            </div>
            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                <div id="uploadBar" class="h-full w-0 rounded-full bg-gradient-to-r from-[#075be8] to-[#064bd7] transition-all"></div>
            </div>
            <p id="uploadMsg" class="mt-2 hidden text-xs font-medium"></p>
            <div id="uploadLinkRow" class="mt-3 hidden items-center gap-2">
                <input type="text" id="uploadLink" readonly class="form-input h-10 flex-1 font-mono text-xs" style="background-color: var(--c-page)">
                <button type="button" id="uploadCopyBtn" class="inline-flex h-10 shrink-0 items-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-bold text-white hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5" /></svg>
                    Kopieer link
                </button>
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
                        <input type="text" id="fileSearch" placeholder="Zoek op bestandsnaam..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="filePerPage"
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
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300" id="countBytes">Opslag: 0</span>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width: 760px">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="w-[60px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">ID</th>
                            <th class="min-w-[200px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Bestand</th>
                            <th class="w-[140px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Geüpload</th>
                            <th class="w-[110px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Downloads</th>
                            <th class="w-[120px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Link</th>
                            <th class="w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Actie</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody"></tbody>
                </table>
            </div>
            <div id="filePagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Delete --}}
    <x-admin.modal id="fileDeleteModal" title="Bestand verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="deleteFileName" class="font-bold">dit bestand</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Downloadlinks naar dit bestand werken daarna niet meer.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="fileDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script>
    (() => {
        const $ = (s, r=document) => r.querySelector(s);
        const $$ = (s, r=document) => [...r.querySelectorAll(s)];
        const els = {
            search: $('#fileSearch'), perPage: $('#filePerPage'),
            tbody: $('#fileTableBody'), pagination: $('#filePagination'),
            countTotal: $('#countTotal'), countBytes: $('#countBytes'),
        };
        const ALLOWED = @json($extensions);
        const CHUNK_SIZE = 4 * 1024 * 1024;
        let state = { page: 1, search: '', per_page: 15 };
        let currentData = [];
        let deleteId = null;
        let uploading = false;
        const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
        const escapeHtml = (s) => s==null?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        const showModal = (id) => { const el = document.getElementById(id); if (!el) return; el.classList.remove('hidden'); el.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; };
        const hideModal = (id) => { const el = document.getElementById(id); if (!el) return; el.classList.add('hidden'); el.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
        $$('[data-modal-close]').forEach(btn => btn.addEventListener('click', () => { const modal = btn.closest('[id^="modal-"]'); if (modal) hideModal(modal.id); }));
        const fmtBytes = (b) => {
            b = Number(b) || 0;
            if (b < 1024) return b + ' B';
            const u = ['KB','MB','GB','TB'];
            let i = Math.floor(Math.log(b) / Math.log(1024));
            i = Math.min(i, 4);
            return (b / Math.pow(1024, i)).toFixed(i >= 3 ? 2 : 0) + ' ' + u[i-1];
        };
        const toastOk = (m) => { if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(m); };
        const toastErr = (m) => { if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(m); else alert(m); };

        async function copyText(text, btn) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (e) {
                const i = document.createElement('input');
                i.value = text; document.body.appendChild(i); i.select();
                document.execCommand('copy'); i.remove();
            }
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = '✓ Gekopieerd!';
                setTimeout(() => { btn.innerHTML = orig; }, 1800);
            } else {
                toastOk('Link gekopieerd! Plak hem bij het product.');
            }
        }

        async function fetchData() {
            const params = new URLSearchParams({ page: state.page, search: state.search, per_page: state.per_page });
            const res = await fetch(`/admin/webshop/bestanden/data?${params}`, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Laden mislukt');
            return res.json();
        }

        function fullUrl(path) {
            if (!path) return '';
            if (path.startsWith('http')) return path;
            return window.location.origin + (path.startsWith('/') ? path : '/' + path);
        }

        function renderTable(paginator) {
            currentData = paginator.data;
            if (!paginator.data.length) {
                els.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-16 text-center"><p class="text-sm font-semibold" style="color:var(--c-heading)">Nog geen bestanden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Upload je eerste installatiebestand met de knop hierboven.</p></td></tr>`;
                return;
            }
            els.tbody.innerHTML = paginator.data.map(f => {
                const url = fullUrl(f.route_url || (`/download/bestand/${f.id}`));
                const when = f.created_at ? new Date(f.created_at).toLocaleDateString('nl-NL') : '—';
                return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                    <td class="px-3 py-3 text-xs font-bold" style="color:var(--c-muted)">#${f.id}</td>
                    <td class="px-3 py-3"><div class="text-sm font-semibold break-all" style="color:var(--c-heading)">${escapeHtml(f.name)}</div><div class="text-[11px]" style="color:var(--c-muted)">${fmtBytes(f.size)}${f.mime ? ' · ' + escapeHtml(f.mime) : ''}</div></td>
                    <td class="px-3 py-3 text-xs" style="color:var(--c-muted)">${when}</td>
                    <td class="px-3 py-3"><span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">${f.downloads_count || 0}×</span></td>
                    <td class="px-3 py-3"><button type="button" data-copy="${escapeHtml(url)}" class="copy-link-btn inline-flex h-9 items-center gap-1.5 rounded-lg border px-3 text-xs font-bold transition hover:bg-blue-50 hover:text-blue-600" style="border-color:var(--c-input-border);color:var(--c-heading)" title="${escapeHtml(url)}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5" /></svg>Kopieer</button></td>
                    <td class="px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                        <button type="button" data-delete="${f.id}" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                    </td>
                </tr>`;
            }).join('');
            $$('#fileTableBody [data-copy]').forEach(b => b.addEventListener('click', () => copyText(b.getAttribute('data-copy'), b)));
            $$('#fileTableBody [data-delete]').forEach(b => b.addEventListener('click', () => openDelete(b.getAttribute('data-delete'))));
        }

        function renderPagination(p) {
            if (!p || p.last_page <= 1) { els.pagination.innerHTML=''; return; }
            let html = `<div class="flex flex-wrap items-center justify-between gap-2 text-xs"><span style="color:var(--c-muted)">Pagina ${p.current_page} van ${p.last_page} — ${p.total} resultaten</span><div class="flex gap-1">`;
            for (let i=1;i<=p.last_page;i++){ const a=i===p.current_page; html+=`<button data-page="${i}" class="min-w-[36px] rounded-lg px-3 py-1.5 font-bold ${a?'bg-blue-600 text-white':'border bg-white hover:bg-slate-50'}" style="${a?'':'border-color:var(--c-input-border);color:var(--c-heading)'}">${i}</button>`; }
            html+=`</div></div>`; els.pagination.innerHTML=html;
            $$('#filePagination [data-page]').forEach(b=>b.addEventListener('click',()=>{ state.page=parseInt(b.getAttribute('data-page')); load(); }));
        }

        async function load() {
            if (window.AdminTable && els.tbody) window.AdminTable.loading(els.tbody, 6);
            try {
                const json = await fetchData();
                renderTable(json.files);
                renderPagination(json.files);
                if (els.countTotal) els.countTotal.textContent = `Totaal: ${json.counts.total}`;
                if (els.countBytes) els.countBytes.textContent = `Opslag: ${fmtBytes(json.counts.bytes)}`;
            } catch(e) { els.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-sm" style="color:var(--c-muted)">Fout: ${escapeHtml(e.message)}</td></tr>`; }
        }

        function openDelete(id) {
            const f = currentData.find(x => String(x.id) === String(id));
            deleteId = id;
            $('#deleteFileName').textContent = f ? f.name : 'dit bestand';
            showModal('modal-fileDeleteModal');
        }

        $('#fileDeleteConfirmBtn')?.addEventListener('click', async () => {
            if (!deleteId) return;
            const btn = $('#fileDeleteConfirmBtn'); btn.disabled = true;
            try {
                const res = await fetch(`/admin/webshop/bestanden/${deleteId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json(); if (!res.ok) throw new Error(json.message || 'Fout');
                hideModal('modal-fileDeleteModal');
                toastOk(json.message);
                load();
            } catch(err) { toastErr(err.message); }
            finally { btn.disabled = false; }
        });

        // ---------- chunked upload ----------
        $('#fileUploadBtn')?.addEventListener('click', () => $('#fileInput').click());
        $('#fileInput')?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            e.target.value = '';
            if (file) startUpload(file);
        });
        $('#uploadCopyBtn')?.addEventListener('click', () => copyText($('#uploadLink').value, $('#uploadCopyBtn')));

        function setProgress(pct, msg, isErr) {
            $('#uploadBar').style.width = pct + '%';
            $('#uploadPct').textContent = pct + '%';
            const m = $('#uploadMsg');
            if (msg) {
                m.textContent = msg;
                m.classList.remove('hidden');
                m.className = 'mt-2 text-xs font-medium ' + (isErr ? 'text-red-500' : 'text-emerald-600');
            } else {
                m.classList.add('hidden');
            }
        }

        async function startUpload(file) {
            if (uploading) { toastErr('Er loopt al een upload. Even wachten...'); return; }
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            if (!ALLOWED.includes(ext)) {
                toastErr('Bestandstype niet toegestaan. Toegestaan: ' + ALLOWED.join(', '));
                return;
            }
            uploading = true;
            $('#uploadProgress').classList.remove('hidden');
            $('#uploadLinkRow').classList.add('hidden');
            $('#uploadLinkRow').classList.remove('flex');
            $('#uploadFileName').textContent = file.name + ' (' + fmtBytes(file.size) + ')';
            setProgress(0);

            const uploadId = (crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + '-' + Math.random().toString(16).slice(2)).replace(/[^A-Za-z0-9_-]/g, '');
            const total = Math.max(1, Math.ceil(file.size / CHUNK_SIZE));

            try {
                for (let i = 0; i < total; i++) {
                    const fd = new FormData();
                    fd.append('upload_id', uploadId);
                    fd.append('index', i);
                    fd.append('total', total);
                    fd.append('chunk', file.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE), 'chunk');
                    const res = await fetch('/admin/webshop/bestanden/chunk', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd,
                    });
                    if (!res.ok) {
                        const j = await res.json().catch(() => ({}));
                        throw new Error(j.message || ('Deel ' + (i + 1) + ' mislukt.'));
                    }
                    setProgress(Math.round(((i + 1) / total) * 100));
                }

                setProgress(100, 'Bezig met samenvoegen...');
                const done = await fetch('/admin/webshop/bestanden/complete', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ upload_id: uploadId, total: total, name: file.name, size: file.size }),
                });
                const json = await done.json();
                if (!done.ok) throw new Error(json.message || 'Samenvoegen mislukt.');

                setProgress(100, '✓ ' + (json.message || 'Geüpload.'));
                $('#uploadLink').value = fullUrl(json.url);
                $('#uploadLinkRow').classList.remove('hidden');
                $('#uploadLinkRow').classList.add('flex');
                toastOk('Bestand geüpload! Kopieer de link en plak hem bij het product.');
                load();
            } catch (err) {
                setProgress(0, '✗ ' + err.message, true);
            } finally {
                uploading = false;
            }
        }

        let t;
        els.search?.addEventListener('input', e => { clearTimeout(t); t=setTimeout(()=>{ state.search=e.target.value; state.page=1; load(); },350); });
        els.perPage?.addEventListener('change', e => { state.per_page=e.target.value; state.page=1; load(); });

        load();
    })();
    </script>
    @endpush
</x-admin.layout>

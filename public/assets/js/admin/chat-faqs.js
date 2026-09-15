/* Chat kennisbank — admin CRUD */
(function () {
    'use strict';

    const tableBody = document.getElementById('faqTableBody');
    if (!tableBody) return;

    const paginationEl = document.getElementById('faqPagination');
    const searchEl = document.getElementById('faqSearch');
    const statusEl = document.getElementById('faqStatusFilter');
    const perPageEl = document.getElementById('faqPerPage');
    const countsEl = document.getElementById('faqCounts');
    const deleteConfirmBtn = document.getElementById('faqDeleteConfirmBtn');

    let currentId = null;
    let currentPage = 1;
    let searchTimer = null;

    function escapeHtml(v) {
        return String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }
    function getToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }
    function closeModal(id) {
        if (window.closeModal) window.closeModal(id);
        else { const el = document.getElementById('modal-' + id); if (el) el.classList.add('hidden'); }
    }
    function openModal(id) {
        if (window.openModal) window.openModal(id);
        else { const el = document.getElementById('modal-' + id); if (el) el.classList.remove('hidden'); }
    }
    function toastOk(msg) {
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(msg);
    }
    function toastErr(msg) {
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(msg);
        else alert(msg);
    }

    function load() {
        if (window.AdminTable) window.AdminTable.loading(tableBody, 5);
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            status: statusEl.value,
            per_page: perPageEl.value,
            page: currentPage,
        });
        fetch('/admin/chat/faqs/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            renderList(data.data || []);
            renderPagination(data.pagination);
            if (countsEl && data.counts) countsEl.textContent = 'Actief: ' + data.counts.active + ' / Totaal: ' + data.counts.total;
        }).catch(() => {});
    }

    function renderList(items) {
        if (!items.length) {
            tableBody.innerHTML = '<tr><td colspan="5" class="px-4 py-14 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen vragen gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Voeg je eerste kennisbank-vraag toe.</p></td></tr>';
            return;
        }
        tableBody.innerHTML = items.map(row => {
            const badge = row.is_active
                ? '<span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700 dark:bg-green-900/30 dark:text-green-300">Actief</span>'
                : '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">Inactief</span>';
            return '<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.12)">'
                + '<td class="px-3 py-3 text-sm font-semibold" style="color:var(--c-heading)">' + escapeHtml(row.question) + '<p class="mt-0.5 max-w-[420px] truncate text-xs font-normal" style="color:var(--c-muted)">' + escapeHtml(row.answer || '') + '</p></td>'
                + '<td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">' + escapeHtml(row.category || '—') + '</td>'
                + '<td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">' + escapeHtml(row.sort_order) + '</td>'
                + '<td class="px-3 py-3 whitespace-nowrap"><button type="button" data-toggle="' + row.id + '" title="Aan/uit">' + badge + '</button></td>'
                + '<td class="px-3 py-3 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">'
                + '<div class="flex justify-end gap-2">'
                + '<button type="button" data-edit="' + row.id + '" class="inline-flex h-8 items-center justify-center rounded-lg bg-blue-50 px-3 text-[11px] font-bold text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">Bewerken</button>'
                + '<button type="button" data-delete="' + row.id + '" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" aria-label="Verwijderen"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>'
                + '</div></td></tr>';
        }).join('');

        tableBody.querySelectorAll('[data-edit]').forEach(btn => {
            btn.addEventListener('click', () => openForm(parseInt(btn.dataset.edit, 10)));
        });
        tableBody.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                currentId = parseInt(btn.dataset.delete, 10);
                const q = btn.closest('tr').querySelector('td').childNodes[0].textContent.trim().slice(0, 60);
                document.getElementById('faqDeleteName').textContent = '"' + q + '"';
                openModal('faqDeleteModal');
            });
        });
        tableBody.querySelectorAll('[data-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                fetch('/admin/chat/faqs/' + btn.dataset.toggle + '/toggle', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
                }).then(r => r.json()).then(d => { toastOk(d.message || 'Bijgewerkt.'); load(); })
                .catch(() => toastErr('Bijwerken mislukt.'));
            });
        });
    }

    function renderPagination(p) {
        if (!p || p.last <= 1) { paginationEl.innerHTML = ''; return; }
        let html = '<div class="flex items-center justify-center gap-1 text-xs">';
        for (let i = 1; i <= p.last; i++) {
            html += '<button type="button" data-page="' + i + '" class="rounded-lg px-2.5 py-1 font-bold transition ' + (i === p.current ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800') + '"' + (i === p.current ? '' : ' style="color:var(--c-heading)"') + '>' + i + '</button>';
        }
        html += '</div>';
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', () => { currentPage = parseInt(btn.dataset.page, 10); load(); });
        });
    }

    function openForm(id) {
        document.getElementById('faqId').value = id || '';
        document.getElementById('modal-faqFormModal-title').textContent = id ? 'Vraag bewerken' : 'Nieuwe vraag';
        if (!id) {
            document.getElementById('faqQuestion').value = '';
            document.getElementById('faqAnswer').value = '';
            document.getElementById('faqKeywords').value = '';
            document.getElementById('faqCategory').value = '';
            document.getElementById('faqSort').value = '0';
            document.getElementById('faqActive').checked = true;
            openModal('faqFormModal');
            return;
        }
        fetch('/admin/chat/faqs/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                const f = data.faq;
                document.getElementById('faqQuestion').value = f.question || '';
                document.getElementById('faqAnswer').value = f.answer || '';
                document.getElementById('faqKeywords').value = f.keywords || '';
                document.getElementById('faqCategory').value = f.category || '';
                document.getElementById('faqSort').value = f.sort_order ?? 0;
                document.getElementById('faqActive').checked = !!f.is_active;
                openModal('faqFormModal');
            }).catch(() => toastErr('Laden mislukt.'));
    }

    document.getElementById('faqCreateBtn').addEventListener('click', () => openForm(null));

    document.getElementById('faqKeywordsAiBtn').addEventListener('click', () => {
        const btn = document.getElementById('faqKeywordsAiBtn');
        const label = document.getElementById('faqKeywordsAiLabel');
        const question = document.getElementById('faqQuestion').value.trim();
        const answer = document.getElementById('faqAnswer').value.trim();
        const category = document.getElementById('faqCategory').value.trim();
        if (!question || !answer) { toastErr('Vul eerst vraag én antwoord in.'); return; }
        const kwInput = document.getElementById('faqKeywords');
        if (kwInput.value.trim() && !confirm('Bestaande zoekwoorden overschrijven met AI-suggesties?')) return;
        btn.disabled = true;
        const origLabel = label.textContent;
        label.textContent = 'AI bedenkt zoekwoorden...';
        fetch('/admin/chat/faqs/generate-keywords', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ question, answer, category }),
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok || !data.success) throw new Error(data.message || 'Genereren mislukt.');
            kwInput.value = data.keywords || '';
            toastOk('Zoekwoorden gegenereerd — controleer en pas aan.');
        }).catch(e => toastErr(e.message)).finally(() => {
            btn.disabled = false;
            label.textContent = origLabel;
        });
    });

    document.getElementById('faqSaveBtn').addEventListener('click', () => {
        const id = document.getElementById('faqId').value;
        const payload = {
            question: document.getElementById('faqQuestion').value.trim(),
            answer: document.getElementById('faqAnswer').value.trim(),
            keywords: document.getElementById('faqKeywords').value.trim(),
            category: document.getElementById('faqCategory').value.trim(),
            sort_order: parseInt(document.getElementById('faqSort').value || '0', 10),
            is_active: document.getElementById('faqActive').checked ? 1 : 0,
        };
        if (!payload.question || !payload.answer) { toastErr('Vul vraag én antwoord in.'); return; }
        const btn = document.getElementById('faqSaveBtn');
        btn.disabled = true;
        fetch(id ? '/admin/chat/faqs/' + id : '/admin/chat/faqs', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload),
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error((data.errors && Object.values(data.errors)[0][0]) || data.message || 'Opslaan mislukt.');
            toastOk(data.message || 'Opgeslagen.');
            closeModal('faqFormModal');
            load();
        }).catch(e => toastErr(e.message)).finally(() => { btn.disabled = false; });
    });

    deleteConfirmBtn.addEventListener('click', () => {
        if (!currentId) return;
        deleteConfirmBtn.disabled = true;
        fetch('/admin/chat/faqs/' + currentId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(() => {
            closeModal('faqDeleteModal');
            currentId = null;
            load();
        }).catch(() => toastErr('Verwijderen mislukt.'))
        .finally(() => { deleteConfirmBtn.disabled = false; });
    });

    searchEl.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => { currentPage = 1; load(); }, 300); });
    statusEl.addEventListener('change', () => { currentPage = 1; load(); });
    perPageEl.addEventListener('change', () => { currentPage = 1; load(); });

    load();
})();

/* Device receipts — admin */
(function () {
    'use strict';

    const tableBody = document.getElementById('ontvangstTableBody');
    const paginationEl = document.getElementById('ontvangstPagination');
    const searchEl = document.getElementById('ontvangstSearch');
    const perPageEl = document.getElementById('ontvangstPerPage');
    const deleteModal = document.getElementById('ontvangstDeleteModal');
    const deleteConfirmBtn = document.getElementById('ontvangstDeleteConfirmBtn');
    if (!tableBody) return;

    let currentId = null;
    let currentPage = 1;
    let searchTimer = null;
    const currentType = window.ONTVANGST_TYPE || 'laptop';

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

    function load() {
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            per_page: perPageEl.value,
            page: currentPage,
            type: currentType,
        });
        fetch('/admin/bevestiging-mail/ontvangst/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            renderList(data.data || []);
            renderPagination(data.pagination);
        }).catch(() => {});
    }

    function renderList(items) {
        if (!items.length) {
            tableBody.innerHTML = `<tr><td colspan="8" class="px-4 py-14 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen ontvangsten gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Maak je eerste ontvangst aan.</p></td></tr>`;
            return;
        }
        tableBody.innerHTML = items.map(row => {
            const d = row.received_at || row.created_at ? new Date((row.received_at || row.created_at).replace(' ', 'T')) : null;
            const dateStr = d ? String(d.getDate()).padStart(2,'0')+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+d.getFullYear()+' '+String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0') : '—';
            const tNum = escapeHtml(row.receipt_number || ('DR-' + String(row.id).padStart(5,'0')));
            const notes = row.notes ? escapeHtml(row.notes) : '—';
            const shortNotes = notes.length > 60 ? notes.slice(0,60)+'…' : notes;
            return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.12)">
                <td class="px-3 py-3 text-sm font-semibold whitespace-nowrap" style="color:var(--c-heading)">${escapeHtml(row.customer_name)}</td>
                <td class="px-3 py-3 text-xs truncate max-w-[170px]" style="color:var(--c-heading)">${escapeHtml(row.customer_email)}</td>
                <td class="px-3 py-3 text-xs font-bold whitespace-nowrap" style="color:var(--c-heading)">${tNum}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(row.device_type || '—')}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(row.serial_number || '—')}</td>
                <td class="px-3 py-3 text-[10px] max-w-[260px] truncate" style="color:var(--c-muted)" title="${notes}">${shortNotes}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(dateStr)}</td>
                <td class="px-3 py-3 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                    <button type="button" data-delete="${row.id}" data-name="${tNum}" class="ontvangst-delete inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                    </button>
                </td>
            </tr>`;
        }).join('');
        tableBody.querySelectorAll('.ontvangst-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                currentId = parseInt(btn.dataset.delete, 10);
                document.getElementById('ontvangstDeleteName').textContent = btn.dataset.name;
                openModal('ontvangstDeleteModal');
            });
        });
    }

    function renderPagination(p) {
        if (!p || p.last <= 1) { paginationEl.innerHTML = ''; return; }
        let html = '<div class="flex items-center justify-center gap-1 text-xs">';
        for (let i = 1; i <= p.last; i++) {
            html += `<button type="button" data-page="${i}" class="rounded-lg px-2.5 py-1 font-bold transition ${i === p.current ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'}" style="${i === p.current ? '' : 'color:var(--c-heading)'}">${i}</button>`;
        }
        html += '</div>';
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', () => { currentPage = parseInt(btn.dataset.page, 10); load(); });
        });
    }

    function confirmDelete() {
        if (!currentId) return;
        const btn = deleteConfirmBtn;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Laden...';
        fetch('/admin/bevestiging-mail/ontvangst/' + currentId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(() => {
            currentId = null;
            closeModal('ontvangstDeleteModal');
            load();
        }).catch(()=>{}).finally(()=>{
            btn.disabled = false;
            btn.innerHTML = origHtml;
        });
    }

    searchEl.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(()=>{ currentPage=1; load(); }, 300); });
    perPageEl.addEventListener('change', ()=>{ currentPage=1; load(); });
    deleteConfirmBtn.addEventListener('click', confirmDelete);

    load();
})();

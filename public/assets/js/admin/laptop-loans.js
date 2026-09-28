/* Leen / Huur Laptop — admin */
(function () {
    'use strict';

    const tableBody = document.getElementById('leenTableBody');
    const paginationEl = document.getElementById('leenPagination');
    const searchEl = document.getElementById('leenSearch');
    const perPageEl = document.getElementById('leenPerPage');
    const statusEl = document.getElementById('leenStatus');
    const deleteConfirmBtn = document.getElementById('leenDeleteConfirmBtn');
    const returnConfirmBtn = document.getElementById('leenReturnConfirmBtn');
    if (!tableBody) return;

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
        if (window.SlimmePC && window.SlimmePC.modal) { window.SlimmePC.modal.close(id); return; }
        if (window.closeModal) window.closeModal(id);
        else { const el = document.getElementById('modal-' + id); if (el) el.classList.add('hidden'); }
    }
    function openModal(id) {
        if (window.SlimmePC && window.SlimmePC.modal) { window.SlimmePC.modal.open(id); return; }
        if (window.openModal) window.openModal(id);
        else { const el = document.getElementById('modal-' + id); if (el) el.classList.remove('hidden'); }
    }
    function toast(msg, type) {
        if (window.SlimmePC && window.SlimmePC.toast) { window.SlimmePC.toast(msg, type || 'success'); return; }
        alert(msg);
    }

    function statusPill(status) {
        if (status === 'teruggebracht') {
            return '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Teruggebracht</span>';
        }
        return '<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Uitgeleend</span>';
    }

    function load() {
        if (window.AdminTable) window.AdminTable.loading(tableBody, 7);
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            status: statusEl.value,
            per_page: perPageEl.value,
            page: currentPage,
        });
        fetch('/admin/leen-huur/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            renderList(data.data || []);
            renderPagination(data.pagination);
        }).catch(() => {});
    }

    function renderList(items) {
        if (!items.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-14 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen uitgiften gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Geef je eerste laptop uit.</p></td></tr>';
            return;
        }
        tableBody.innerHTML = items.map(row => {
            const d = row.given_at ? new Date(String(row.given_at).replace(' ', 'T')) : null;
            const dateStr = d && !isNaN(d) ? String(d.getDate()).padStart(2,'0')+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+d.getFullYear()+' '+String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0') : '—';
            const lNum = escapeHtml(row.loan_number || ('LL-' + String(row.id).padStart(5,'0')));
            const photoCount = parseInt(row.photos_count || 0, 10);
            const photoBadge = photoCount > 0 ? '<span class="ml-1.5 inline-flex items-center gap-1 rounded-full bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" /></svg>' + photoCount + '</span>' : '';
            const isOut = row.status !== 'teruggebracht';
            return '<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.12)">'
                + '<td class="px-3 py-3 text-xs font-bold whitespace-nowrap" style="color:var(--c-heading)">' + lNum + photoBadge + '</td>'
                + '<td class="px-3 py-3 text-sm font-semibold whitespace-nowrap" style="color:var(--c-heading)">' + escapeHtml(row.customer_name) + '</td>'
                + '<td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">' + escapeHtml(row.phone || '—') + '</td>'
                + '<td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">' + escapeHtml(row.laptop_type || '—') + '</td>'
                + '<td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">' + escapeHtml(dateStr) + '</td>'
                + '<td class="px-3 py-3 whitespace-nowrap">' + statusPill(row.status) + '</td>'
                + '<td class="px-3 py-3 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">'
                + '<div class="flex justify-end gap-2">'
                + '<button type="button" data-preview="' + row.id + '" class="leen-preview inline-flex h-8 items-center justify-center rounded-lg bg-blue-50 px-3 text-[11px] font-bold text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">Preview</button>'
                + '<a href="/admin/leen-huur/' + row.id + '/edit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" title="Bewerken"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></a>'
                + (isOut ? '<button type="button" data-return="' + row.id + '" data-name="' + escapeHtml(row.customer_name) + '" class="leen-return inline-flex h-8 items-center justify-center rounded-lg bg-emerald-50 px-3 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300">Retour</button>' : '')
                + '<button type="button" data-delete="' + row.id + '" data-name="' + lNum + '" class="leen-delete inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>'
                + '</div></td></tr>';
        }).join('');
        tableBody.querySelectorAll('.leen-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                currentId = parseInt(btn.dataset.delete, 10);
                document.getElementById('leenDeleteName').textContent = btn.dataset.name;
                openModal('leenDeleteModal');
            });
        });
        tableBody.querySelectorAll('.leen-return').forEach(btn => {
            btn.addEventListener('click', () => {
                currentId = parseInt(btn.dataset.return, 10);
                document.getElementById('leenReturnName').textContent = btn.dataset.name || 'deze laptop';
                openModal('leenReturnModal');
            });
        });
        tableBody.querySelectorAll('.leen-preview').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.preview;
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
                fetch('/admin/leen-huur/' + id, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        const r = data.loan;
                        document.getElementById('leenPrevNum').textContent = r.loan_number;
                        document.getElementById('leenPrevStatus').textContent = r.status === 'teruggebracht' ? 'Teruggebracht' : 'Uitgeleend';
                        document.getElementById('leenPrevName').textContent = r.customer_name;
                        document.getElementById('leenPrevEmail').textContent = r.customer_email;
                        document.getElementById('leenPrevPhone').textContent = r.phone || '—';
                        document.getElementById('leenPrevAddress').textContent = (r.address || '') + ', ' + (r.postcode || '') + ' ' + (r.city || '');
                        document.getElementById('leenPrevRepair').textContent = r.repair_number || '—';
                        document.getElementById('leenPrevLaptop').textContent = r.laptop_type;
                        document.getElementById('leenPrevDate').textContent = r.given_at ? new Date(String(r.given_at).replace(' ', 'T')).toLocaleString('nl-NL') : '—';
                        document.getElementById('leenPrevPdf').href = '/admin/leen-huur/' + r.id + '/overeenkomst';
                        currentId = r.id;
                        renderPreviewPhotos(data.photos || []);
                        openModal('leenPreviewModal');
                    })
                    .finally(() => { btn.disabled = false; btn.innerHTML = origHtml; });
            });
        });
    }

    function renderPreviewPhotos(photos) {
        const grid = document.getElementById('leenPrevPhotosGrid');
        const empty = document.getElementById('leenPrevPhotosEmpty');
        const count = document.getElementById('leenPrevPhotosCount');
        if (!grid) return;
        grid.innerHTML = '';
        count.textContent = photos.length;
        if (!photos.length) { empty.classList.remove('hidden'); return; }
        empty.classList.add('hidden');
        photos.forEach((p, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900';
            b.innerHTML = '<img src="' + p.url + '" alt="Foto ' + (i + 1) + '" class="h-full w-full object-cover" loading="lazy">';
            b.addEventListener('click', () => {
                document.getElementById('leenPhotoLightboxImg').src = p.url;
                openModal('leenPhotoLightbox');
            });
            grid.appendChild(b);
        });
    }

    function renderPagination(p) {
        if (!p || !paginationEl) return;
        if (p.last <= 1) { paginationEl.innerHTML = '<p class="text-xs" style="color:var(--c-muted)">Totaal: ' + p.total + '</p>'; return; }
        let btns = '';
        for (let i = 1; i <= p.last; i++) {
            btns += '<button type="button" data-page="' + i + '" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-bold ' + (i === p.current ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800') + '" ' + (i === p.current ? '' : 'style="color:var(--c-heading)"') + '>' + i + '</button>';
        }
        paginationEl.innerHTML = '<div class="flex items-center justify-between gap-2"><p class="text-xs" style="color:var(--c-muted)">Totaal: ' + p.total + '</p><div class="flex flex-wrap gap-1">' + btns + '</div></div>';
        paginationEl.querySelectorAll('button[data-page]').forEach(b => {
            b.addEventListener('click', () => { currentPage = parseInt(b.dataset.page, 10); load(); });
        });
    }

    if (deleteConfirmBtn) {
        deleteConfirmBtn.addEventListener('click', () => {
            if (!currentId) return;
            deleteConfirmBtn.disabled = true;
            fetch('/admin/leen-huur/' + currentId, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(data => {
                closeModal('leenDeleteModal');
                toast(data.message || 'Uitgifte verwijderd.');
                load();
            }).catch(() => toast('Verwijderen mislukt.', 'error'))
              .finally(() => { deleteConfirmBtn.disabled = false; });
        });
    }

    if (returnConfirmBtn) {
        returnConfirmBtn.addEventListener('click', () => {
            if (!currentId) return;
            returnConfirmBtn.disabled = true;
            fetch('/admin/leen-huur/' + currentId + '/retour', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' }
            }).then(async r => {
                const data = await r.json().catch(() => ({}));
                if (!r.ok) throw new Error(data.message || 'Retour bevestigen mislukt.');
                closeModal('leenReturnModal');
                toast(data.message || 'Laptop teruggebracht.');
                load();
            }).catch(err => toast(err.message, 'error'))
              .finally(() => { returnConfirmBtn.disabled = false; });
        });
    }

    searchEl.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { currentPage = 1; load(); }, 300);
    });
    perPageEl.addEventListener('change', () => { currentPage = 1; load(); });
    statusEl.addEventListener('change', () => { currentPage = 1; load(); });

    load();
})();

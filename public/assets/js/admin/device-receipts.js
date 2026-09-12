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
        if (window.AdminTable) window.AdminTable.loading(tableBody, 7);
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
            tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-14 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen ontvangsten gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Maak je eerste ontvangst aan.</p></td></tr>`;
            return;
        }
        tableBody.innerHTML = items.map(row => {
            const d = row.received_at || row.created_at ? new Date((row.received_at || row.created_at).replace(' ', 'T')) : null;
            const dateStr = d ? String(d.getDate()).padStart(2,'0')+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+d.getFullYear()+' '+String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0') : '—';
            const tNum = escapeHtml(row.receipt_number || ('DR-' + String(row.id).padStart(5,'0')));
            const photoCount = parseInt(row.photos_count || 0, 10);
            const photoBadge = photoCount > 0 ? `<span class="ml-1.5 inline-flex items-center gap-1 rounded-full bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" /></svg>${photoCount}</span>` : '';
            return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.12)">
                <td class="px-3 py-3 text-sm font-semibold whitespace-nowrap" style="color:var(--c-heading)">${escapeHtml(row.customer_name)}</td>
                <td class="px-3 py-3 text-xs truncate max-w-[170px]" style="color:var(--c-heading)">${escapeHtml(row.customer_email)}</td>
                <td class="px-3 py-3 text-xs font-bold whitespace-nowrap" style="color:var(--c-heading)">${tNum}${photoBadge}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(row.device_type || '—')}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(row.serial_number || '—')}</td>
                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">${escapeHtml(dateStr)}</td>
                <td class="px-3 py-3 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                    <div class="flex justify-end gap-2">
                        <button type="button" data-preview="${row.id}" class="ontvangst-preview inline-flex h-8 items-center justify-center rounded-lg bg-blue-50 px-3 text-[11px] font-bold text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">
                            Preview
                        </button>
                        <button type="button" data-delete="${row.id}" data-name="${tNum}" class="ontvangst-delete inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                    </div>
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
        tableBody.querySelectorAll('.ontvangst-preview').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.preview;
                // Add loading indicator to the button
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
                
                fetch('/admin/bevestiging-mail/ontvangst/' + id)
                    .then(r => r.json())
                    .then(data => {
                        const r = data.receipt;
                        document.getElementById('prevName').textContent = r.customer_name;
                        document.getElementById('prevEmail').textContent = r.customer_email;
                        document.getElementById('prevTNum').textContent = r.receipt_number;
                        document.getElementById('prevDevice').textContent = r.device_type;
                        document.getElementById('prevSerial').textContent = r.serial_number || '—';
                        document.getElementById('prevDate').textContent = r.received_at ? new Date(r.received_at.replace(' ', 'T')).toLocaleString('nl-NL') : '—';
                        document.getElementById('prevNotes').textContent = r.notes || '—';
                        document.getElementById('prevStatus').value = r.status;
                        currentId = r.id;
                        renderPreviewPhotos(data.photos || [], r.id);
                        openModal('ontvangstPreviewModal');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    });
            });
        });
    }

    function renderPreviewPhotos(photos, receiptId) {
        const grid = document.getElementById('prevPhotosGrid');
        const emptyEl = document.getElementById('prevPhotosEmpty');
        const countEl = document.getElementById('prevPhotosCount');
        if (!grid) return;
        const list = Array.isArray(photos) ? photos : [];
        if (countEl) countEl.textContent = list.length;
        if (emptyEl) emptyEl.style.display = list.length ? 'none' : '';
        grid.innerHTML = list.map((p, idx) => {
            const url = typeof p === 'string' ? p : p.url;
            const pid = typeof p === 'object' ? p.photo_id : null;
            const delBtn = pid ? `<button type="button" data-photo-del="${pid}" aria-label="Foto verwijderen" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-lg bg-black/60 text-white opacity-0 transition group-hover:opacity-100 hover:bg-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>` : '';
            return `<button type="button" data-photo-tile data-photo-view="${escapeHtml(url)}" class="group relative block h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900" title="Foto ${idx + 1} openen"><img src="${escapeHtml(url)}" alt="Foto ${idx + 1}" loading="lazy" class="h-full w-full object-cover pointer-events-none">${delBtn}<span class="absolute bottom-1 left-1 rounded-md bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">${idx + 1}</span></button>`;
        }).join('');
        grid.querySelectorAll('[data-photo-view]').forEach(tile => {
            tile.addEventListener('click', () => {
                const url = tile.getAttribute('data-photo-view');
                const img = document.getElementById('photoLightboxImg');
                if (img) img.src = url;
                openModal('ontvangstPhotoLightbox');
            });
        });
        grid.querySelectorAll('[data-photo-del]').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                const pid = btn.getAttribute('data-photo-del');
                if (!confirm('Deze foto verwijderen?')) return;
                btn.disabled = true;
                fetch('/admin/bevestiging-mail/ontvangst/' + receiptId + '/photo/' + pid, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
                }).then(r => r.json()).then(() => {
                    const tile = btn.closest('[data-photo-tile]');
                    if (tile) tile.remove();
                    const remaining = grid.querySelectorAll('[data-photo-tile]').length;
                    if (countEl) countEl.textContent = remaining;
                    if (emptyEl) emptyEl.style.display = remaining ? 'none' : '';
                    load();
                }).catch(() => {}).finally(() => { btn.disabled = false; });
            });
        });
    }

    function updateStatus() {
        if (!currentId) return;
        const btn = document.getElementById('prevStatusUpdateBtn');
        const statusEl = document.getElementById('prevStatus');
        const status = statusEl.value;
        const origHtml = btn.innerHTML;

        if (status === 'completed') {
            openModal('ontvangstConfirmStatusModal');
            document.getElementById('ontvangstConfirmStatusBtn').onclick = () => {
                closeModal('ontvangstConfirmStatusModal');
                performStatusUpdate(btn, status, origHtml);
            };
            return;
        }

        performStatusUpdate(btn, status, origHtml);
    }

    function performStatusUpdate(btn, status, origHtml) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Laden...';

        fetch('/admin/bevestiging-mail/ontvangst/' + currentId + '/status', {
            method: 'POST',
            headers: { 
                'Accept': 'application/json', 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getToken(), 
                'X-Requested-With': 'XMLHttpRequest' 
            },
            body: JSON.stringify({ status })
        }).then(r => r.json()).then(data => {
            if (window.SlimmePC && window.SlimmePC.toast) {
                window.SlimmePC.toast.success(data.message || 'Status bijgewerkt succesvol!');
            } else {
                alert(data.message || 'Status bijgewerkt succesvol!');
            }
            load();
        }).catch(e => {
            if (window.SlimmePC && window.SlimmePC.toast) {
                window.SlimmePC.toast.error('Er is een fout opgetreden bij het bijwerken van de status.');
            } else {
                alert('Er is een fout opgetreden bij het bijwerken van de status.');
            }
        }).finally(() => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
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
        const deletedId = currentId;
        fetch('/admin/bevestiging-mail/ontvangst/' + deletedId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(() => {
            const row = document.querySelector(`[data-delete="${deletedId}"]`)?.closest('tr');
            if (row) row.remove();
            if (!tableBody.querySelectorAll('tr').length) {
                tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-14 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen ontvangsten gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Maak je eerste ontvangst aan.</p></td></tr>`;
            }
            currentId = null;
            closeModal('ontvangstDeleteModal');
        }).catch(()=>{}).finally(()=>{
            btn.disabled = false;
            btn.innerHTML = origHtml;
        });
    }

    searchEl.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(()=>{ currentPage=1; load(); }, 300); });
    perPageEl.addEventListener('change', ()=>{ currentPage=1; load(); });
    deleteConfirmBtn.addEventListener('click', confirmDelete);
    document.addEventListener('click', e => {
        if (e.target && e.target.id === 'prevStatusUpdateBtn') updateStatus();
    });

    load();
})();

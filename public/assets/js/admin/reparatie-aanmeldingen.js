/* Repair submissions inbox (reparatie aanmeldingen) — admin */
(function () {
    'use strict';

    const tableBody = document.getElementById('repairTableBody');
    const paginationEl = document.getElementById('repairPagination');
    const searchEl = document.getElementById('repairSearch');
    const statusFilterEl = document.getElementById('repairStatusFilter');
    const perPageEl = document.getElementById('repairPerPage');
    const countNewEl = document.getElementById('repairCountNew');
    const countTotalEl = document.getElementById('repairCountTotal');

    const statusSelect = document.getElementById('repairStatusSelect');
    const deleteBtn = document.getElementById('repairDeleteBtn');
    const deleteModal = document.getElementById('repairDeleteModal');
    const deleteConfirmBtn = document.getElementById('repairDeleteConfirmBtn');

    let currentId = null;
    let currentPage = 1;
    let searchTimer = null;

    const STATUS_LABEL = {
        new: 'Nieuw',
        in_progress: 'In behandeling',
        completed: 'Afgerond',
    };

    const STATUS_BADGE = {
        new: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        in_progress: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        completed: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    };

    const STATUS_SELECT_STYLE = {
        new: 'background-color:#fee2e2;color:#b91c1c;border-color:#fecaca',
        in_progress: 'background-color:#fef3c7;color:#b45309;border-color:#fde68a',
        completed: 'background-color:#dcfce7;color:#15803d;border-color:#bbf7d0',
    };

    function escapeHtml(value) {
        return String(value ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }

    function api(url, options) {
        return fetch(url, Object.assign({
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        }, options)).then(res => res.json());
    }

    function closeModal(id) {
        if (window.closeModal && typeof window.closeModal === 'function') {
            window.closeModal(id);
        } else {
            const el = document.getElementById('modal-' + id);
            if (el) el.classList.add('hidden');
        }
    }

    function openModal(id) {
        if (window.openModal && typeof window.openModal === 'function') {
            window.openModal(id);
        } else {
            const el = document.getElementById('modal-' + id);
            if (el) el.classList.remove('hidden');
        }
    }

    function load() {
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            status: statusFilterEl.value,
            per_page: perPageEl.value,
            page: currentPage,
        });

        api('/admin/reparatie-aanmeldingen/data?' + params.toString())
            .then(data => {
                renderList(data.data);
                renderPagination(data.pagination);
                if (data.counts) {
                    countNewEl.textContent = 'Nieuw: ' + data.counts.new;
                    countTotalEl.textContent = 'Totaal: ' + data.counts.total;
                }
            })
            .catch(() => {});
    }

    function renderList(items) {
        if (!items.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center">
                        <p class="font-semibold" style="color: var(--c-heading)">Geen aanvragen gevonden</p>
                        <p class="mt-1 text-xs" style="color: var(--c-muted)">Pas je zoekopdracht of filters aan.</p>
                    </td>
                </tr>`;
            return;
        }

        tableBody.innerHTML = items.map(row => {
            const badge = STATUS_BADGE[row.status] || STATUS_BADGE.new;
            const label = STATUS_LABEL[row.status] || row.status;
            const date = row.created_at ? new Date(row.created_at.replace(' ', 'T')).toLocaleDateString('nl-NL') : '—';
            return `
                <tr class="border-b transition hover:bg-blue-50/50 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,0.12)">
                    <td class="px-4 py-3 text-sm font-bold" style="color: var(--c-heading)">${escapeHtml(row.repair_number)}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-[#075be8] to-[#064bd7] text-xs font-bold text-white">${escapeHtml((row.name || '?').charAt(0).toUpperCase())}</span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold" style="color: var(--c-heading)">${escapeHtml(row.name)}</p>
                                <p class="truncate text-[11px]" style="color: var(--c-muted)">${escapeHtml(row.email)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: var(--c-heading)">${escapeHtml(row.device)}</td>
                    <td class="px-4 py-3 text-sm" style="color: var(--c-heading)">${escapeHtml(row.brand)} ${escapeHtml(row.model)}</td>
                    <td class="px-4 py-3">
                        <div>
                            <select data-status="${row.id}" data-prev="${row.status}" class="status-select rounded-lg border px-2 py-1.5 text-[11px] font-bold leading-none outline-none transition focus:ring-2 focus:ring-blue-100" style="${STATUS_SELECT_STYLE[row.status] || ''}">
                                <option value="new" ${row.status === 'new' ? 'selected' : ''}>Nieuw</option>
                                <option value="in_progress" ${row.status === 'in_progress' ? 'selected' : ''}>In behandeling</option>
                                <option value="completed" ${row.status === 'completed' ? 'selected' : ''}>Afgerond</option>
                            </select>
                            <span class="status-msg mt-1 hidden block text-[10px] font-semibold text-green-600">Bijgewerkt ✓</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs" style="color: var(--c-muted)">${date}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" data-view="${row.id}" class="repair-view inline-flex h-9 items-center justify-center rounded-lg bg-blue-600 px-3 text-xs font-bold text-white transition hover:bg-blue-700">Bekijk</button>
                    </td>
                </tr>`;
        }).join('');

        tableBody.querySelectorAll('.repair-view').forEach(btn => {
            btn.addEventListener('click', () => openDetail(parseInt(btn.dataset.view, 10)));
        });

        tableBody.querySelectorAll('.status-select').forEach(sel => {
            sel.addEventListener('change', () => onStatusChange(sel, parseInt(sel.dataset.status, 10), sel.value));
        });
    }

    function renderPagination(p) {
        if (!p || p.last <= 1) {
            paginationEl.innerHTML = '';
            return;
        }
        let html = '<div class="flex items-center justify-center gap-1 text-xs">';
        for (let i = 1; i <= p.last; i++) {
            html += `<button type="button" data-page="${i}" class="rounded-lg px-2.5 py-1 font-bold transition ${i === p.current ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800'}" style="${i === p.current ? '' : 'color: var(--c-heading)'}">${i}</button>`;
        }
        html += '</div>';
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', () => {
                currentPage = parseInt(btn.dataset.page, 10);
                load();
            });
        });
    }

    function openDetail(id) {
        currentId = id;
        api('/admin/reparatie-aanmeldingen/' + id).then(data => {
            renderDetail(data.submission, data.photos || []);
            openModal('repairDetailModal');
        }).catch(() => {});
    }

    function renderDetail(s, photos) {
        document.getElementById('repairName').textContent = s.name || '—';
        document.getElementById('repairMeta').textContent = s.email || '';
        document.getElementById('repairAvatar').textContent = (s.name || '?').charAt(0).toUpperCase();
        document.getElementById('repairNumber').textContent = s.repair_number || '—';
        document.getElementById('repairPhone').textContent = s.phone || '—';
        document.getElementById('repairDate').textContent = s.created_at ? new Date(s.created_at.replace(' ', 'T')).toLocaleString('nl-NL') : '—';
        statusSelect.value = s.status;

        const field = (label, value) => `
            <div class="grid gap-1 py-2.5 sm:grid-cols-[180px_1fr] sm:gap-5 border-b" style="border-color: rgba(148,163,184,0.12)">
                <dt class="text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">${label}</dt>
                <dd class="break-words text-sm font-semibold" style="color: var(--c-heading)">${escapeHtml(value)}</dd>
            </div>`;

        const problems = Array.isArray(s.problems) ? s.problems.join(', ') : (s.problems || '—');

        let html = `
            <dl>
                ${field('Aanmeldnummer', s.repair_number)}
                ${field('Apparaat', s.device)}
                ${field('Probleem', problems)}
                ${field('Omschrijving', s.description)}
                ${field('Merk', s.brand)}
                ${field('Model', s.model)}
                ${field('Serienummer', s.serial || 'Niet opgegeven')}
                ${field('Belangrijke gegevens', s.data_importance)}
                ${field('Eerder geopend', s.opened_before)}
                ${field('Naam', s.name)}
                ${field('E-mail', s.email)}
                ${field('Telefoon', s.phone)}
                ${field('Postcode', s.postcode)}
                ${field('Vervolg', s.delivery_method)}
                ${field('Contactvoorkeur', s.contact_preference)}
                ${field('Privacy akkoord', s.privacy ? 'Ja' : 'Nee')}
                ${field('IP-adres', s.ip_address || '')}
            </dl>`;

        if (photos.length) {
            html += `
                <h3 class="mb-3 mt-6 text-sm font-black" style="color: var(--c-heading)">Foto's</h3>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    ${photos.map(p => `
                        <a href="${p.url}" target="_blank" class="block overflow-hidden rounded-xl border" style="border-color: rgba(148,163,184,0.25)">
                            <img src="${p.url}" alt="" class="aspect-square w-full object-cover">
                        </a>`).join('')}
                </div>`;
        }

        document.getElementById('repairFields').innerHTML = html;
    }

    function changeStatus(id, status) {
        api('/admin/reparatie-aanmeldingen/' + id + '/status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken() },
            body: JSON.stringify({ status: status }),
        }).then(() => load()).catch(() => {});
    }

    function onStatusChange(sel, id, status) {
        sel.disabled = true;
        api('/admin/reparatie-aanmeldingen/' + id + '/status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken() },
            body: JSON.stringify({ status: status }),
        }).then(() => {
            sel.dataset.prev = status;
            sel.style.cssText = STATUS_SELECT_STYLE[status] || '';
            const msg = sel.parentElement.querySelector('.status-msg');
            if (msg) {
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 2500);
            }
            updateCounts();
        }).catch(() => {
            sel.value = sel.dataset.prev || status;
        }).finally(() => {
            sel.disabled = false;
        });
    }

    function updateCounts() {
        api('/admin/reparatie-aanmeldingen/data?per_page=1&page=1')
            .then(data => {
                if (data.counts) {
                    countNewEl.textContent = 'Nieuw: ' + data.counts.new;
                    countTotalEl.textContent = 'Totaal: ' + data.counts.total;
                }
            })
            .catch(() => {});
    }

    function confirmDelete() {
        if (!currentId) return;
        api('/admin/reparatie-aanmeldingen/' + currentId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getToken() },
        }).then(() => {
            currentId = null;
            closeModal('repairDetailModal');
            closeModal('repairDeleteModal');
            load();
        }).catch(() => {});
    }

    function getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    // Events
    searchEl.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { currentPage = 1; load(); }, 350);
    });
    statusFilterEl.addEventListener('change', () => { currentPage = 1; load(); });
    perPageEl.addEventListener('change', () => { currentPage = 1; load(); });

    statusSelect.addEventListener('change', () => {
        if (currentId) changeStatus(currentId, statusSelect.value);
    });

    deleteBtn.addEventListener('click', () => {
        if (!currentId) return;
        openModal('repairDeleteModal');
    });
    deleteConfirmBtn.addEventListener('click', confirmDelete);

    load();
})();

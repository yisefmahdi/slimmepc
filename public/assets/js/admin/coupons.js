(function () {
    function init() {
    const tbody = document.getElementById('couponTableBody');
    if (!tbody) return;

    const searchInput = document.getElementById('couponSearch');
    const statusFilter = document.getElementById('couponStatusFilter');
    const perPageSelect = document.getElementById('couponPerPage');
    const paginationEl = document.getElementById('couponPagination');
    const countTotal = document.getElementById('countTotal');
    const countActive = document.getElementById('countActive');
    const countInactive = document.getElementById('countInactive');

    let currentPage = 1;
    let deleteId = null;

    function toastSuccess(msg){ if(window.SlimmePC && window.SlimmePC.toast && window.SlimmePC.toast.success) window.SlimmePC.toast.success(msg); }
    function toastError(msg){ if(window.SlimmePC && window.SlimmePC.toast && window.SlimmePC.toast.error) window.SlimmePC.toast.error(msg); }

    function loading() {
        if (window.AdminTable && window.AdminTable.loading) {
            window.AdminTable.loading(tbody, 9);
        } else {
            tbody.innerHTML = '<tr><td colspan="9" class="py-10 text-center text-sm" style="color: var(--c-muted)">Laden...</td></tr>';
        }
    }

    async function load() {
        loading();
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPageSelect.value,
            search: searchInput.value.trim(),
            status: statusFilter.value,
        });
        try {
            const res = await axios.get(`/admin/webshop/coupons/data?${params.toString()}`);
            render(res.data);
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="9" class="py-10 text-center text-sm text-red-500">${e.response?.data?.message || 'Fout bij laden.'}</td></tr>`;
        }
    }

    function render(data) {
        const items = data.coupons.data || [];
        const counts = data.counts || {};
        if (countTotal) countTotal.textContent = `Totaal: ${counts.total ?? 0}`;
        if (countActive) countActive.textContent = `Actief: ${counts.active ?? 0}`;
        if (countInactive) countInactive.textContent = `Inactief: ${counts.inactive ?? 0}`;

        if (items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="py-12 text-center"><div class="flex flex-col items-center gap-2"><span class="text-sm font-semibold" style="color: var(--c-heading)">Geen kortingscodes gevonden</span><span class="text-xs" style="color: var(--c-muted)">Probeer een andere zoekterm of maak een nieuwe code aan.</span></div></td></tr>`;
            renderPagination(data.coupons);
            return;
        }

        tbody.innerHTML = items.map(c => {
            const val = c.discount_type === 'percentage' ? `${parseFloat(c.discount_value)}%` : `€${parseFloat(c.discount_value).toFixed(2).replace('.',',')}`;
            const typeLabel = c.discount_type === 'percentage' ? '<span class="rounded-full bg-purple-50 px-2.5 py-1 text-[11px] font-bold text-purple-600 dark:bg-purple-900/30">%</span>' : '<span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:bg-blue-900/30">€</span>';
            const minAmt = c.min_amount ? `€${parseFloat(c.min_amount).toFixed(2).replace('.',',')}` : '—';
            const usage = `${c.used_count ?? 0}${c.usage_limit ? ' / ' + c.usage_limit : ''}`;
            const validity = formatValidity(c);
            const singleBadge = c.is_single_use ? '<span class="ml-1 rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-600 dark:bg-amber-900/30">1x</span>' : '';

            const statusSwitch = `<button type="button" role="switch" aria-checked="${c.status ? 'true' : 'false'}" data-toggle-status="${c.id}" data-status="${c.status ? 1 : 0}" class="apple-switch ${c.status ? 'is-active' : 'is-inactive'}" title="${c.status ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)'}"><span class="apple-knob"></span></button>`;

            return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                <td class="px-3 py-3"><span class="inline-flex rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-mono font-bold text-white tracking-wider">${escapeHtml(c.code)}</span>${singleBadge}</td>
                <td class="px-3 py-3 text-xs font-medium" style="color: var(--c-heading)">${escapeHtml(c.name || '—')}</td>
                <td class="px-3 py-3 text-center">${typeLabel}</td>
                <td class="px-3 py-3 text-center text-xs font-bold" style="color: var(--c-heading)">${val}</td>
                <td class="px-3 py-3 text-center text-xs" style="color: var(--c-muted)">${minAmt}</td>
                <td class="px-3 py-3 text-center text-xs font-semibold" style="color: var(--c-heading)">${usage}</td>
                <td class="px-3 py-3 text-xs" style="color: var(--c-muted)">${validity}</td>
                <td class="px-3 py-3">${statusSwitch}</td>
                <td class="px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                    <div class="inline-flex items-center gap-1">
                        <button type="button" onclick="editCoupon(${c.id})" title="Bewerken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></button>
                        <button type="button" onclick="deleteCoupon(${c.id}, '${escapeHtml(c.code)}')" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        renderPagination(data.coupons);

        // Bind apple-switch toggles (like products.js)
        tbody.querySelectorAll('[data-toggle-status]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-toggle-status');
                const current = parseInt(btn.getAttribute('data-status'), 10);
                const newStatus = current ? 0 : 1;
                // Optimistic UI
                btn.setAttribute('data-status', newStatus);
                btn.setAttribute('aria-checked', newStatus ? 'true' : 'false');
                btn.className = `apple-switch ${newStatus ? 'is-active' : 'is-inactive'}`;
                btn.title = newStatus ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)';
                try {
                    const res = await axios.post(`/admin/webshop/coupons/${id}/toggle`, { status: newStatus });
                    toastSuccess(res.data.message || 'Status bijgewerkt.');
                    load();
                } catch (e) {
                    // revert
                    btn.setAttribute('data-status', current);
                    btn.setAttribute('aria-checked', current ? 'true' : 'false');
                    btn.className = `apple-switch ${current ? 'is-active' : 'is-inactive'}`;
                    btn.title = current ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)';
                    toastError(e.response?.data?.message || 'Fout bij bijwerken.');
                }
            });
        });
    }

    function formatValidity(c) {
        if (!c.start_date && !c.end_date) return '<span class="text-slate-400">Altijd geldig</span>';
        const fmt = d => d ? new Date(d).toLocaleDateString('nl-NL') : '—';
        if (c.start_date && c.end_date) return `${fmt(c.start_date)} → ${fmt(c.end_date)}`;
        if (c.start_date) return `Vanaf ${fmt(c.start_date)}`;
        return `T/m ${fmt(c.end_date)}`;
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function renderPagination(p) {
        if (!paginationEl) return;
        if (p.last_page <= 1) { paginationEl.innerHTML = ''; return; }
        let html = '<div class="flex items-center justify-between gap-2 text-xs"><span style="color: var(--c-muted)">Pagina ' + p.current_page + ' van ' + p.last_page + ' — ' + p.total + ' resultaten</span><div class="flex items-center gap-1">';
        for (let i = 1; i <= p.last_page; i++) {
            if (i === 1 || i === p.last_page || (i >= p.current_page - 1 && i <= p.current_page + 1)) {
                html += `<button type="button" data-page="${i}" class="h-7 min-w-7 rounded-lg border px-2 text-xs font-bold transition ${i === p.current_page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-slate-100'}" style="${i !== p.current_page ? 'border-color: var(--c-input-border); color: var(--c-heading)' : ''}">${i}</button>`;
            } else if (i === p.current_page - 2 || i === p.current_page + 2) {
                html += '<span class="px-1" style="color: var(--c-muted)">…</span>';
            }
        }
        html += '</div></div>';
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', () => { currentPage = parseInt(btn.getAttribute('data-page')); load(); });
        });
    }

    // Events
    let searchTimer;
    if (searchInput) searchInput.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => { currentPage = 1; load(); }, 300); });
    if (statusFilter) statusFilter.addEventListener('change', () => { currentPage = 1; load(); });
    if (perPageSelect) perPageSelect.addEventListener('change', () => { currentPage = 1; load(); });

    // Modal logic
    const modalId = 'couponFormModal';
    const form = document.getElementById('couponForm');
    const couponIdInput = document.getElementById('couponId');

    function openModal() { if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.open(modalId); }
    function closeModal() { if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.close(modalId); }

    document.getElementById('openCreateCoupon')?.addEventListener('click', () => {
        form.reset();
        couponIdInput.value = '';
        const btnLabel = document.querySelector(`#modal-${modalId} [data-btn-label]`);
        if (btnLabel) btnLabel.textContent = 'Code aanmaken';
        const titleEl = document.querySelector(`#modal-${modalId} h3`);
        if (titleEl) titleEl.textContent = 'Nieuwe kortingscode';
        clearErrors();
        document.getElementById('cp-code').value = genCode();
        openModal();
    });

    function genCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let s = '';
        for (let i = 0; i < 8; i++) s += chars[Math.floor(Math.random() * chars.length)];
        return s;
    }

    document.getElementById('generateCodeBtn')?.addEventListener('click', () => {
        document.getElementById('cp-code').value = genCode();
    });

    window.editCoupon = async function (id) {
        try {
            const res = await axios.get(`/admin/webshop/coupons/${id}`);
            const c = res.data.coupon;
            couponIdInput.value = c.id;
            document.getElementById('cp-code').value = c.code || '';
            document.getElementById('cp-name').value = c.name || '';
            document.getElementById('cp-discount-type').value = c.discount_type || 'percentage';
            document.getElementById('cp-discount-value').value = c.discount_value || '';
            document.getElementById('cp-min-amount').value = c.min_amount || '';
            document.getElementById('cp-usage-limit').value = c.usage_limit || '';
            document.getElementById('cp-status').value = c.status ? '1' : '0';
            document.getElementById('cp-single-use').checked = !!c.is_single_use;
            const toLocal = d => {
                if(!d) return '';
                const date = new Date(d);
                const pad = n => String(n).padStart(2,'0');
                return `${date.getFullYear()}-${pad(date.getMonth()+1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
            };
            document.getElementById('cp-start-date').value = toLocal(c.start_date);
            document.getElementById('cp-end-date').value = toLocal(c.end_date);
            const editLabel = document.querySelector(`#modal-${modalId} [data-btn-label]`);
            if (editLabel) editLabel.textContent = 'Wijzigingen opslaan';
            clearErrors();
            openModal();
        } catch (e) {
            toastError(e.response?.data?.message || 'Fout bij laden.');
        }
    };

    window.deleteCoupon = function (id, code) {
        deleteId = id;
        document.getElementById('deleteCouponCode').textContent = code;
        if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.open('couponDeleteModal');
    };

    function clearErrors() {
        form.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
        form.querySelectorAll('.form-input, input, select').forEach(el => el.classList.remove('border-red-500'));
    }

    function showErrors(errors) {
        clearErrors();
        Object.entries(errors).forEach(([field, msgs]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;
            const errEl = input.closest('div')?.querySelector('.field-error') || input.parentElement?.querySelector('.field-error');
            if (errEl) { errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
            input.classList.add('border-red-500');
        });
    }

    document.getElementById('couponSaveBtn')?.addEventListener('click', async () => {
        const btn = document.getElementById('couponSaveBtn');
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"/></svg> Opslaan...';
        clearErrors();

        const id = couponIdInput.value;
        const isEdit = !!id;
        const url = isEdit ? `/admin/webshop/coupons/${id}` : '/admin/webshop/coupons';
        const method = isEdit ? 'put' : 'post';

        const payload = {
            code: document.getElementById('cp-code').value.trim(),
            name: document.getElementById('cp-name').value.trim(),
            discount_type: document.getElementById('cp-discount-type').value,
            discount_value: document.getElementById('cp-discount-value').value,
            min_amount: document.getElementById('cp-min-amount').value || null,
            start_date: document.getElementById('cp-start-date').value || null,
            end_date: document.getElementById('cp-end-date').value || null,
            status: parseInt(document.getElementById('cp-status').value, 10),
            usage_limit: document.getElementById('cp-usage-limit').value || null,
            is_single_use: document.getElementById('cp-single-use').checked ? 1 : 0,
        };

        try {
            const res = await axios[method](url, payload);
            toastSuccess(res.data.message || 'Opgeslagen.');
            closeModal();
            load();
        } catch (e) {
            if (e.response?.status === 422 && e.response.data.errors) {
                showErrors(e.response.data.errors);
            } else {
                toastError(e.response?.data?.message || 'Fout bij opslaan.');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    });

    document.getElementById('couponDeleteConfirmBtn')?.addEventListener('click', async () => {
        if (!deleteId) return;
        const btn = document.getElementById('couponDeleteConfirmBtn');
        btn.disabled = true;
        try {
            const res = await axios.delete(`/admin/webshop/coupons/${deleteId}`);
            toastSuccess(res.data.message || 'Verwijderd.');
            if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.close('couponDeleteModal');
            deleteId = null;
            load();
        } catch (e) {
            toastError(e.response?.data?.message || 'Fout bij verwijderen.');
        } finally {
            btn.disabled = false;
        }
    });

    // Initial load
    load();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

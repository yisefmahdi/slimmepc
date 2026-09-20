/* Lidmaatschap inbox — admin (zelfde design-systeem als overige inboxen) */
(function () {
    'use strict';

    const bodyEl = document.getElementById('lidTableBody');
    if (!bodyEl) return;

    const paginationEl = document.getElementById('lidPagination');
    const searchEl = document.getElementById('lidSearch');
    const statusEl = document.getElementById('lidStatusFilter');
    const perPageEl = document.getElementById('lidPerPage');
    const deleteConfirmBtn = document.getElementById('lidDeleteConfirmBtn');

    let currentPage = 1;
    let searchTimer = null;
    let pendingDeleteId = null;
    let currentDetailId = null;

    const STATUS_LABEL = { paid: 'Betaald', unpaid: 'Open', cancelled: 'Geannuleerd' };

    function esc(v) {
        return String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }
    function token() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }
    function toast(msg, type) {
        if (window.SlimmePC && window.SlimmePC.toast) {
            if (type === 'success') window.SlimmePC.toast.success(msg);
            else window.SlimmePC.toast.error(msg);
        }
    }
    function eur(v) {
        return '€' + Number(v || 0).toFixed(2).replace('.', ',');
    }

    const SPINNER = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
    function btnLoading(btn, on) {
        if (!btn) return;
        if (on) {
            if (btn.dataset.origHtml === undefined) btn.dataset.origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = SPINNER;
            btn.classList.add('pointer-events-none', 'opacity-80');
        } else {
            if (btn.dataset.origHtml !== undefined) btn.innerHTML = btn.dataset.origHtml;
            btn.disabled = false;
            btn.classList.remove('pointer-events-none', 'opacity-80');
        }
    }

    function statusPill(s) {
        if (s === 'paid') return '<span class="inline-flex rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:bg-green-900/30 dark:text-green-300">Betaald</span>';
        if (s === 'cancelled') return '<span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">Geannuleerd</span>';
        return '<span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Open</span>';
    }

    function load() {
        if (window.AdminTable) window.AdminTable.loading(bodyEl, 7);
        else bodyEl.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-sm" style="color:var(--c-muted)">Gegevens laden…</td></tr>';
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            status: statusEl.value,
            per_page: perPageEl.value,
            page: currentPage,
        });
        fetch('/admin/lidmaatschap/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            renderRows(data.data || []);
            renderPagination(data.pagination);
            const c = data.counts || {};
            document.getElementById('lidCountPaid').textContent = 'Betaald: ' + (c.paid || 0);
            document.getElementById('lidCountUnpaid').textContent = 'Open: ' + (c.unpaid || 0);
            document.getElementById('lidCountTotal').textContent = 'Totaal: ' + (c.total || 0);
        }).catch(() => {
            bodyEl.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-sm text-red-500">Laden mislukt.</td></tr>';
        });
    }

    function renderRows(items) {
        if (!items.length) {
            bodyEl.innerHTML = '<tr><td colspan="7" class="px-6 py-16 text-center"><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen leden gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Pas je zoekopdracht of filters aan.</p></td></tr>';
            return;
        }
        bodyEl.innerHTML = items.map(r => {
            const period = (r.start_date ? r.start_date.substring(0, 10).split('-').reverse().join('-') : '—')
                + ' → ' + (r.end_date ? r.end_date.substring(0, 10).split('-').reverse().join('-') : '—');
            return '<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">'
                + '<td class="whitespace-nowrap px-4 py-3 text-xs font-bold" style="color:var(--c-heading)">' + esc(r.klantnummer) + '</td>'
                + '<td class="px-4 py-3 text-sm font-semibold" style="color:var(--c-heading)">' + esc(r.name) + '<span class="block text-xs font-normal" style="color:var(--c-muted)">' + esc(r.customer_email) + '</span></td>'
                + '<td class="px-4 py-3 text-xs" style="color:var(--c-muted)">' + (r.customer_type === 'business' ? 'Zakelijk' : 'Particulier') + '</td>'
                + '<td class="whitespace-nowrap px-4 py-3 text-sm font-bold" style="color:var(--c-heading)">' + eur(r.total) + '</td>'
                + '<td class="px-4 py-3">' + statusPill(r.payment_status) + '</td>'
                + '<td class="whitespace-nowrap px-4 py-3 text-xs" style="color:var(--c-muted)">' + esc(period) + '</td>'
                + '<td class="px-4 py-2 text-right"><button type="button" data-view="' + r.id + '" class="inline-flex h-8 items-center gap-1.5 rounded-lg px-3 text-xs font-bold text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/30">Bekijken</button></td>'
                + '</tr>';
        }).join('');
        bodyEl.querySelectorAll('[data-view]').forEach(btn => {
            btn.addEventListener('click', () => openDetail(parseInt(btn.dataset.view, 10)));
        });
    }

    function renderPagination(p) {
        if (!p || p.last <= 1) { paginationEl.innerHTML = ''; return; }
        paginationEl.innerHTML = '<div class="flex items-center justify-center gap-2 text-xs"><button type="button" data-nav="prev" class="rounded-lg px-2.5 py-1 font-bold hover:bg-slate-100 dark:hover:bg-slate-800" style="color:var(--c-heading)">‹</button><span style="color:var(--c-muted)">' + p.total + ' · ' + p.current + '/' + p.last + '</span><button type="button" data-nav="next" class="rounded-lg px-2.5 py-1 font-bold hover:bg-slate-100 dark:hover:bg-slate-800" style="color:var(--c-heading)">›</button></div>';
        paginationEl.querySelectorAll('[data-nav]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.nav === 'prev' && currentPage > 1) currentPage--;
                if (btn.dataset.nav === 'next' && currentPage < p.last) currentPage++;
                load();
            });
        });
    }

    function row(label, value) {
        return '<div class="flex items-center justify-between gap-3 rounded-xl border px-3.5 py-2.5" style="border-color:rgba(148,163,184,.15)"><span class="text-xs font-semibold" style="color:var(--c-muted)">' + label + '</span><span class="text-right text-sm font-bold" style="color:var(--c-heading)">' + value + '</span></div>';
    }

    function openDetail(id) {
        currentDetailId = id;
        fetch('/admin/lidmaatschap/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                const m = data.membership;
                document.getElementById('lidAvatar').textContent = (m.name || '?').trim().charAt(0).toUpperCase();
                document.getElementById('lidName').textContent = m.name || '—';
                document.getElementById('lidMeta').textContent = (m.email || '') + ' · ' + (STATUS_LABEL[m.payment_status] || m.payment_status);
                document.getElementById('lidNumber').textContent = m.klantnummer || '—';
                document.getElementById('lidPhone').textContent = m.phone || '—';
                document.getElementById('lidPeriod').textContent = (m.start_date || '—') + ' → ' + (m.end_date || '—');
                document.getElementById('lidDate').textContent = m.created_at || '—';
                const inv = (m.invoices && m.invoices[0]) || null;
                const invBtn = document.getElementById('lidInvoiceBtn');
                if (inv) {
                    invBtn.classList.remove('hidden');
                    invBtn.setAttribute('href', '/admin/lidmaatschap/' + m.id + '/factuur');
                } else {
                    invBtn.classList.add('hidden');
                }
                document.getElementById('lidFields').innerHTML =
                    row('Type', m.customer_type === 'business' ? 'Zakelijk' : 'Particulier')
                    + row('Geslacht', m.customer_gender === 'man' ? 'Man' : (m.customer_gender === 'vrouw' ? 'Vrouw' : '—'))
                    + row('Adres', esc([m.address, m.postcode, m.city].filter(Boolean).join(', ')) || '—')
                    + row('Bedrag', eur(m.total))
                    + row('Factuur', inv ? esc(inv.invoice_number) + ' · ' + eur(inv.total) : '—')
                    + row('Account', m.user ? esc(m.user.name + (m.user.klantnummer ? ' (' + m.user.klantnummer + ')' : '')) : 'Gast (geen login)');
                if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.open('lidDetailModal');
                else document.getElementById('modal-lidDetailModal').classList.remove('hidden');
            }).catch(() => toast('Details laden mislukt.', 'error'));
    }

    document.getElementById('lidDeleteBtn').addEventListener('click', () => {
        if (!currentDetailId) return;
        document.getElementById('lidDeleteName').textContent = document.getElementById('lidName').textContent || 'dit lidmaatschap';
        pendingDeleteId = currentDetailId;
        if (window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.open('lidDeleteModal');
    });

    deleteConfirmBtn.addEventListener('click', () => {
        if (!pendingDeleteId) return;
        btnLoading(deleteConfirmBtn, true);
        fetch('/admin/lidmaatschap/' + pendingDeleteId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(d => {
            if (window.SlimmePC && window.SlimmePC.modal) {
                window.SlimmePC.modal.close('lidDeleteModal');
                window.SlimmePC.modal.close('lidDetailModal');
            }
            toast(d.message || 'Verwijderd.', 'success');
            pendingDeleteId = null;
            currentDetailId = null;
            load();
        }).catch(() => toast('Verwijderen mislukt.', 'error'))
        .finally(() => btnLoading(deleteConfirmBtn, false));
    });

    searchEl.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => { currentPage = 1; load(); }, 300); });
    statusEl.addEventListener('change', () => { currentPage = 1; load(); });
    perPageEl.addEventListener('change', () => { currentPage = 1; load(); });

    load();
})();

(() => {
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => [...r.querySelectorAll(s)];

  const els = {
    search: $('#categorySearch'),
    status: $('#categoryStatusFilter'),
    perPage: $('#categoryPerPage'),
    tbody: $('#categoryTableBody'),
    pagination: $('#categoryPagination'),
    countTotal: $('#countTotal'),
    countActive: $('#countActive'),
    countInactive: $('#countInactive'),
  };

  let state = { page: 1, search: '', status: 'all', per_page: 10 };
  let currentData = [];
  let deleteId = null;
  let editId = null;

  function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  function showModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function hideModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Wire modal close buttons
  $$('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('[id^="modal-"]');
      if (modal) hideModal(modal.id);
    });
  });

  $$('[data-modal-overlay]').forEach(ov => {
    ov.addEventListener('click', () => {
      const modal = ov.closest('[id^="modal-"]');
      if (modal) hideModal(modal.id);
    });
  });

  function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(e => { e.textContent = ''; e.classList.add('hidden'); });
    form.querySelectorAll('.border-red-500').forEach(e => e.classList.remove('border-red-500'));
  }

  function showErrors(form, errors) {
    Object.entries(errors).forEach(([field, msgs]) => {
      const input = form.querySelector(`[name="${field}"]`);
      if (!input) return;
      const errEl = input.closest('div')?.querySelector('.field-error');
      if (errEl) {
        errEl.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
        errEl.classList.remove('hidden');
      }
      input.classList.add('border-red-500');
    });
  }

  async function fetchData() {
    const params = new URLSearchParams({
      page: state.page,
      search: state.search,
      status: state.status,
      per_page: state.per_page,
    });
    const res = await fetch(`/admin/webshop/categories/data?${params}`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) throw new Error('Failed to load');
    const json = await res.json();
    return json;
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function renderTable(categories) {
    currentData = categories.data;
    if (!categories.data.length) {
      els.tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-16 text-center"><div class="flex flex-col items-center gap-3"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M6 13.5V3.75a.75.75 0 01.75-.75h9a.75.75 0 01.75.75v9.75m-10.5 0a3.75 3.75 0 003.75 3.75h3a3.75 3.75 0 003.75-3.75M6 13.5h12" /></svg></span><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen categorieën gevonden</p><p class="text-xs" style="color:var(--c-muted)">Probeer een andere zoekterm of voeg een nieuwe categorie toe.</p></div></td></tr>`;
      return;
    }
    els.tbody.innerHTML = categories.data.map(cat => {
      const img = cat.image ? `<img src="/storage/${escapeHtml(cat.image)}" alt="" class="h-10 w-16 rounded-lg border object-cover" style="border-color:rgba(148,163,184,.2)">` : `<span class="flex h-10 w-16 items-center justify-center rounded-lg border bg-slate-50 text-[10px] font-bold" style="border-color:rgba(148,163,184,.2);color:var(--c-muted)">Geen</span>`;
      const statusBadge = cat.status
        ? `<span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Actief</span>`
        : `<span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Inactief</span>`;
      const iconCell = cat.icon
        ? `<span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"><i data-lucide="${escapeHtml(cat.icon)}" class="h-4 w-4"></i></span>`
        : `<span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-800" title="Geen pictogram">—</span>`;
      const descCell = cat.description
        ? `<span class="block max-w-[240px] truncate text-xs" style="color:var(--c-muted)" title="${escapeHtml(cat.description)}">${escapeHtml(cat.description)}</span>`
        : `<span class="text-xs" style="color:var(--c-muted)">—</span>`;
      return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
        <td class="px-3 py-3">${img}</td>
        <td class="px-3 py-3"><span class="text-sm font-semibold" style="color:var(--c-heading)">${escapeHtml(cat.name)}</span></td>
        <td class="px-3 py-3 text-center">${iconCell}</td>
        <td class="px-3 py-3">${descCell}</td>
        <td class="px-3 py-3"><span class="text-xs" style="color:var(--c-muted)">${escapeHtml(cat.slug)}</span></td>
        <td class="px-3 py-3 text-center"><span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">${cat.products_count ?? 0}</span></td>
        <td class="px-3 py-3">${statusBadge}</td>
        <td class="w-[160px] min-w-[160px] max-w-[160px] px-2 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
          <div class="flex items-center justify-end gap-0.5">
            <button type="button" data-view="${cat.id}" title="Bekijken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/30 dark:hover:text-blue-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"/></svg></button>
            <button type="button" data-edit="${cat.id}" title="Bewerken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></button>
            <button type="button" data-toggle="${cat.id}" data-status="${cat.status ? 1 : 0}" title="${cat.status ? 'Deactiveren' : 'Activeren'}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/30 dark:hover:text-amber-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="${cat.status ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}" /></svg></button>
            <button type="button" data-delete="${cat.id}" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
          </div>
        </td>
      </tr>`;
    }).join('');

     $$('[data-toggle]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.getAttribute('data-toggle');
        const current = parseInt(btn.getAttribute('data-status'), 10);
        const newStatus = current ? 0 : 1;
        try {
          const res = await fetch(`/admin/webshop/categories/${id}/toggle`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ status: newStatus }),
          });
          const json = await res.json();
          if (!res.ok) throw new Error(json.message || 'Fout');
          if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Status bijgewerkt.');
          load();
        } catch (err) {
          if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(err.message);
          else alert(err.message);
        }
      });
    });

    $$('[data-view]').forEach(btn => btn.addEventListener('click', () => openDetails(btn.getAttribute('data-view'))));
    $$('[data-edit]').forEach(btn => btn.addEventListener('click', () => openEdit(btn.getAttribute('data-edit'))));
    $$('[data-delete]').forEach(btn => btn.addEventListener('click', () => openDelete(btn.getAttribute('data-delete'))));

    if (window.lucide && window.lucide.createIcons) {
      try { window.lucide.createIcons(); } catch(e) {}
    }
  }

  function renderPagination(p) {
    if (!p || p.last_page <= 1) { els.pagination.innerHTML = ''; return; }
    let html = `<div class="flex flex-wrap items-center justify-between gap-2 text-xs"><span style="color:var(--c-muted)">Pagina ${p.current_page} van ${p.last_page} — ${p.total} resultaten</span><div class="flex gap-1">`;
    for (let i=1; i<=p.last_page; i++) {
      const active = i===p.current_page;
      html += `<button data-page="${i}" class="min-w-[36px] rounded-lg px-3 py-1.5 font-bold transition ${active ? 'bg-blue-600 text-white' : 'border bg-white hover:bg-slate-50'}" style="${active?'':'border-color:var(--c-input-border);color:var(--c-heading)'}">${i}</button>`;
    }
    html += `</div></div>`;
    els.pagination.innerHTML = html;
    $$('[data-page]').forEach(b => b.addEventListener('click', () => { state.page = parseInt(b.getAttribute('data-page')); load(); }));
  }

  function renderCounts(c) {
    els.countTotal.textContent = `Totaal: ${c.total}`;
    els.countActive.textContent = `Actief: ${c.active}`;
    els.countInactive.textContent = `Inactief: ${c.inactive}`;
  }

  async function load() {
    if (window.AdminTable && els.tbody) window.AdminTable.loading(els.tbody, 8);
    try {
      const json = await fetchData();
      renderTable(json.categories);
      renderPagination(json.categories);
      renderCounts(json.counts);
    } catch (e) {
      els.tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-sm" style="color:var(--c-muted)">Fout bij laden: ${escapeHtml(e.message)}</td></tr>`;
    }
  }

  // Search debounce
  let t;
  els.search?.addEventListener('input', (e) => {
    clearTimeout(t);
    t = setTimeout(() => { state.search = e.target.value; state.page = 1; load(); }, 350);
  });
  els.status?.addEventListener('change', (e) => { state.status = e.target.value; state.page = 1; load(); });
  els.perPage?.addEventListener('change', (e) => { state.per_page = e.target.value; state.page = 1; load(); });

  function setIconPickerValue(name) {
    const hidden = $('#c-icon');
    if (hidden) hidden.value = name || '';
    const picker = $('#c-icon-picker');
    if (!picker) return;
    const nameEl = picker.querySelector('.icon-picker-name');
    if (nameEl) nameEl.textContent = name || 'Kies een pictogram';
    const preview = picker.querySelector('.icon-picker-preview');
    if (preview) {
      preview.setAttribute('data-lucide', name || 'circle');
      preview.textContent = '';
      // re-render via icon-picker helper if available, else try lucide
      if (window.lucide && window.lucide.createIcons) {
        try {
          const ns = 'http://www.w3.org/2000/svg';
          const pascal = name ? name.replace(/(^\w|-\w)/g, m => m.replace(/-/,'').toUpperCase()) : 'Circle';
          // fallback: let icon-picker.js MutationObserver handle it; force by re-creating element
          const icon = window.lucide.icons[pascal] || window.lucide.icons['Circle'];
          if (icon) {
            const svg = document.createElementNS(ns, 'svg');
            svg.setAttribute('viewBox','0 0 24 24');
            svg.setAttribute('fill','none');
            svg.setAttribute('stroke','currentColor');
            svg.setAttribute('stroke-width','2');
            svg.setAttribute('stroke-linecap','round');
            svg.setAttribute('stroke-linejoin','round');
            svg.setAttribute('class','icon-picker-preview h-4 w-4');
            svg.setAttribute('data-lucide', name || 'circle');
            (icon[2]||[]).forEach(child => {
              const el = document.createElementNS(ns, child[0]);
              Object.entries(child[1]||{}).forEach(([k,v]) => el.setAttribute(k,v));
              (child[2]||[]).forEach(gc => {
                const cel = document.createElementNS(ns, gc[0]);
                Object.entries(gc[1]||{}).forEach(([k,v]) => cel.setAttribute(k,v));
                el.appendChild(cel);
              });
              svg.appendChild(el);
            });
            preview.replaceWith(svg);
          }
        } catch(e) {}
      }
    }
  }

  // Create
  $('#openCreateCategory')?.addEventListener('click', () => {
    editId = null;
    const form = $('#categoryForm');
    form.reset();
    $('#categoryId').value = '';
    setIconPickerValue('');
    const descEl = $('#c-description');
    if (descEl) descEl.value = '';
    $('#c-image-preview').classList.add('hidden');
    clearErrors(form);
    const tEl = $('#modal-categoryFormModal').querySelector('h3');
    if (tEl) tEl.textContent = 'Nieuwe categorie';
    showModal('modal-categoryFormModal');
  });

  async function openEdit(id) {
    try {
      const res = await fetch(`/admin/webshop/categories/${id}`, { headers: { 'Accept':'application/json' } });
      const json = await res.json();
      const cat = json.category;
      editId = cat.id;
      $('#categoryId').value = cat.id;
      $('#c-name').value = cat.name;
      setIconPickerValue(cat.icon || '');
      const descEl = $('#c-description');
      if (descEl) descEl.value = cat.description || '';
      $('#c-status').value = cat.status ? '1' : '0';
      const preview = $('#c-image-preview');
      if (cat.image) {
        preview.querySelector('img').src = `/storage/${cat.image}`;
        preview.classList.remove('hidden');
      } else {
        preview.classList.add('hidden');
      }
      clearErrors($('#categoryForm'));
      const titleEl = $('#modal-categoryFormModal').querySelector('h3');
      if (titleEl) titleEl.textContent = 'Categorie bewerken';
      showModal('modal-categoryFormModal');
    } catch (e) { alert('Fout bij laden'); }
  }

  async function openDetails(id) {
    try {
      const res = await fetch(`/admin/webshop/categories/${id}`, { headers: { 'Accept':'application/json' } });
      const json = await res.json();
      const cat = json.category;
      deleteId = cat.id;
      const iconBadge = cat.icon ? `<span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"><i data-lucide="${escapeHtml(cat.icon)}" class="h-3.5 w-3.5"></i>${escapeHtml(cat.icon)}</span>` : `<span class="text-xs" style="color:var(--c-muted)">Geen pictogram</span>`;
      const descBlock = cat.description ? `<p class="mt-2 text-sm leading-5" style="color:var(--c-body)">${escapeHtml(cat.description)}</p>` : `<p class="mt-2 text-xs" style="color:var(--c-muted)">Geen beschrijving</p>`;
      const html = `
        <div class="flex gap-4">
          ${cat.image ? `<img src="/storage/${cat.image}" class="h-20 w-28 rounded-xl border object-cover" style="border-color:rgba(148,163,184,.2)">` : `<span class="flex h-20 w-28 items-center justify-center rounded-xl border bg-slate-50 text-xs" style="border-color:rgba(148,163,184,.2)">Geen afbeelding</span>`}
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-extrabold" style="color:var(--c-heading)">${escapeHtml(cat.name)}</h3>
            <p class="text-xs" style="color:var(--c-muted)">${escapeHtml(cat.slug)} — ${cat.status ? 'Actief' : 'Inactief'} — ${cat.products_count ?? 0} producten</p>
            <div class="mt-2">${iconBadge}</div>
            ${descBlock}
          </div>
        </div>`;
      $('#categoryDetailsContent').innerHTML = html;
      if (window.lucide && window.lucide.createIcons) { try { window.lucide.createIcons(); } catch(e) {} }
      showModal('modal-categoryDetailsModal');
    } catch (e) { alert('Fout'); }
  }

  function openDelete(id) {
    const cat = currentData.find(c => String(c.id)===String(id));
    deleteId = id;
    $('#deleteCategoryName').textContent = cat ? cat.name : 'deze categorie';
    showModal('modal-categoryDeleteModal');
  }

  $('#categoryDetailsEditBtn')?.addEventListener('click', () => {
    hideModal('modal-categoryDetailsModal');
    if (deleteId) openEdit(deleteId);
  });

  $('#categoryDetailsDeleteBtn')?.addEventListener('click', () => {
    hideModal('modal-categoryDetailsModal');
    if (deleteId) openDelete(deleteId);
  });

  // Image preview
  $('#c-image')?.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const preview = $('#c-image-preview');
    preview.querySelector('img').src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
  });

  // Save
  $('#categorySaveBtn')?.addEventListener('click', async () => {
    const form = $('#categoryForm');
    clearErrors(form);
    const formData = new FormData(form);
    const isEdit = !!$('#categoryId').value;
    const url = isEdit ? `/admin/webshop/categories/${$('#categoryId').value}` : '/admin/webshop/categories';
    const method = isEdit ? 'POST' : 'POST';
    if (isEdit) formData.append('_method', 'PUT');

    const btn = $('#categorySaveBtn');
    const label = btn.querySelector('[data-btn-label]');
    const original = label.textContent;
    label.textContent = 'Opslaan...';
    btn.disabled = true;

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
      });
      const json = await res.json();
      if (!res.ok) {
        if (json.errors) showErrors(form, json.errors);
        throw new Error(json.message || 'Validatie fout');
      }
      hideModal('modal-categoryFormModal');
      if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Categorie succesvol opgeslagen.');
      load();
    } catch (err) {
      if (!err.message.includes('Validatie')) alert(err.message);
    } finally {
      label.textContent = original;
      btn.disabled = false;
    }
  });

  // Delete confirm
  $('#categoryDeleteConfirmBtn')?.addEventListener('click', async () => {
    if (!deleteId) return;
    const btn = $('#categoryDeleteConfirmBtn');
    btn.disabled = true;
    try {
      const res = await fetch(`/admin/webshop/categories/${deleteId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
      });
      const json = await res.json();
      if (!res.ok) throw new Error(json.message || 'Fout');
      hideModal('modal-categoryDeleteModal');
      if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Categorie succesvol verwijderd.');
      load();
    } catch (err) {
      alert(err.message);
    } finally { btn.disabled = false; }
  });

  load();
})();

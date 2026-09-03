(() => {
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => [...r.querySelectorAll(s)];

  const els = {
    search: $('#productSearch'),
    category: $('#productCategoryFilter'),
    status: $('#productStatusFilter'),
    brand: $('#productBrandFilter'),
    stock: $('#productStockFilter'),
    minPrice: $('#productMinPrice'),
    maxPrice: $('#productMaxPrice'),
    perPage: $('#productPerPage'),
    tbody: $('#productTableBody'),
    pagination: $('#productPagination'),
    countTotal: $('#countTotal'),
    countActive: $('#countActive'),
    countInactive: $('#countInactive'),
    countInStock: $('#countInStock'),
    countFeatured: $('#countFeatured'),
  };

  let state = { page: 1, search: '', category_id: 'all', brand: 'all', status: 'all', stock_status: 'all', min_price: '', max_price: '', per_page: 15 };
  let currentData = [];
  let deleteId = null;
  let editId = null;

  const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

  const showModal = (id) => {
    const el = document.getElementById(id);
    if (!el) return; el.classList.remove('hidden'); el.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden';
  };
  const hideModal = (id) => {
    const el = document.getElementById(id);
    if (!el) return; el.classList.add('hidden'); el.setAttribute('aria-hidden','true'); document.body.style.overflow='';
  };
  $$('[data-modal-close]').forEach(btn => btn.addEventListener('click', () => {
    const modal = btn.closest('[id^="modal-"]'); if (modal) hideModal(modal.id);
  }));
  $$('[data-modal-overlay]').forEach(ov => ov.addEventListener('click', () => {
    const modal = ov.closest('[id^="modal-"]'); if (modal) hideModal(modal.id);
  }));

  const clearErrors = (form) => {
    form.querySelectorAll('.field-error').forEach(e => { e.textContent=''; e.classList.add('hidden'); });
    form.querySelectorAll('.border-red-500').forEach(e => e.classList.remove('border-red-500'));
  };
  const showErrors = (form, errors) => {
    Object.entries(errors).forEach(([field, msgs]) => {
      const input = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
      if (!input) return;
      const errEl = input.closest('div')?.querySelector('.field-error') || input.parentElement?.querySelector('.field-error');
      if (errEl) { errEl.textContent = Array.isArray(msgs)?msgs[0]:msgs; errEl.classList.remove('hidden'); }
      input.classList.add('border-red-500');
    });
  };
  const escapeHtml = (s) => s==null?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

  async function fetchData() {
    const params = new URLSearchParams({
      page: state.page, search: state.search, category_id: state.category_id, brand: state.brand,
      status: state.status, stock_status: state.stock_status, min_price: state.min_price, max_price: state.max_price, per_page: state.per_page
    });
    const res = await fetch(`/admin/webshop/products/data?${params}`, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Failed');
    return res.json();
  }

  function renderTable(products) {
    currentData = products.data;
    if (!products.data.length) {
      els.tbody.innerHTML = `<tr><td colspan="12" class="px-6 py-16 text-center"><div class="flex flex-col items-center gap-3"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M6 13.5V3.75a.75.75 0 01.75-.75h9a.75.75 0 01.75.75v9.75m-10.5 0a3.75 3.75 0 003.75 3.75h3a3.75 3.75 0 003.75-3.75M6 13.5h12"/></svg></span><p class="text-sm font-semibold" style="color:var(--c-heading)">Geen producten gevonden</p></div></td></tr>`;
      return;
    }
    els.tbody.innerHTML = products.data.map(p => {
      const img = p.main_image ? `<img src="/storage/${escapeHtml(p.main_image)}" alt="" class="h-10 w-10 rounded-lg border object-cover" style="border-color:rgba(148,163,184,.2)">` : `<span class="flex h-10 w-10 items-center justify-center rounded-lg border bg-slate-50 text-[10px] font-bold" style="border-color:rgba(148,163,184,.2);color:var(--c-muted)">Geen</span>`;
      const stockBadge = p.stock_status==='in_stock'
        ? `<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Op voorraad</span>`
        : `<span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Niet op voorraad</span>`;
      
      const homeSwitch = `<button type="button" role="switch" aria-checked="${p.is_featured ? 'true' : 'false'}" data-toggle-featured="${p.id}" data-featured="${p.is_featured ? 1 : 0}" class="apple-switch ${p.is_featured ? 'is-active' : 'is-inactive'}" title="${p.is_featured ? 'Op Home (klik om te verbergen)' : 'Niet op Home (klik om te tonen)'}"><span class="apple-knob"></span></button>`;

      const statusSwitch = `<button type="button" role="switch" aria-checked="${p.status ? 'true' : 'false'}" data-toggle-status="${p.id}" data-status="${p.status ? 1 : 0}" class="apple-switch ${p.status ? 'is-active' : 'is-inactive'}" title="${p.status ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)'}"><span class="apple-knob"></span></button>`;

      const discount = p.discount_value ? (p.discount_type==='percentage' ? `${p.discount_value}%` : `€${p.discount_value}`) : '—';
      const oldPrice = p.old_price ? `€${Number(p.old_price).toFixed(2)}` : '—';
      const avg = p.rating_avg ? Number(p.rating_avg).toFixed(1) : '—';
      const cnt = p.rating_count || 0;
      const ratingCell = `<button type="button" data-reviews="${p.id}" title="Beoordelingen bekijken" class="inline-flex items-center gap-1.5 rounded-full ${cnt>0 ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 hover:bg-amber-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'} px-2.5 py-1 text-[11px] font-bold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 ${cnt>0 ? 'text-amber-400' : 'text-slate-400'}"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.37 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.84-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.34 8.719c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/></svg>${avg}<span class="font-normal">(${cnt})</span></button>`;
      return `<tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
        <td class="px-3 py-3">${img}</td>
        <td class="px-3 py-3"><div class="text-sm font-semibold line-clamp-1" style="color:var(--c-heading)">${escapeHtml(p.title)}</div></td>
        <td class="px-3 py-3 text-xs" style="color:var(--c-muted)">${escapeHtml(p.brand||'—')}</td>
        <td class="px-3 py-3 text-xs"><span class="rounded-full bg-slate-100 px-2 py-1 font-bold" style="color:var(--c-heading)">${escapeHtml(p.category?.name||'—')}</span></td>
        <td class="px-3 py-3 text-sm font-bold" style="color:var(--c-heading)">€${Number(p.price).toFixed(2)}</td>
        <td class="px-3 py-3 text-xs line-through" style="color:var(--c-muted)">${oldPrice}</td>
        <td class="px-3 py-3 text-xs font-bold">${discount}</td>
        <td class="px-3 py-3">${stockBadge}</td>
        <td class="px-3 py-3">${homeSwitch}</td>
        <td class="px-3 py-3 text-center">${ratingCell}</td>
        <td class="px-3 py-3">${statusSwitch}</td>
        <td class="w-[100px] min-w-[100px] max-w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
          <div class="flex items-center justify-end gap-1">
            <button type="button" data-edit="${p.id}" title="Bewerken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></button>
            <button type="button" data-delete="${p.id}" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
          </div>
        </td>
      </tr>`;
    }).join('');

    $$('[data-toggle-status]').forEach(btn => btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-toggle-status');
      const current = parseInt(btn.getAttribute('data-status'), 10);
      const newStatus = current ? 0 : 1;
      
      btn.setAttribute('data-status', newStatus);
      btn.setAttribute('aria-checked', newStatus ? 'true' : 'false');
      btn.className = `apple-switch ${newStatus ? 'is-active' : 'is-inactive'}`;
      btn.title = newStatus ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)';

      try {
        const res = await fetch(`/admin/webshop/products/${id}/toggle`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({ status: newStatus })
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Fout');
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Status bijgewerkt.');
        load();
      } catch (err) {
        btn.setAttribute('data-status', current);
        btn.setAttribute('aria-checked', current ? 'true' : 'false');
        btn.className = `apple-switch ${current ? 'is-active' : 'is-inactive'}`;
        btn.title = current ? 'Actief (klik om te deactiveren)' : 'Inactief (klik om te activeren)';
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(err.message);
        else alert(err.message);
      }
    }));

    $$('[data-toggle-featured]').forEach(btn => btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-toggle-featured');
      const current = parseInt(btn.getAttribute('data-featured'), 10);
      const newFeatured = current ? 0 : 1;
      
      btn.setAttribute('data-featured', newFeatured);
      btn.setAttribute('aria-checked', newFeatured ? 'true' : 'false');
      btn.className = `apple-switch ${newFeatured ? 'is-active' : 'is-inactive'}`;
      btn.title = newFeatured ? 'Op Home (klik om te verbergen)' : 'Niet op Home (klik om te tonen)';

      try {
        const res = await fetch(`/admin/webshop/products/${id}/toggle-featured`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({ is_featured: newFeatured })
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Fout');
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Home-status bijgewerkt.');
        load();
      } catch (err) {
        btn.setAttribute('data-featured', current);
        btn.setAttribute('aria-checked', current ? 'true' : 'false');
        btn.className = `apple-switch ${current ? 'is-active' : 'is-inactive'}`;
        btn.title = current ? 'Op Home (klik om te verbergen)' : 'Niet op Home (klik om te tonen)';
        if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(err.message);
        else alert(err.message);
      }
    }));

    $$('[data-reviews]').forEach(b=> b.addEventListener('click', ()=> openReviews(b.getAttribute('data-reviews'))));
    $$('[data-edit]').forEach(b => b.addEventListener('click', ()=> openEdit(b.getAttribute('data-edit'))));
    $$('[data-delete]').forEach(b => b.addEventListener('click', ()=> openDelete(b.getAttribute('data-delete'))));
  }

  let currentReviewProductId = null;
  async function openReviews(productId){
    currentReviewProductId = productId;
    const p = currentData.find(x=>String(x.id)===String(productId));
    const header = document.getElementById('productReviewsHeader');
    const stats = document.getElementById('productReviewsStats');
    const list = document.getElementById('productReviewsList');
    const empty = document.getElementById('productReviewsEmpty');
    if(header) header.innerHTML = p ? `<span class="font-bold" style="color:var(--c-heading)">${escapeHtml(p.title)}</span> <span>— beoordelingen</span>` : 'Beoordelingen';
    if(list) list.innerHTML = '<div class="flex justify-center py-8"><svg class="h-6 w-6 animate-spin text-blue-600" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"/></svg></div>';
    if(empty) empty.classList.add('hidden');
    showModal('modal-productReviewsModal');
    try{
      const res = await fetch(`/admin/webshop/reviews/product/${productId}`, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
      const json = await res.json();
      const avg = json.avg ? Number(json.avg).toFixed(1) : '—';
      const cnt = json.count || 0;
      const data = json.reviews?.data || json.reviews || [];
      if(stats) stats.innerHTML = `<div class="rounded-xl bg-amber-50 p-3 dark:bg-amber-900/20"><div class="text-lg font-extrabold text-amber-600">${avg}</div><div class="text-[10px]">Gemiddelde</div></div><div class="rounded-xl bg-blue-50 p-3 dark:bg-blue-900/20"><div class="text-lg font-extrabold text-blue-600">${cnt}</div><div class="text-[10px]">Goedgekeurd</div></div><div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800"><div class="text-lg font-extrabold" style="color:var(--c-heading)">${data.length}</div><div class="text-[10px]" style="color:var(--c-muted)">Totaal geladen</div></div>`;
      if(!data.length){
        if(list) list.innerHTML = '';
        if(empty) empty.classList.remove('hidden');
        return;
      }
      if(empty) empty.classList.add('hidden');
      list.innerHTML = data.map(r=>{
        const stars = '★'.repeat(r.rating) + '☆'.repeat(5-r.rating);
        const author = escapeHtml(r.guest_name || r.user?.name || 'Gast');
        const approved = r.is_approved ? '<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600">Goedgekeurd</span>' : '<span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-600">In afwachting</span>';
        const actions = r.is_approved
          ? `<button data-reject="${r.id}" class="rounded-lg border px-2.5 py-1 text-xs font-bold hover:bg-amber-50">Afkeuren</button>`
          : `<button data-approve="${r.id}" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-emerald-700">Goedkeuren</button>`;
        return `<div class="rounded-xl border p-3" style="border-color:rgba(148,163,184,.2); background-color:var(--c-card)">
          <div class="flex items-center justify-between gap-2">
            <div class="text-xs font-bold" style="color:var(--c-heading)">${author} <span class="font-normal" style="color:var(--c-muted)">• ${new Date(r.created_at).toLocaleDateString('nl-NL')}</span></div>
            <div class="flex items-center gap-1.5">${approved}</div>
          </div>
          <div class="mt-1 text-xs text-amber-500">${stars} <span class="text-[11px] font-bold text-slate-700">${r.rating}/5</span></div>
          ${r.title ? `<div class="mt-1 text-sm font-semibold" style="color:var(--c-heading)">${escapeHtml(r.title)}</div>` : ''}
          <p class="mt-1 text-sm" style="color:var(--c-body)">${escapeHtml(r.body)}</p>
          <div class="mt-2 flex gap-1.5">
            ${actions}
            <button data-delete-review="${r.id}" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-bold text-red-600 hover:bg-red-50">Verwijderen</button>
          </div>
        </div>`;
      }).join('');
      list.querySelectorAll('[data-approve]').forEach(b=> b.addEventListener('click', async()=>{
        const id=b.getAttribute('data-approve');
        b.disabled=true;
        try{ const res=await fetch(`/admin/webshop/reviews/${id}/approve`,{method:'POST',headers:{'X-CSRF-TOKEN':getCsrf(),'Accept':'application/json'}}); const j=await res.json(); if(!res.ok) throw new Error(j.message); if(window.SlimmePC) window.SlimmePC.toast.success(j.message); openReviews(currentReviewProductId); load(); }catch(e){ alert(e.message)} finally{ b.disabled=false;}
      }));
      list.querySelectorAll('[data-reject]').forEach(b=> b.addEventListener('click', async()=>{
        const id=b.getAttribute('data-reject');
        b.disabled=true;
        try{ const res=await fetch(`/admin/webshop/reviews/${id}/reject`,{method:'POST',headers:{'X-CSRF-TOKEN':getCsrf(),'Accept':'application/json'}}); const j=await res.json(); if(!res.ok) throw new Error(j.message); if(window.SlimmePC) window.SlimmePC.toast.success(j.message); openReviews(currentReviewProductId); load(); }catch(e){ alert(e.message)} finally{ b.disabled=false;}
      }));
      list.querySelectorAll('[data-delete-review]').forEach(b=> b.addEventListener('click', async()=>{
        const id=b.getAttribute('data-delete-review');
        if(!confirm('Verwijderen?')) return;
        b.disabled=true;
        try{ const res=await fetch(`/admin/webshop/reviews/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':getCsrf(),'Accept':'application/json'}}); const j=await res.json(); if(!res.ok) throw new Error(j.message); if(window.SlimmePC) window.SlimmePC.toast.success(j.message); openReviews(currentReviewProductId); load(); }catch(e){ alert(e.message)} finally{ b.disabled=false;}
      }));
    }catch(e){ if(list) list.innerHTML = `<p class="text-sm text-red-500">Fout: ${escapeHtml(e.message)}</p>`; }
  }

  function renderPagination(p){
    if(!p||p.last_page<=1){ els.pagination.innerHTML=''; return; }
    let html = `<div class="flex flex-wrap items-center justify-between gap-2 text-xs"><span style="color:var(--c-muted)">Pagina ${p.current_page} van ${p.last_page} — ${p.total} resultaten</span><div class="flex gap-1">`;
    for(let i=1;i<=p.last_page;i++){ const a=i===p.current_page; html+=`<button data-page="${i}" class="min-w-[36px] rounded-lg px-3 py-1.5 font-bold ${a?'bg-blue-600 text-white':'border bg-white hover:bg-slate-50'}" style="${a?'':'border-color:var(--c-input-border);color:var(--c-heading)'}">${i}</button>`; }
    html+=`</div></div>`; els.pagination.innerHTML=html;
    $$('[data-page]').forEach(b=>b.addEventListener('click',()=>{ state.page=parseInt(b.getAttribute('data-page')); load(); }));
  }

  function renderCounts(c){
    if (els.countTotal) els.countTotal.textContent=`Totaal: ${c.total}`;
    if (els.countActive) els.countActive.textContent=`Actief: ${c.active}`;
    if (els.countInactive) els.countInactive.textContent=`Inactief: ${c.inactive}`;
    if (els.countInStock) els.countInStock.textContent=`Op voorraad: ${c.in_stock}`;
    if (els.countFeatured) els.countFeatured.textContent=`Op Home: ${c.featured ?? 0}`;
  }

  async function load(){
    if (window.AdminTable && els.tbody) window.AdminTable.loading(els.tbody, 12);
    try{
      const json = await fetchData();
      renderTable(json.products);
      renderPagination(json.products);
      renderCounts(json.counts);
    }catch(e){ els.tbody.innerHTML=`<tr><td colspan="12" class="px-6 py-12 text-center text-sm" style="color:var(--c-muted)">Fout: ${escapeHtml(e.message)}</td></tr>`; }
  }

  let t;
  els.search?.addEventListener('input', e=>{ clearTimeout(t); t=setTimeout(()=>{ state.search=e.target.value; state.page=1; load(); },350); });
  ['category','status','brand','stock'].forEach(k=>{
    const el = els[k]; if(!el) return;
    el.addEventListener('change', e=>{ state[k==='category'?'category_id':k==='stock'?'stock_status':k]=e.target.value; state.page=1; load(); });
  });
  els.minPrice?.addEventListener('change', e=>{ state.min_price=e.target.value; state.page=1; load(); });
  els.maxPrice?.addEventListener('change', e=>{ state.max_price=e.target.value; state.page=1; load(); });
  els.perPage?.addEventListener('change', e=>{ state.per_page=e.target.value; state.page=1; load(); });

  // dynamic fields
  function addField(containerId, name, value=''){
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className='flex gap-2';
    div.innerHTML=`<input type="text" name="${name}[]" value="${escapeHtml(value)}" placeholder="Waarde" class="form-input h-10 flex-1 text-sm" style="background-color:var(--c-input-bg);border-color:var(--c-input-border);color:var(--c-heading)"><button type="button" class="rounded-lg border p-2 text-red-500 hover:bg-red-50" onclick="this.parentElement.remove()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    container.appendChild(div);
  }

  $('#p-add-feature')?.addEventListener('click', ()=> addField('p-features-container','features'));
  $('#p-add-color')?.addEventListener('click', ()=> addField('p-colors-container','colors'));
  $('#p-add-size')?.addEventListener('click', ()=> addField('p-sizes-container','sizes'));

  // Create is now a separate page: /admin/webshop/products/create
  // Edit is now a separate page: /admin/webshop/products/{id}/edit
  async function openEdit(id){
    window.location.href = `/admin/webshop/products/${id}/edit`;
  }

  async function openDetails(id){
    try{
      const res=await fetch(`/admin/webshop/products/${id}`,{headers:{'Accept':'application/json'}});
      const json=await res.json(); const p=json.product;
      deleteId=p.id;
      const html=`<div class="flex gap-4">${p.main_image?`<img src="/storage/${p.main_image}" class="h-20 w-20 rounded-xl border object-cover">`:`<span class="flex h-20 w-20 items-center justify-center rounded-xl border bg-slate-50 text-xs">Geen</span>`}<div><h3 class="text-lg font-extrabold" style="color:var(--c-heading)">${escapeHtml(p.title)}</h3><p class="text-xs" style="color:var(--c-muted)">${escapeHtml(p.category?.name||'')} — €${Number(p.price).toFixed(2)}</p></div></div><div class="grid grid-cols-2 gap-3 text-sm"><div><span style="color:var(--c-muted)">Merk:</span> <b style="color:var(--c-heading)">${escapeHtml(p.brand||'—')}</b></div><div><span style="color:var(--c-muted)">SKU:</span> <b>${escapeHtml(p.sku||'—')}</b></div><div><span style="color:var(--c-muted)">Voorraad:</span> <b>${p.stock_status==='in_stock' ? 'Op voorraad' : 'Niet op voorraad'}</b></div><div><span style="color:var(--c-muted)">Status:</span> <b>${p.status?'Actief':'Inactief'}</b></div></div><p class="text-sm" style="color:var(--c-body)">${escapeHtml(p.description||'Geen beschrijving')}</p>`;
      $('#productDetailsContent').innerHTML=html;
      showModal('modal-productDetailsModal');
    }catch(e){ alert('Fout'); }
  }

  function openDelete(id){
    const p=currentData.find(x=>String(x.id)===String(id));
    deleteId=id;
    $('#deleteProductName').textContent=p?p.title:'dit product';
    showModal('modal-productDeleteModal');
  }

  $('#productDetailsEditBtn')?.addEventListener('click',()=>{ hideModal('modal-productDetailsModal'); if(deleteId) openEdit(deleteId); });
  $('#productDetailsDeleteBtn')?.addEventListener('click',()=>{ hideModal('modal-productDetailsModal'); if(deleteId) openDelete(deleteId); });

  $('#p-main-image')?.addEventListener('change', e=>{
    const file=e.target.files[0]; if(!file) return;
    const preview=$('#p-main-preview'); preview.querySelector('img').src=URL.createObjectURL(file); preview.classList.remove('hidden');
  });

  $('#productSaveBtn')?.addEventListener('click', async()=>{
    const form=$('#productForm'); clearErrors(form);
    const formData=new FormData(form);
    const isEdit=!!$('#productId').value;
    const url=isEdit?`/admin/webshop/products/${$('#productId').value}`:'/admin/webshop/products';
    if(isEdit) formData.append('_method','PUT');
    const btn=$('#productSaveBtn'); const label=btn.querySelector('[data-btn-label]'); const orig=label.textContent; label.textContent='Opslaan...'; btn.disabled=true;
    try{
      const res=await fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:formData});
      const json=await res.json();
      if(!res.ok){ if(json.errors) showErrors(form,json.errors); throw new Error(json.message||'Validatie fout'); }
      hideModal('modal-productFormModal'); load();
    }catch(err){ if(!err.message.includes('Validatie')) alert(err.message); }
    finally{ label.textContent=orig; btn.disabled=false; }
  });

  $('#productDeleteConfirmBtn')?.addEventListener('click', async()=>{
    if(!deleteId) return; const btn=$('#productDeleteConfirmBtn'); btn.disabled=true;
    try{
      const res=await fetch(`/admin/webshop/products/${deleteId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':getCsrf(),'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
      const json=await res.json(); if(!res.ok) throw new Error(json.message||'Fout');
      hideModal('modal-productDeleteModal');
      if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.success(json.message || 'Product succesvol verwijderd.');
      load();
    }catch(err){ alert(err.message); }
    finally{ btn.disabled=false; }
  });

  load();
})();

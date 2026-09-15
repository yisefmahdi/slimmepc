/* Live Chat inbox — admin (spiegelt contact-inbox) */
(function () {
    'use strict';

    const listEl = document.getElementById('chatList');
    if (!listEl) return;

    const paginationEl = document.getElementById('chatPagination');
    const searchEl = document.getElementById('chatSearch');
    const statusEl = document.getElementById('chatStatusFilter');
    const perPageEl = document.getElementById('chatPerPage');
    const chatPane = document.getElementById('chatPane');
    const listPane = document.getElementById('chatListPane');
    const emptyEl = document.getElementById('chatEmpty');
    const threadWrap = document.getElementById('chatThread');
    const threadEl = document.getElementById('chatMessages');
    const replyEl = document.getElementById('chatReply');
    const replyBtn = document.getElementById('chatReplyBtn');
    const attachBtn = document.getElementById('chatAttachBtn');
    const attachFile = document.getElementById('chatAttachFile');
    const attachChip = document.getElementById('chatAttachChip');
    const attachName = document.getElementById('chatAttachName');
    const attachRemove = document.getElementById('chatAttachRemove');
    const statusSelect = document.getElementById('chatStatusSelect');
    const deleteBtn = document.getElementById('chatDeleteBtn');
    const deleteConfirmBtn = document.getElementById('chatDeleteConfirmBtn');
    const backBtn = document.getElementById('chatBackBtn');
    const aiToggle = document.getElementById('chatAiToggle');
    const aiDot = document.getElementById('chatAiDot');
    const aiLabel = document.getElementById('chatAiLabel');
    const aiNote = document.getElementById('chatAiNote');

    let currentId = null;
    let currentPage = 1;
    let searchTimer = null;
    let syncTimer = null;
    let lastUnread = 0;
    let threadSeq = 0;

    const STATUS_LABEL = { ai: 'AI actief', open: 'Open', handed_over: 'Medewerker', closed: 'Gesloten', offline: 'Offline' };

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
    function fmtDate(iso) {
        try {
            const d = new Date(iso);
            return String(d.getDate()).padStart(2, '0') + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + d.getFullYear() + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        } catch (e) { return '—'; }
    }
    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            [660, 880].forEach((freq, i) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = freq;
                const t = ctx.currentTime + i * 0.15;
                gain.gain.setValueAtTime(0.12, t);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.14);
                osc.start(t);
                osc.stop(t + 0.15);
            });
        } catch (e) { /* geen audio */ }
    }

    function load(silent) {
        if (!silent && window.AdminTable) window.AdminTable.loading(listEl, 1);
        const params = new URLSearchParams({
            search: searchEl.value.trim(),
            status: statusEl.value,
            per_page: perPageEl.value,
            page: currentPage,
        });
        fetch('/admin/chat/inbox/data?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            renderList(data.data || []);
            renderPagination(data.pagination);
            renderCounts(data.counts || {});
            const unread = (data.counts && data.counts.unread) || 0;
            if (silent && unread > lastUnread) beep();
            lastUnread = unread;
            const badge = document.getElementById('sidebarChatBadge');
            if (badge) {
                const handover = (data.counts && data.counts.handover) || 0;
                badge.textContent = handover;
                badge.classList.toggle('hidden', handover <= 0);
            }
        }).catch(() => {});
    }

    function renderList(items) {
        if (!items.length) {
            listEl.innerHTML = '<div class="flex flex-col items-center gap-3 px-6 py-16 text-center"><p class="font-semibold" style="color:var(--c-heading)">Geen gesprekken gevonden</p><p class="text-xs" style="color:var(--c-muted)">Pas je zoekopdracht of filters aan.</p></div>';
            return;
        }
        listEl.innerHTML = items.map(row => {
            const last = row.last_message || {};
            const preview = (last.sender === 'admin' ? 'Jij: ' : last.sender === 'ai' ? 'AI: ' : '') + (last.body || (last.sender ? '📷 Foto' : '—'));
            const unread = row.unread > 0 ? '<span class="ml-auto inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">' + row.unread + '</span>' : '';
            const statusColor = row.status === 'handed_over' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                : row.status === 'closed' ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'
                : row.status === 'offline' ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'
                : 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300';
            const stars = row.rating ? '<span class="text-amber-500" title="Beoordeling">★ ' + row.rating + '</span>' : '';
            return '<button type="button" data-open="' + row.id + '" class="mb-1.5 flex w-full items-center gap-3 rounded-xl border border-transparent p-3 text-left transition hover:border-blue-200 hover:bg-blue-50/50 dark:hover:bg-slate-800/60' + (row.id === currentId ? ' !border-blue-300 !bg-blue-50/70 dark:!bg-blue-900/20' : '') + '">'
                + '<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-sm font-bold text-white">' + escapeHtml((row.name || '?').trim().charAt(0).toUpperCase()) + '</span>'
                + '<span class="min-w-0 flex-1">'
                + '<span class="flex items-center gap-1.5 text-sm font-bold" style="color:var(--c-heading)"><span class="truncate">' + escapeHtml(row.name) + '</span>' + unread + '</span>'
                + '<span class="mt-0.5 block truncate text-xs" style="color:var(--c-muted)">' + escapeHtml(preview) + '</span>'
                + '<span class="mt-1 flex items-center gap-1.5 text-[10px]"><span class="inline-flex rounded-full px-2 py-0.5 font-bold ' + statusColor + '">' + escapeHtml(STATUS_LABEL[row.status] || row.status) + '</span>' + stars + '</span>'
                + '</span></button>';
        }).join('');
        listEl.querySelectorAll('[data-open]').forEach(btn => {
            btn.addEventListener('click', () => openThread(parseInt(btn.dataset.open, 10)));
        });
    }

    function renderPagination(p) {
        if (!p || p.last <= 1) { paginationEl.innerHTML = ''; return; }
        let html = '<div class="flex items-center justify-center gap-2 text-xs"><button type="button" data-nav="prev" class="rounded-lg px-2.5 py-1 font-bold hover:bg-slate-100 dark:hover:bg-slate-800" style="color:var(--c-heading)">‹</button><span style="color:var(--c-muted)">' + p.total + ' · ' + p.current + '/' + p.last + '</span><button type="button" data-nav="next" class="rounded-lg px-2.5 py-1 font-bold hover:bg-slate-100 dark:hover:bg-slate-800" style="color:var(--c-heading)">›</button></div>';
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('[data-nav]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.nav === 'prev' && currentPage > 1) currentPage--;
                if (btn.dataset.nav === 'next' && currentPage < p.last) currentPage++;
                load();
            });
        });
    }

    function renderCounts(c) {
        document.getElementById('chatCountOpen').textContent = 'Open: ' + (c.open || 0);
        document.getElementById('chatCountHandover').textContent = 'Medewerker: ' + (c.handover || 0);
        document.getElementById('chatCountUnread').textContent = 'Ongelezen: ' + (c.unread || 0);
        document.getElementById('chatCountRating').textContent = '★ ' + (c.avg_rating || '—');
    }

    function openThread(id) {
        currentId = id;
        const seq = ++threadSeq;
        threadWrap.classList.remove('hidden');
        threadWrap.classList.add('flex');
        emptyEl.classList.add('hidden');
        chatPane.classList.remove('hidden');
        chatPane.classList.add('flex');
        listPane.classList.add('hidden');
        listPane.classList.remove('flex');
        if (window.innerWidth >= 1024) { listPane.classList.remove('hidden'); listPane.classList.add('flex'); }
        threadEl.innerHTML = '<div class="flex justify-center py-8"><svg class="h-8 w-8 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg></div>';
        fetch('/admin/chat/inbox/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                if (seq !== threadSeq) return;
                const c = data.conversation;
                document.getElementById('chatAvatar').textContent = (c.name || '?').trim().charAt(0).toUpperCase();
                document.getElementById('chatName').textContent = c.name || '—';
                document.getElementById('chatMeta').textContent = (c.email || '') + ' · ' + (STATUS_LABEL[c.status] || c.status);
                document.getElementById('chatEmail').textContent = c.email || '—';
                document.getElementById('chatAccount').textContent = c.user ? (c.user.name + (c.user.klantnummer ? ' (' + c.user.klantnummer + ')' : '')) : 'Gast';
                document.getElementById('chatDate').textContent = fmtDate(c.created_at);
                statusSelect.value = c.status;
                paintAi(c.ai_enabled);
                paintLock(c.status);
                const ratingBadge = document.getElementById('chatRatingBadge');
                const ratingChip = document.getElementById('chatRatingChip');
                if (c.rating) {
                    ratingBadge.classList.remove('hidden');
                    ratingBadge.classList.add('inline-flex');
                    ratingBadge.textContent = '★ ' + c.rating;
                    ratingChip.classList.remove('hidden');
                    ratingChip.classList.add('inline-flex');
                    document.getElementById('chatRatingText').textContent = '★ ' + c.rating + (c.rating_comment ? ' — ' + c.rating_comment : '');
                    ratingBadge.title = c.rating_comment || 'Beoordeling';
                } else {
                    ratingBadge.classList.add('hidden');
                    ratingBadge.classList.remove('inline-flex');
                    ratingChip.classList.add('hidden');
                    ratingChip.classList.remove('inline-flex');
                }
                threadEl.innerHTML = (data.messages || []).map(renderBubble).join('') || '<p class="py-8 text-center text-xs" style="color:var(--c-muted)">Nog geen berichten.</p>';
                threadEl.scrollTop = threadEl.scrollHeight;
                load(true);
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('conversation', id);
                    window.history.replaceState({}, '', url);
                } catch (e) { /* noop */ }
            }).catch(() => toastErr('Gesprek laden mislukt.'));
    }

    function renderBubble(m) {
        const photo = m.photo_url ? '<a data-photo href="' + escapeHtml(m.photo_url) + '" class="mt-2 block max-w-[240px] cursor-zoom-in overflow-hidden rounded-xl border border-slate-200/70"><img src="' + escapeHtml(m.photo_url) + '" alt="Foto" loading="lazy" class="max-h-48 w-auto object-cover"></a>' : '';
        if (m.sender === 'admin') {
            return '<div class="flex justify-end"><div class="max-w-[85%] rounded-2xl rounded-tr-md bg-gradient-to-r from-[#075be8] to-[#064bd7] px-3.5 py-2.5 text-[13px] leading-relaxed text-white shadow-sm">' + escapeHtml(m.body || '') + photo + '</div></div>';
        }
        const aiLabel = m.sender === 'ai' ? '<span class="mb-1 block text-[10px] font-extrabold uppercase tracking-wide text-violet-500">AI</span>' : '';
        return '<div class="flex justify-start"><div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">' + aiLabel + escapeHtml(m.body || '') + photo + '</div></div>';
    }

    function senderBadge() { return ''; }

    /* ============ Knop-loading (spinner, design-systeem) ============ */
    const SPINNER_SVG = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
    function btnLoading(btn, on) {
        if (!btn) return;
        if (on) {
            if (btn.dataset.origHtml === undefined) btn.dataset.origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = SPINNER_SVG;
            btn.classList.add('pointer-events-none', 'opacity-80');
        } else {
            if (btn.dataset.origHtml !== undefined) btn.innerHTML = btn.dataset.origHtml;
            btn.disabled = false;
            btn.classList.remove('pointer-events-none', 'opacity-80');
        }
    }

    /* ============ Foto lightbox (popup, geen nieuw tabblad) ============ */
    const lightbox = document.getElementById('chatLightbox');
    const lightboxImg = document.getElementById('chatLightboxImg');
    const lightboxClose = document.getElementById('chatLightboxClose');
    function isLightboxOpen() {
        return !!(lightbox && !lightbox.classList.contains('hidden'));
    }
    function openLightbox(url) {
        if (!lightbox || !lightboxImg || !url) return;
        lightboxImg.src = url;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        lightbox.setAttribute('aria-hidden', 'false');
    }
    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        lightbox.setAttribute('aria-hidden', 'true');
        if (lightboxImg) lightboxImg.src = '';
    }
    if (lightboxClose) lightboxClose.addEventListener('click', (e) => { e.stopPropagation(); closeLightbox(); });
    if (lightbox) lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    threadEl.addEventListener('click', (e) => {
        const a = e.target && e.target.closest ? e.target.closest('a[data-photo]') : null;
        if (!a) return;
        e.preventDefault();
        e.stopPropagation();
        openLightbox(a.getAttribute('href'));
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isLightboxOpen()) closeLightbox();
    });

    function paintAi(on) {
        aiDot.className = 'inline-block h-2 w-2 rounded-full ' + (on ? 'bg-green-500' : 'bg-slate-400');
        aiLabel.textContent = on ? 'AI aan' : 'AI uit';
        aiNote.classList.toggle('hidden', !on);
    }

    function paintLock(status) {
        const locked = status === 'closed';
        const note = document.getElementById('chatLockedNote');
        if (note) {
            note.classList.toggle('hidden', !locked);
            note.classList.toggle('flex', locked);
        }
        [replyEl, attachBtn, replyBtn].forEach(el => { if (el) el.disabled = locked; });
        replyEl.placeholder = locked
            ? 'Ticket gesloten — heropen via de status hierboven.'
            : 'Typ je antwoord hier... ( AI stopt zodra je verstuurt )';
    }

    function sendReply() {
        if (!currentId) return;
        if (replyEl.disabled) {
            toastErr('Ticket gesloten — heropen het gesprek eerst via de status.');
            return;
        }
        const body = replyEl.value.trim().slice(0, 5000);
        const file = attachFile.files && attachFile.files[0];
        if (!body && !file) return;
        const fd = new FormData();
        if (body) fd.append('body', body);
        if (file) fd.append('attachment', file);
        // Optimistisch tonen (geen volledige herlaad meer): eigen bubbel direct
        // toevoegen, daarna alleen lijst/tellers stil bijwerken.
        const tmpId = 'tmp-' + Date.now();
        let tmpPhotoUrl = null;
        if (file) {
            try { tmpPhotoUrl = URL.createObjectURL(file); } catch (e) { tmpPhotoUrl = null; }
        }
        const optimistic = { id: tmpId, sender: 'admin', body: body || '', photo_url: tmpPhotoUrl };
        threadEl.insertAdjacentHTML('beforeend', renderBubble(optimistic));
        const optimisticNode = threadEl.lastElementChild;
        if (optimisticNode) optimisticNode.setAttribute('data-tmp', tmpId);
        threadEl.scrollTop = threadEl.scrollHeight;
        replyEl.value = '';
        clearAttach();
        autoGrow();
        btnLoading(replyBtn, true);
        fetch('/admin/chat/inbox/' + currentId + '/reply', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) {
                const tmpNode = threadEl.querySelector('[data-tmp="' + tmpId + '"]');
                if (tmpNode) tmpNode.remove();
                throw new Error((data.errors && Object.values(data.errors)[0][0]) || data.message || 'Versturen mislukt.');
            }
            // Optimistische bubbel koppelen aan echte data (foto-URL vervangen).
            const node = threadEl.querySelector('[data-tmp="' + tmpId + '"]');
            if (node) {
                node.removeAttribute('data-tmp');
                if (data.reply && data.reply.photo_url) {
                    node.querySelectorAll('a[data-photo]').forEach(a => a.setAttribute('href', data.reply.photo_url));
                    node.querySelectorAll('a[data-photo] img').forEach(img => img.setAttribute('src', data.reply.photo_url));
                }
            }
            if (tmpPhotoUrl) { try { setTimeout(() => URL.revokeObjectURL(tmpPhotoUrl), 120000); } catch (e) {} }
            paintAi(data.ai_enabled);
            load(true);
            toastOk(data.message || 'Verzonden.');
        }).catch(e => toastErr(e.message)).finally(() => { btnLoading(replyBtn, false); });
    }

    function clearAttach() {
        attachFile.value = '';
        attachChip.classList.add('hidden');
        attachChip.classList.remove('flex');
    }

    function autoGrow() {
        replyEl.style.height = 'auto';
        replyEl.style.height = Math.min(Math.max(replyEl.scrollHeight, 52), 160) + 'px';
    }

    replyBtn.addEventListener('click', sendReply);
    replyEl.addEventListener('input', autoGrow);
    replyEl.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendReply(); }
    });

    attachBtn.addEventListener('click', () => attachFile.click());
    attachFile.addEventListener('change', () => {
        const f = attachFile.files && attachFile.files[0];
        if (!f) { clearAttach(); return; }
        if (!f.type.startsWith('image/') || f.size > 10 * 1024 * 1024) {
            toastErr('Alleen afbeeldingen tot 10MB.');
            clearAttach();
            return;
        }
        document.getElementById('chatAttachName').textContent = f.name;
        attachChip.classList.remove('hidden');
        attachChip.classList.add('flex');
    });
    attachRemove.addEventListener('click', clearAttach);

    statusSelect.addEventListener('change', () => {
        if (!currentId) return;
        fetch('/admin/chat/inbox/' + currentId + '/status', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ status: statusSelect.value }),
        }).then(r => r.json()).then(d => { toastOk(d.message || 'Bijgewerkt.'); paintLock(statusSelect.value); load(true); })
        .catch(() => toastErr('Bijwerken mislukt.'));
    });

    aiToggle.addEventListener('click', () => {
        if (!currentId) return;
        fetch('/admin/chat/inbox/' + currentId + '/toggle-ai', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(d => { paintAi(d.ai_enabled); toastOk(d.message || 'Bijgewerkt.'); })
        .catch(() => toastErr('Bijwerken mislukt.'));
    });

    deleteBtn.addEventListener('click', () => {
        if (!currentId) return;
        document.getElementById('chatDeleteName').textContent = 'dit gesprek';
        openModal('chatDeleteModal');
    });
    deleteConfirmBtn.addEventListener('click', () => {
        if (!currentId) return;
        deleteConfirmBtn.disabled = true;
        fetch('/admin/chat/inbox/' + currentId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
        }).then(r => r.json()).then(() => {
            closeModal('chatDeleteModal');
            currentId = null;
            threadWrap.classList.add('hidden');
            threadWrap.classList.remove('flex');
            emptyEl.classList.remove('hidden');
            load();
        }).catch(() => toastErr('Verwijderen mislukt.'))
        .finally(() => { deleteConfirmBtn.disabled = false; });
    });

    backBtn.addEventListener('click', () => {
        threadWrap.classList.add('hidden');
        threadWrap.classList.remove('flex');
        chatPane.classList.add('hidden');
        chatPane.classList.remove('flex');
        listPane.classList.remove('hidden');
        listPane.classList.add('flex');
        const header = document.getElementById('chatInboxPageHeader');
        if (header) header.classList.remove('hidden');
    });

    searchEl.addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => { currentPage = 1; load(); }, 300); });
    statusEl.addEventListener('change', () => { currentPage = 1; load(); });
    perPageEl.addEventListener('change', () => { currentPage = 1; load(); });

    function syncLoop() {
        if (replyEl.value.trim() !== '') return;
        load(true);
        if (currentId) {
            fetch('/admin/chat/inbox/' + currentId, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(data => {
                    if (replyEl.value.trim() !== '') return;
                    threadEl.innerHTML = (data.messages || []).map(renderBubble).join('');
                    threadEl.scrollTop = threadEl.scrollHeight;
                }).catch(() => {});
        }
    }

    load();
    syncTimer = setInterval(syncLoop, 30000);

    try {
        const params = new URLSearchParams(window.location.search);
        const conv = parseInt(params.get('conversation') || '', 10);
        if (conv) openThread(conv);
    } catch (e) { /* noop */ }
})();

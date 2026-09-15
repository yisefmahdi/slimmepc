/* Slimme-PC Live Chat — welkom → start → AI/medewerker + offline + rating */
(function () {
    'use strict';

    var cfg = window.AiChat || {};
    var routes = cfg.routes || {};
    var authedUser = cfg.user || {};
    window.AiChat = cfg;
    window.AiChat.version = '2026-09-14g';

    var panel = document.getElementById('aiChatPanel');
    var openBtn = document.getElementById('openAiChat');
    if (!panel || !openBtn || !routes.status) return;

    var closeBtn = document.getElementById('closeAiChat');
    var messagesEl = document.getElementById('aiChatMessages');
    var gateEl = document.getElementById('aiChatGate');
    var gateError = null;
    var nameInput = null;
    var emailInput = null;
    var startBtn = null;
    var typingEl = document.getElementById('aiChatTyping');
    var actionsEl = document.getElementById('aiChatActions');
    var handoverBtn = document.getElementById('aiChatHandoverBtn');
    var endBtn = document.getElementById('aiChatEndBtn');
    var form = document.getElementById('aiChatForm');
    var input = document.getElementById('aiChatInput');
    var photoInput = document.getElementById('aiChatPhoto');
    var attachBtn = document.getElementById('aiChatAttachBtn');
    var photoPreview = document.getElementById('aiChatPhotoPreview');
    var photoImg = document.getElementById('aiChatPhotoImg');
    var photoName = document.getElementById('aiChatPhotoName');
    var photoRemove = document.getElementById('aiChatPhotoRemove');
    var offlineEl = document.getElementById('aiChatOffline');
    var offlineReason = document.getElementById('aiChatOfflineReason');
    var offName = document.getElementById('aiChatOffName');
    var offEmail = document.getElementById('aiChatOffEmail');
    var offMsg = document.getElementById('aiChatOffMsg');
    var offSend = document.getElementById('aiChatOffSend');
    var offError = document.getElementById('aiChatOffError');
    var ratingEl = document.getElementById('aiChatRating');
    var starsEl = document.getElementById('aiChatStars');
    var rateComment = document.getElementById('aiChatRateComment');
    var rateSend = document.getElementById('aiChatRateSend');
    var rateSkip = document.getElementById('aiChatRateSkip');
    var historyBtn = document.getElementById('aiChatHistoryBtn');
    var historyEl = document.getElementById('aiChatHistory');
    var historyList = document.getElementById('aiChatHistoryList');
    var historyClose = document.getElementById('aiChatHistoryClose');
    var presenceText = document.getElementById('aiChatPresenceText');

    var TOKEN_KEY = 'slimmepc-chat-token';
    var token = null;
    try { token = localStorage.getItem(TOKEN_KEY); } catch (e) { token = null; }
    var state = 'gate'; // gate | chat | offline | rating
    var renderedIds = {};
    var pollTimer = null;
    var statusCache = null;
    var statusCacheAt = 0;
    var selectedStars = 0;
    var stagedPhotoUrl = null;
    var sending = false;

    if (authedUser.name) {
        if (offName) offName.value = authedUser.name;
        if (offEmail) offEmail.value = authedUser.email || '';
        if (historyBtn) { historyBtn.classList.remove('hidden'); historyBtn.classList.add('flex'); }
    }

    /**
     * Bouwt de gate (naam + e-mail) opnieuw op als hij weg is
     * (bv. na rating of na een gesloten gesprek) en bindt de startknop.
     */
    function ensureGate() {
        gateEl = document.getElementById('aiChatGate');
        if (!gateEl || !gateEl.isConnected) {
            var tmp = document.createElement('div');
            tmp.innerHTML = '<div id="aiChatGate" class="rounded-2xl bg-white p-3.5 shadow-sm ring-1 ring-slate-200/70">'
                + '<p class="mb-2.5 text-xs font-bold text-slate-600">Vul je gegevens in om een nieuw gesprek te starten:</p>'
                + '<div class="space-y-2">'
                + '<input type="text" id="aiChatName" maxlength="255" placeholder="Je naam *" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">'
                + '<input type="email" id="aiChatEmail" maxlength="255" placeholder="Je e-mailadres *" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">'
                + '</div>'
                + '<button type="button" id="aiChatStartBtn" class="bg-brand-gradient-br mt-2.5 flex h-10 w-full items-center justify-center rounded-xl text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5">Start chatten</button>'
                + '<p id="aiChatGateError" class="hidden mt-2 text-xs font-semibold text-red-600"></p>'
                + '</div>';
            gateEl = tmp.firstChild;
            messagesEl.appendChild(gateEl);
        }
        gateError = document.getElementById('aiChatGateError');
        nameInput = document.getElementById('aiChatName');
        emailInput = document.getElementById('aiChatEmail');
        startBtn = document.getElementById('aiChatStartBtn');
        if (authedUser.name) {
            if (nameInput && !nameInput.value) nameInput.value = authedUser.name;
            if (emailInput && !emailInput.value) emailInput.value = authedUser.email || '';
        }
        if (startBtn) startBtn.onclick = doStart;
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }
    function refreshIcons() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            try { window.lucide.createIcons(); } catch (e) { /* noop */ }
        }
        if (typeof window.__lucideRefresh === 'function') {
            try { window.__lucideRefresh(); } catch (e) { /* noop */ }
        }
    }

    /* ================= Geluid (Web Audio, geen bestanden) ================= */
    var SOUND_KEY = 'slimmepc-chat-sound';
    var soundOn = true;
    try { soundOn = localStorage.getItem(SOUND_KEY) !== '0'; } catch (e) { soundOn = true; }
    var audioCtx = null;
    var entryPlayed = false;

    function ensureAudio() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
            return audioCtx && audioCtx.state === 'running' ? audioCtx : null;
        } catch (e) { return null; }
    }
    function tone(ctx, freq, startAt, dur, vol, type) {
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = type || 'sine';
        osc.frequency.setValueAtTime(freq, startAt);
        gain.gain.setValueAtTime(0.0001, startAt);
        gain.gain.exponentialRampToValueAtTime(vol, startAt + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.0001, startAt + dur);
        osc.start(startAt);
        osc.stop(startAt + dur + 0.02);
    }
    function playSound(kind) {
        if (!soundOn) return;
        var ctx = ensureAudio();
        if (!ctx) return;
        var t = ctx.currentTime;
        try {
            if (kind === 'open') {
                tone(ctx, 520, t, 0.12, 0.10);
                tone(ctx, 780, t + 0.09, 0.16, 0.10);
            } else if (kind === 'send') {
                tone(ctx, 880, t, 0.08, 0.07, 'triangle');
            } else if (kind === 'receive') {
                tone(ctx, 660, t, 0.13, 0.11);
                tone(ctx, 880, t + 0.11, 0.18, 0.11);
            } else if (kind === 'entry') {
                tone(ctx, 587, t, 0.14, 0.07);
                tone(ctx, 880, t + 0.12, 0.20, 0.07);
            }
        } catch (e) { /* stil */ }
    }
    function paintSound() {
        var on = document.getElementById('aiChatSoundOn');
        var off = document.getElementById('aiChatSoundOff');
        if (on) on.classList.toggle('hidden', !soundOn);
        if (off) off.classList.toggle('hidden', soundOn);
    }
    // Entry-chime: direct proberen, bij autoplay-blokkade na eerste interactie.
    function entryChime() {
        if (entryPlayed) return;
        entryPlayed = true;
        playSound('entry');
        // Valt de eerste poging weg (autoplay), dan alsnog bij eerste interactie.
        if (audioCtx && audioCtx.state !== 'running') {
            var once = function () {
                entryPlayed = false;
                playSound('entry');
                entryPlayed = true;
                document.removeEventListener('pointerdown', once);
                document.removeEventListener('keydown', once);
            };
            document.addEventListener('pointerdown', once);
            document.addEventListener('keydown', once);
        }
    }
    setTimeout(entryChime, 1200);
    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    /* ============ Foto lightbox (popup binnen het paneel) ============ */
    var lightbox = document.getElementById('aiChatLightbox');
    var lightboxImg = document.getElementById('aiChatLightboxImg');
    var lightboxClose = document.getElementById('aiChatLightboxClose');
    function isLightboxOpen() {
        return !!(lightbox && !lightbox.classList.contains('hidden'));
    }
    function openLightbox(url) {
        if (!lightbox || !lightboxImg || !url) return;
        lightboxImg.src = url;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        lightbox.setAttribute('aria-hidden', 'false');
        refreshIcons();
    }
    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        lightbox.setAttribute('aria-hidden', 'true');
        if (lightboxImg) lightboxImg.src = '';
    }
    if (lightboxClose) lightboxClose.addEventListener('click', function (e) {
        e.stopPropagation();
        closeLightbox();
    });
    if (lightbox) lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    messagesEl.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a[data-photo]') : null;
        if (!a) return;
        e.preventDefault();
        e.stopPropagation();
        openLightbox(a.getAttribute('href'));
    });

    /* ============ Knop-loading (spinner, design-systeem) ============ */
    var SPINNER_SVG = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
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
            refreshIcons();
        }
    }

    function linkify(text) {        return esc(text).replace(/https?:\/\/[^\s<]+/g, function (url) {
            var raw = url.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#039;/g, "'");
            raw = raw.replace(/[.,;:!?]+$/, '');
            var href = esc(raw);
            var short = raw.replace(/^https?:\/\//, '');
            if (short.length > 42) short = short.slice(0, 42) + '…';
            return '<a href="' + href + '" target="_blank" rel="noopener" title="' + href + '" class="ai-link font-bold text-blue-600 underline hover:text-blue-800">' + esc(short) + '</a>';
        });
    }
    function scrollBottom() {
        if (messagesEl) messagesEl.scrollTop = messagesEl.scrollHeight;
    }
    function saveToken(t) {
        token = t;
        try {
            if (t) localStorage.setItem(TOKEN_KEY, t);
            else localStorage.removeItem(TOKEN_KEY);
        } catch (e) { /* noop */ }
    }
    function showTyping(show) {
        if (!typingEl) return;
        typingEl.classList.toggle('hidden', !show);
        typingEl.classList.toggle('flex', !!show);
        if (show) scrollBottom();
    }

    function assistantBubble(html) {
        var row = document.createElement('div');
        row.className = 'flex justify-start';
        row.innerHTML = '<div dir="auto" class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">' + html + '</div>';
        messagesEl.appendChild(row);
        scrollBottom();
    }
    function userBubble(text) {
        var row = document.createElement('div');
        row.className = 'flex justify-end';
        row.innerHTML = '<div dir="auto" class="bg-brand-gradient-br max-w-[85%] rounded-2xl rounded-tr-md px-3.5 py-2.5 text-[13px] leading-relaxed text-white shadow-sm">' + esc(text) + '</div>';
        messagesEl.appendChild(row);
        scrollBottom();
    }
    function customerBlock(body, photoUrl) {
        // Skip optimistic duplicate of latest customer message.
        if (body && messagesEl.children.length > 0) {
            var lastBubble = messagesEl.children[messagesEl.children.length - 1];
            if (lastBubble && lastBubble.querySelector && lastBubble.querySelector('.bg-brand-gradient-br')) {
                var txtNode = lastBubble.querySelector('.bg-brand-gradient-br');
                if (txtNode && txtNode.textContent === body) return;
            }
        }
        var wrap = document.createElement('div');
        wrap.className = 'flex justify-end';
        var col = '<div class="flex max-w-[85%] flex-col items-end gap-1.5">';
        if (body) col += '<div dir="auto" class="rounded-2xl rounded-tr-md bg-brand-gradient-br px-3.5 py-2.5 text-[13px] leading-relaxed text-white shadow-sm">' + esc(body) + '</div>';
        if (photoUrl) col += '<a data-photo href="' + esc(photoUrl) + '" class="block max-w-[70%] cursor-zoom-in overflow-hidden rounded-2xl shadow-sm ring-1 ring-slate-200/70"><img src="' + esc(photoUrl) + '" alt="Foto" loading="lazy" class="max-h-48 w-auto object-cover"></a>';
        col += '</div>';
        wrap.innerHTML = col;
        messagesEl.appendChild(wrap);
        scrollBottom();
    }
    function renderMessage(m) {
        if (!m || renderedIds[m.id]) return false;
        renderedIds[m.id] = true;
        if (m.sender === 'customer') {
            if (!m.body && !m.photo_url) return true;
            customerBlock(m.body || '', m.photo_url);
            return true;
        }
        if (m.sender === 'admin') {
            var rowA = document.createElement('div');
            rowA.className = 'flex justify-start';
            var inner = '<div dir="auto" class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-2 ring-blue-300"><p class="mb-1 text-[10px] font-extrabold uppercase tracking-wide text-blue-600">Medewerker</p>';
            if (m.body) inner += esc(m.body);
            if (m.photo_url) {
                inner += '<a data-photo href="' + esc(m.photo_url) + '" class="mt-2 block cursor-zoom-in overflow-hidden rounded-xl ring-1 ring-slate-200/70"><img src="' + esc(m.photo_url) + '" alt="Foto van medewerker" loading="lazy" class="max-h-48 w-auto object-cover"></a>';
            }
            inner += '</div>';
            rowA.innerHTML = inner;
            messagesEl.appendChild(rowA);
            scrollBottom();
            return true;
        }
        var html = linkify(m.body || '');
        assistantBubble(html);
        return true;
    }

    /**
     * Herbouwt de thread vanaf de server (geen duplicaten):
     * wist statische welkom/gate, registreert alle ids, toont suggesties
     * alleen als de klant nog niets heeft gestuurd.
     */
    function renderThread(list) {
        list = Array.isArray(list) ? list : [];
        messagesEl.innerHTML = '';
        renderedIds = {};
        // Behoud de statische welkomst-div alleen bij een verse gate.
        if (view === 'gate') {
            var welcome = document.createElement('div');
            welcome.className = 'flex justify-start';
            welcome.innerHTML = '<div dir="auto" class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">Hoi! Ik ben de <strong>Slimme-PC assistent</strong>. Waar kan ik je mee helpen?</div>';
            messagesEl.appendChild(welcome);
        }
        list.forEach(function (m) {
            renderMessage(m);
        });
    }

    function renderProductCards(products) {
        if (!products || !products.length) return;
        products.forEach(function (p) {
            var row = document.createElement('div');
            row.className = 'flex justify-start';
            var stock = p.in_stock
                ? '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-green-600"><span class="inline-block h-1.5 w-1.5 rounded-full bg-green-500"></span>Op voorraad</span>'
                : '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-500"><span class="inline-block h-1.5 w-1.5 rounded-full bg-red-500"></span>Niet op voorraad</span>';
            var price = '€ ' + Number(p.price).toFixed(2).replace('.', ',');
            var card = '<a href="' + esc(p.url) + '" target="_blank" rel="noopener" class="flex max-w-[85%] items-center gap-3 rounded-2xl rounded-tl-md bg-white p-2.5 pr-3 text-left shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md hover:ring-blue-300">'
                + (p.image ? '<img src="' + esc(p.image) + '" alt="' + esc(p.title) + '" loading="lazy" class="h-14 w-14 shrink-0 rounded-xl border border-slate-100 object-cover">' : '')
                + '<span class="min-w-0 flex-1">'
                + '<span class="block truncate text-[13px] font-bold text-slate-800">' + esc((p.brand ? p.brand + ' ' : '') + p.title) + '</span>'
                + '<span class="mt-0.5 flex items-center gap-2 text-xs"><span class="font-extrabold text-blue-700">' + esc(price) + '</span>' + stock + '</span>'
                + (p.delivery ? '<span class="mt-0.5 block truncate text-[11px] text-slate-500">' + esc(p.delivery) + '</span>' : '')
                + '</span></a>';
            row.innerHTML = card;
            messagesEl.appendChild(row);
        });
        scrollBottom();
        refreshIcons();
    }

    function showHandoverBtn(show) {
        if (!handoverBtn) return;
        handoverBtn.classList.toggle('hidden', !show);
        handoverBtn.classList.toggle('flex', !!show);
        if (show) {
            actionsEl.classList.remove('hidden');
            actionsEl.classList.add('flex');
        } else if (actionsEl) {
            actionsEl.classList.add('hidden');
            actionsEl.classList.remove('flex');
        }
        refreshIcons();
    }

    var handoverOffered = false;

    function setView(view) {
        state = view;
        var inChat = view === 'chat';
        messagesEl.classList.toggle('hidden', view !== 'gate' && view !== 'chat');
        messagesEl.classList.toggle('flex', view === 'gate' || view === 'chat');
        offlineEl.classList.toggle('hidden', view !== 'offline');
        offlineEl.classList.toggle('flex', view === 'offline');
        ratingEl.classList.toggle('hidden', view !== 'rating');
        ratingEl.classList.toggle('flex', view === 'rating');
        form.classList.toggle('hidden', !inChat);
        form.classList.toggle('flex', inChat);
        // Actiebar alleen tonen als de AI een medewerker aanbiedt.
        var showBar = inChat && handoverOffered;
        actionsEl.classList.toggle('hidden', !showBar);
        actionsEl.classList.toggle('flex', showBar);
        refreshIcons();
    }

    function isOpen() {
        return !panel.classList.contains('hidden');
    }
    function setFab(open) {
        var bars = document.getElementById('aiChatFabBars');
        var cross = document.getElementById('aiChatFabClose');
        if (bars) bars.classList.toggle('hidden', !!open);
        if (cross) cross.classList.toggle('hidden', !open);
        try { if (bars) bars.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><rect x="4" y="6" width="16" height="2.5" rx="1.25" fill="currentColor" stroke="none"/><rect x="7.5" y="10.5" width="9" height="2.5" rx="1.25" fill="currentColor" stroke="none"/><rect x="7.5" y="15" width="9" height="2.5" rx="1.25" fill="currentColor" stroke="none"/></svg>'; } catch (e) { /* noop */ }
        try { if (cross) cross.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M6 18 18 6M6 6l12 12"/></svg>'; } catch (e) { /* noop */ }
    }
    function openPanel() {
        panel.classList.remove('hidden');
        panel.classList.add('flex');
        panel.setAttribute('aria-hidden', 'false');
        openBtn.setAttribute('aria-expanded', 'true');
        setFab(true);
        refreshIcons();
        playSound('open');
        boot();
    }
    function closePanel(reason) {
        try {
            window.AiChat.lastCloseReason = (reason || 'unknown') + ' @ ' + new Date().toISOString();
            if (window.console && window.console.debug) window.console.debug('[ai-chat] panel closed:', window.AiChat.lastCloseReason);
        } catch (e) { /* noop */ }
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        panel.setAttribute('aria-hidden', 'true');
        openBtn.setAttribute('aria-expanded', 'false');
        setFab(false);
    }

    function api(path, options) {
        options = options || {};
        options.credentials = 'same-origin';
        options.headers = Object.assign({
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest'
        }, options.headers || {});
        return fetch(path, options).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
                return { ok: res.ok, status: res.status, data: data };
            });
        });
    }

    function getStatus() {
        var now = Date.now();
        if (statusCache && now - statusCacheAt < 15000) return Promise.resolve(statusCache);
        return api(routes.status).then(function (r) {
            statusCache = r.data || { open: true };
            statusCacheAt = now;
            return statusCache;
        });
    }

    var booted = false;
    var lastKnownOpen = null;
    function boot() {
        if (booted) {
            // Bij elk openen opnieuw evalueren (uren kunnen intussen zijn gewijzigd).
            refreshOnOpen();
            return;
        }
        booted = true;
        getStatus().then(function (st) {
            lastKnownOpen = st.open !== false;
            applyStatus(st);
        }).catch(function () {
            ensureGate();
            setView('gate');
        });
    }

    function applyStatus(st) {
        if (st.open === false) {
            if (offlineReason && st.reason) offlineReason.textContent = st.reason + (st.opens_at ? ' We zijn weer open vanaf ' + formatOpensAt(st.opens_at) + '.' : ' Laat je bericht achter — we reageren per e-mail.');
            if (presenceText) presenceText.textContent = 'Gesloten — laat een bericht achter';
            setView('offline');
            return;
        }
        if (presenceText) presenceText.textContent = 'Online — reageert direct';
        if (token) {
            resume();
        } else {
            ensureGate();
            setView('gate');
            setTimeout(function () { if (nameInput && !authedUser.name) nameInput.focus(); }, 150);
        }
    }

    /**
     * Bij elk openen: alleen UPGRADEN offline → open (nooit een actief
     * gesprek downgraden; een gesloten thread regelt zichzelf via 422-reset).
     */
    function refreshOnOpen() {
        getStatus().then(function (st) {
            var isOpen = st.open !== false;
            if (isOpen === lastKnownOpen) return;
            lastKnownOpen = isOpen;
            if (!isOpen) {
                if (!token && state !== 'chat' && state !== 'rating') applyStatus(st);
                return;
            }
            if (state === 'offline' || (!token && state !== 'chat' && state !== 'rating')) {
                applyStatus(st);
            }
        }).catch(function () { /* stil: behoud huidige view */ });
    }

    function formatOpensAt(iso) {
        try {
            var d = new Date(iso);
            var days = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
            return days[d.getDay()] + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        } catch (e) { return ''; }
    }

    function resume() {
        api(routes.messages + '?token=' + encodeURIComponent(token)).then(function (r) {
            if (!r.ok) {
                resetToFreshGate('Sessie verlopen. Start hieronder een nieuw gesprek.');
                return;
            }
            if (r.data.status === 'closed') {
                // Gesloten gesprek: toon alleen als archief (lezen, geen schrijven, geen rating meer).
                showRateForm();
                rateSend.classList.add('hidden');
                rateSend.classList.remove('flex');
                rateSkip.classList.add('hidden');
                rateSkip.classList.remove('flex');
                if (rateComment) rateComment.disabled = true;
                var ratingDone = document.getElementById('aiChatRatedDone');
                if (ratingDone) {
                    ratingDone.classList.remove('hidden');
                    ratingDone.classList.add('flex');
                    document.getElementById('aiChatRateForm').classList.add('hidden');
                }
                setView('rating');
                selectedStars = 0;
                paintStars();
                return;
            }
            if (r.data.status === 'offline') {
                // Offline-threads lopen via e-mail; in de widget kan er niet
                // in worden geschreven. Alleen tonen als de shop nog dicht is.
                getStatus().then(function (st) {
                    if (st.open === false) {
                        if (presenceText) presenceText.textContent = 'Gesloten — laat een bericht achter';
                        setView('offline');
                    } else {
                        resetToFreshGate('We zijn weer open! Start hieronder een nieuw gesprek.');
                    }
                }).catch(function () {
                    resetToFreshGate(null);
                });
                return;
            }
            if (gateEl) gateEl.remove();
            handoverOffered = false;
            showHandoverBtn(false);
            setView('chat');
            threadStatus = r.data.status || null;
            startPoll();
            renderThread(r.data.messages || []);
            if (r.data.status === 'handed_over' && presenceText) presenceText.textContent = 'Medewerker is erbij';
        }).catch(function () {
            resetToFreshGate(null);
        });
    }

    /**
     * Harde reset naar een verse gate (bv. na gesloten gesprek):
     * token weg, polling stop, thread leeg, nieuwe gate met melding.
     */
    function resetToFreshGate(notice) {
        saveToken(null);
        stopPoll();
        threadStatus = null;
        renderedIds = {};
        messagesEl.innerHTML = '';
        var welcome = document.createElement('div');
        welcome.className = 'flex justify-start';
        welcome.innerHTML = '<div dir="auto" class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">Hoi! Ik ben de <strong>Slimme-PC assistent</strong>. Waar kan ik je mee helpen?</div>';
        messagesEl.appendChild(welcome);
        ensureGate();
        if (notice && gateError) {
            gateError.textContent = notice;
            gateError.classList.remove('hidden');
        }
        if (presenceText) presenceText.textContent = 'Online — reageert direct';
        setView('gate');
    }

    function doStart() {
        var name = nameInput ? nameInput.value.trim().slice(0, 255) : '';
        var email = emailInput ? emailInput.value.trim().slice(0, 255) : '';
        gateError.classList.add('hidden');
        // Gate-velden zijn leidend (ook ingelogd) — daarheen gaan alle mails.
        if (!name) { gateError.textContent = 'Vul je naam in.'; gateError.classList.remove('hidden'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { gateError.textContent = 'Vul een geldig e-mailadres in.'; gateError.classList.remove('hidden'); return; }
        btnLoading(startBtn, true);
        var fd = new FormData();
        fd.append('name', name);
        fd.append('email', email);
        api(routes.start, { method: 'POST', body: fd }).then(function (r) {
            if (!r.ok) {
                var msg = (r.data.errors && Object.values(r.data.errors)[0][0]) || r.data.message || 'Starten mislukt.';
                gateError.textContent = msg;
                gateError.classList.remove('hidden');
                return;
            }
            saveToken(r.data.token);
            if (gateEl) gateEl.remove();
            setView('chat');
            threadStatus = 'ai';
            startPoll();
            renderThread(r.data.messages || []);
        }).catch(function () {
            gateError.textContent = 'Starten mislukt. Probeer het opnieuw.';
            gateError.classList.remove('hidden');
        }).finally(function () { btnLoading(startBtn, false); });
    }

    function clearPhoto(keepUrl) {
        if (photoInput) photoInput.value = '';
        if (stagedPhotoUrl && !keepUrl) { URL.revokeObjectURL(stagedPhotoUrl); stagedPhotoUrl = null; }
        photoPreview.classList.add('hidden');
        photoPreview.classList.remove('flex');
    }

    if (attachBtn) attachBtn.addEventListener('click', function () { photoInput.click(); });
    if (photoInput) photoInput.addEventListener('change', function () {
        var f = photoInput.files && photoInput.files[0];
        if (!f) { if (photoInput) photoInput.value = ''; return; }
        if (!f.type.startsWith('image/') || f.size > 10 * 1024 * 1024) {
            assistantBubble('Deze foto kan niet worden toegevoegd (alleen afbeeldingen tot 10MB).');
            clearPhoto();
            return;
        }
        stagedPhotoUrl = URL.createObjectURL(f);
        photoImg.src = stagedPhotoUrl;
        photoName.textContent = f.name;
        photoPreview.classList.remove('hidden');
        photoPreview.classList.add('flex');
        refreshIcons();
    });
    if (photoRemove) photoRemove.addEventListener('click', clearPhoto);

    function autogrowInput() {
        if (!input) return;
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }
    if (input) {
        input.addEventListener('input', autogrowInput);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (form) form.requestSubmit();
            }
        });
    }

    if (form) form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!token || sending) return;
        var text = input ? input.value.trim().slice(0, 2000) : '';
        var hasPhoto = photoInput && photoInput.files && photoInput.files[0];
        if (!text && !hasPhoto) return;
        sending = true;
        var fd = new FormData();
        fd.append('token', token);
        if (text) fd.append('body', text);
        if (hasPhoto) fd.append('photo', photoInput.files[0]);
        if (text || hasPhoto) customerBlock(text, hasPhoto ? stagedPhotoUrl : null);
        if (input) { input.value = ''; autogrowInput(); }
        (function (url) {
            clearPhoto(true);
            if (url) setTimeout(function () { try { URL.revokeObjectURL(url); } catch (e) {} }, 120000);
        })(hasPhoto ? stagedPhotoUrl : null);
        showTyping(true);
        playSound('send');
        var sendBtn = document.getElementById('aiChatSend');
        btnLoading(sendBtn, true);
        api(routes.send, { method: 'POST', body: fd }).then(function (r) {
            showTyping(false);
            sending = false;
            btnLoading(sendBtn, false);
            if (!r.ok) {
                if (r.data && r.data.code === 'conversation_closed') {
                    resetToFreshGate('Dit gesprek is gesloten. Start hieronder een nieuw gesprek.');
                    return;
                }
                assistantBubble(esc((r.data && r.data.message) || 'Versturen mislukt. Probeer het opnieuw.'));
                return;
            }
            // Eigen bericht staat er al (optimistisch) — registreer id tegen poll-duplicaten.
            if (r.data.message && r.data.message.id) renderedIds[r.data.message.id] = true;
            if (r.data.reply && renderMessage(r.data.reply)) playSound('receive');
            if (r.data.status) threadStatus = r.data.status;
            if (r.data.products && r.data.products.length) renderProductCards(r.data.products);
            handoverOffered = !!r.data.handoff_offer;
            showHandoverBtn(handoverOffered);
            poll(true);
        }).catch(function () {
            showTyping(false);
            sending = false;
            btnLoading(sendBtn, false);
            assistantBubble('Versturen mislukt. Controleer je verbinding.');
        });
    });

    if (handoverBtn) handoverBtn.addEventListener('click', function () {
        if (!token) return;
        askConfirm('Wil je door een medewerker geholpen worden? Er wordt een ticket voor je aangemaakt.', function () {
            var fd = new FormData();
            fd.append('token', token);
            var startedAt = Date.now();
            showTyping(true);
            btnLoading(handoverBtn, true);
            // Minimaal 900ms typing-Dots, ook als de server sneller is.
            var finish = function (fn) {
                var wait = Math.max(0, 900 - (Date.now() - startedAt));
                setTimeout(function () {
                    showTyping(false);
                    btnLoading(handoverBtn, false);
                    if (fn) fn();
                }, wait);
            };
            api(routes.handover, { method: 'POST', body: fd }).then(function (r) {
                finish(function () {
                    if (!r.ok) return;
                    var ding = false;
                    (r.data.messages || []).forEach(function (m) { if (renderMessage(m)) ding = true; });
                    if (ding) playSound('receive');
                    if (presenceText) presenceText.textContent = 'Medewerker is erbij';
                    handoverOffered = false;
                    showHandoverBtn(false);
                    threadStatus = 'handed_over';
                    restartPoll();
                });
            }).catch(function () {
                finish(null);
            });
        });
    });

    function endChat() {
        if (!token) return;
        askConfirm('Wil je dit gesprek beëindigen?', function () {
            var fd = new FormData();
            fd.append('token', token);
            api(routes.close, { method: 'POST', body: fd }).then(function () {
                stopPoll();
                selectedStars = 0;
                paintStars();
                if (rateComment) rateComment.value = '';
                showRateForm();
                setView('rating');
            });
        });
    }

    if (endBtn) endBtn.addEventListener('click', endChat);
    var headerEndBtn = document.getElementById('aiChatHeaderEndBtn');
    if (headerEndBtn) headerEndBtn.addEventListener('click', endChat);
    var soundBtn = document.getElementById('aiChatSoundBtn');
    paintSound();
    if (soundBtn) soundBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        soundOn = !soundOn;
        try { localStorage.setItem(SOUND_KEY, soundOn ? '1' : '0'); } catch (err) { /* noop */ }
        paintSound();
        if (soundOn) playSound('open');
    });

    function paintStars() {
        starsEl.querySelectorAll('.ai-star').forEach(function (btn) {
            var n = parseInt(btn.dataset.stars, 10);
            btn.classList.toggle('text-amber-400', n <= selectedStars);
            btn.classList.toggle('text-slate-300', n > selectedStars);
            var svg = btn.querySelector('svg');
            if (svg) svg.setAttribute('fill', n <= selectedStars ? 'currentColor' : 'none');
        });
        refreshIcons();
    }
    starsEl.addEventListener('click', function (e) {
        e.stopPropagation();
        var btn = e.target && e.target.closest ? e.target.closest('[data-stars]') : null;
        if (!btn) return;
        selectedStars = parseInt(btn.dataset.stars, 10);
        paintStars();
    });
    function showRateForm() {
        var formEl = document.getElementById('aiChatRateForm');
        var doneEl = document.getElementById('aiChatRatedDone');
        if (formEl) formEl.classList.remove('hidden');
        if (doneEl) { doneEl.classList.add('hidden'); doneEl.classList.remove('flex'); }
    }
    function showRatedDone(stars) {
        var formEl = document.getElementById('aiChatRateForm');
        var doneEl = document.getElementById('aiChatRatedDone');
        var starsEl2 = document.getElementById('aiChatRatedStars');
        if (formEl) formEl.classList.add('hidden');
        if (starsEl2) starsEl2.textContent = stars > 0 ? '★★★★★'.slice(0, stars) + '☆☆☆☆☆'.slice(0, 5 - stars) : '';
        if (doneEl) { doneEl.classList.remove('hidden'); doneEl.classList.add('flex'); }
        refreshIcons();
    }
    function finishRating(skipped) {
        var fd = new FormData();
        fd.append('token', token || '');
        if (!skipped) {
            if (!selectedStars) return;
            fd.append('rating', selectedStars);
            if (rateComment && rateComment.value.trim()) fd.append('comment', rateComment.value.trim().slice(0, 1000));
        }
        var done = function () {
            // Paneel blijft open: bedankje + knop voor nieuw gesprek (geen abrupte reset).
            showRatedDone(skipped ? 0 : selectedStars);
        };
        if (skipped || !token) { done(); return; }
        btnLoading(rateSend, !skipped);
        api(routes.rate, { method: 'POST', body: fd }).then(done).catch(done).finally(function () { btnLoading(rateSend, false); });
    }
    if (rateSend) rateSend.addEventListener('click', function (e) { e.stopPropagation(); finishRating(false); });
    if (rateSkip) rateSkip.addEventListener('click', function (e) { e.stopPropagation(); finishRating(true); });
    var newChatBtn = document.getElementById('aiChatNewChatBtn');
    if (newChatBtn) newChatBtn.addEventListener('click', function () {
        resetToFreshGate(null);
    });

    if (offSend) offSend.addEventListener('click', function () {
        var offSpinner = document.getElementById('aiChatOffSpinner');
        var offLabel = document.getElementById('aiChatOffSendLabel');
        var offForm = document.getElementById('aiChatOffForm');
        var offDone = document.getElementById('aiChatOffDone');
        var name = offName.value.trim().slice(0, 255);
        var email = offEmail.value.trim().slice(0, 255);
        var msg = offMsg.value.trim().slice(0, 2000);
        offError.classList.add('hidden');
        if (!name) { offError.textContent = 'Vul je naam in.'; offError.classList.remove('hidden'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { offError.textContent = 'Vul een geldig e-mailadres in.'; offError.classList.remove('hidden'); return; }
        if (!msg) { offError.textContent = 'Typ je bericht.'; offError.classList.remove('hidden'); return; }
        offSend.disabled = true;
        if (offSpinner) offSpinner.classList.remove('hidden');
        if (offLabel) offLabel.textContent = 'Bezig met verzenden...';
        var fd = new FormData();
        fd.append('name', name);
        fd.append('email', email);
        fd.append('message', msg);
        api(routes.offline, { method: 'POST', body: fd }).then(function (r) {
            if (!r.ok) {
                var m = (r.data.errors && Object.values(r.data.errors)[0][0]) || r.data.message || 'Versturen mislukt.';
                offError.textContent = m;
                offError.classList.remove('hidden');
                return;
            }
            // Ticket aangemaakt: géén thread openen (e-mail-only),
            // alleen het bedankje van de server tonen (in klanttaal).
            var thanksEl = document.getElementById('aiChatOffThanks');
            if (thanksEl && r.data.thanks) thanksEl.textContent = r.data.thanks;
            if (offForm) { offForm.classList.add('hidden'); }
            if (offDone) { offDone.classList.remove('hidden'); offDone.classList.add('flex'); }
            playSound('receive');
            refreshIcons();
        }).catch(function () {
            offError.textContent = 'Versturen mislukt. Probeer het opnieuw.';
            offError.classList.remove('hidden');
        }).finally(function () {
            offSend.disabled = false;
            if (offSpinner) offSpinner.classList.add('hidden');
            if (offLabel) offLabel.textContent = 'Verstuur bericht';
        });
    });

    var offAgain = document.getElementById('aiChatOffAgain');
    if (offAgain) offAgain.addEventListener('click', function () {
        var offForm = document.getElementById('aiChatOffForm');
        var offDone = document.getElementById('aiChatOffDone');
        if (offMsg) offMsg.value = '';
        if (offDone) { offDone.classList.add('hidden'); offDone.classList.remove('flex'); }
        if (offForm) offForm.classList.remove('hidden');
    });

    if (historyBtn) historyBtn.addEventListener('click', function () {
        historyEl.classList.remove('hidden');
        historyEl.classList.add('flex');
        historyList.innerHTML = '<p class="text-xs text-slate-500">Laden...</p>';
        api(routes.history).then(function (r) {
            var items = (r.data && r.data.conversations) || [];
            if (!items.length) {
                historyList.innerHTML = '<p class="text-xs text-slate-500">Nog geen eerdere chats.</p>';
                return;
            }
            historyList.innerHTML = '';
            items.forEach(function (c) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'w-full rounded-xl border border-slate-200 p-3 text-left transition hover:border-blue-300 hover:bg-blue-50/50';
                b.innerHTML = '<p class="flex items-center justify-between text-xs font-bold text-slate-700"><span>Gesprek #' + esc(c.id) + ' · ' + esc(c.status) + '</span>' + (c.rating ? '<span class="text-amber-500">★ ' + esc(c.rating) + '</span>' : '') + '</p>'
                    + '<p class="mt-1 truncate text-xs text-slate-500">' + esc(c.last_message || '—') + '</p>';
                b.addEventListener('click', function () {
                    currentId = c.id;
                    saveToken(c.token);
                    historyEl.classList.add('hidden');
                    historyEl.classList.remove('flex');
                    openThread(c.id);
                });
                historyList.appendChild(b);
            });
            refreshIcons();
        });
    });
    if (historyClose) historyClose.addEventListener('click', function () {
        historyEl.classList.add('hidden');
        historyEl.classList.remove('flex');
    });

    /**
     * Styled confirm in site-design (geen native dialog).
     * De generieke modal (z-60) zou achter het chatpaneel (z-1000) vallen —
     * daarom tijdelijk verhogen.
     */
    function askConfirm(message, onConfirm) {
        if (window.SlimmePC && typeof window.SlimmePC.confirm === 'function') {
            window.SlimmePC.confirm(message, onConfirm);
            var gm = document.getElementById('modal-generic-confirm');
            if (gm) gm.style.zIndex = '1100';
        } else if (confirm(message) && onConfirm) {
            onConfirm();
        }
    }

    var pollMs = 15000;
    var threadStatus = null;
    function pollInterval() { return threadStatus === 'handed_over' ? 5000 : 15000; }
    function poll(once) {
        if (!token || state !== 'chat' || document.hidden) return;
        api(routes.messages + '?token=' + encodeURIComponent(token)).then(function (r) {
            if (!r.ok) return;
            if (r.data.status && r.data.status !== threadStatus) {
                threadStatus = r.data.status;
                restartPoll();
            }
            if (r.data.status === 'closed') {
                stopPoll();
                selectedStars = 0;
                paintStars();
                showRateForm();
                setView('rating');
                return;
            }
            var ding = false;
            (r.data.messages || []).forEach(function (m) {
                if (renderMessage(m) && (m.sender === 'ai' || m.sender === 'admin')) ding = true;
            });
            if (ding) playSound('receive');
        }).catch(function () { /* stil */ });
        if (!once && !pollTimer) {
            pollMs = pollInterval();
            pollTimer = setInterval(function () { poll(true); }, pollMs);
        }
    }
    function startPoll() {
        stopPoll();
        pollMs = pollInterval();
        pollTimer = setInterval(function () { poll(true); }, pollMs);
    }
    function restartPoll() {
        if (!pollTimer) return;
        var ms = pollInterval();
        if (ms === pollMs) return;
        startPoll();
    }
    function stopPoll() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    openBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isOpen()) closePanel('fab-toggle');
        else openPanel();
    });
    if (closeBtn) closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closePanel('x-button');
    });
    document.addEventListener('click', function (e) {
        if (!isOpen()) return;
        // Tijdens beoordelen nooit automatisch sluiten.
        if (state === 'rating') return;
        // Kliks in SlimmePC-modals (confirm) mogen het paneel nooit sluiten.
        if (e.target && e.target.closest && e.target.closest('[id^="modal-"]')) return;
        if (panel.contains(e.target) || openBtn.contains(e.target)) return;
        if (historyEl && !historyEl.classList.contains('hidden')) return;
        var desc = 'outside-click';
        try {
            var t = e.target;
            desc += ':' + (t.tagName || '?') + '#' + (t.id || '-') + '.' + (typeof t.className === 'string' ? t.className.split(' ').slice(0, 3).join('.') : '-');
        } catch (err) { /* noop */ }
        closePanel(desc);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isLightboxOpen()) { closeLightbox(); return; }
        if (e.key === 'Escape' && isOpen()) closePanel('escape-key');
    });

    refreshIcons();
})();

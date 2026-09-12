/* Slimme-PC AI chat widget — DESIGN ONLY (static demo reply, no backend yet) */
(function () {
    'use strict';

    var panel = document.getElementById('aiChatPanel');
    var openBtn = document.getElementById('openAiChat');
    var closeBtn = document.getElementById('closeAiChat');
    var form = document.getElementById('aiChatForm');
    var input = document.getElementById('aiChatInput');
    var messages = document.getElementById('aiChatMessages');
    var typing = document.getElementById('aiChatTyping');
    if (!panel || !openBtn) return;

    function refreshIcons() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            try { window.lucide.createIcons(); } catch (e) { /* noop */ }
        }
        if (typeof window.__lucideRefresh === 'function') {
            try { window.__lucideRefresh(); } catch (e) { /* noop */ }
        }
    }

    function esc(value) {
        return String(value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function scrollBottom() {
        if (messages) messages.scrollTop = messages.scrollHeight;
    }

    function isOpen() {
        return !panel.classList.contains('hidden');
    }

    function openPanel() {
        panel.classList.remove('hidden');
        panel.classList.add('flex');
        panel.setAttribute('aria-hidden', 'false');
        openBtn.setAttribute('aria-expanded', 'true');
        refreshIcons();
        setTimeout(function () { if (input) input.focus(); }, 120);
    }

    function closePanel() {
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        panel.setAttribute('aria-hidden', 'true');
        openBtn.setAttribute('aria-expanded', 'false');
    }

    function addUserBubble(text) {
        var row = document.createElement('div');
        row.className = 'flex justify-end';
        row.innerHTML = '<div class="bg-brand-gradient-br max-w-[85%] rounded-2xl rounded-tr-md px-3.5 py-2.5 text-[13px] leading-relaxed text-white shadow-sm">' + esc(text) + '</div>';
        messages.appendChild(row);
        scrollBottom();
    }

    function addAssistantBubble(html) {
        var row = document.createElement('div');
        row.className = 'flex justify-start';
        row.innerHTML = '<div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">' + html + '</div>';
        messages.appendChild(row);
        scrollBottom();
    }

    function showTyping(show) {
        if (!typing) return;
        typing.classList.toggle('hidden', !show);
        typing.classList.toggle('flex', !!show);
        if (show) scrollBottom();
    }

    var demoTimer = null;

    function demoReply() {
        showTyping(true);
        if (demoTimer) clearTimeout(demoTimer);
        demoTimer = setTimeout(function () {
            showTyping(false);
            addAssistantBubble('Bedankt voor je bericht! De live-assistent komt binnenkort beschikbaar. Bel ons gerust op <strong>055 203 21 45</strong>.');
            refreshIcons();
        }, 800);
    }

    openBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isOpen()) closePanel();
        else openPanel();
    });

    if (closeBtn) closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closePanel();
    });

    document.addEventListener('click', function (e) {
        if (!isOpen()) return;
        if (panel.contains(e.target) || openBtn.contains(e.target)) return;
        closePanel();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) closePanel();
    });

    messages.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('[data-suggestion]') : null;
        if (!btn || !input) return;
        input.value = btn.getAttribute('data-suggestion') || '';
        input.focus();
    });

    if (form) form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = input ? input.value.trim().slice(0, 500) : '';
        if (!text) return;
        addUserBubble(text);
        if (input) input.value = '';
        var sugg = document.getElementById('aiChatSuggestions');
        if (sugg) sugg.remove();
        demoReply();
    });

    refreshIcons();
})();

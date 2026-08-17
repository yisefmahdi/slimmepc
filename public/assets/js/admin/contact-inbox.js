(function () {
'use strict';

const state = {
    search: '',
    status: '',
    perPage: 10,
    page: 1,
    currentId: null,
    deletingId: null,
    replyLoading: false,
    searchTimer: null,
    unreadTotal: null,
    mobileChatOpen: false,
};

/* True below the lg breakpoint (single-pane mobile chat). */
function isMobile() {
    return window.matchMedia('(max-width: 1023.98px)').matches;
}

function showListPane() {
    state.mobileChatOpen = false;
    $('#inboxListPane').removeClass('hidden');
    $('#inboxChatPane').addClass('hidden').removeClass('flex');
    if (isMobile()) $('#inboxPageHeader').removeClass('hidden');
}

function showChatPane() {
    state.mobileChatOpen = true;
    $('#inboxListPane').addClass('hidden');
    $('#inboxChatPane').removeClass('hidden').addClass('flex');
    if (isMobile()) $('#inboxPageHeader').addClass('hidden');
}

/* ---------- helpers ---------- */
function esc(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatDateTime(value) {
    if (!value) return '—';
    const date = new Date(value);
    return date.toLocaleDateString('nl-NL', {
        day: '2-digit', month: 'short', year: 'numeric',
    }) + ' ' + date.toLocaleTimeString('nl-NL', {
        hour: '2-digit', minute: '2-digit',
    });
}

function formatDate(value) {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('nl-NL', {
        day: '2-digit', month: 'short', year: 'numeric',
    });
}

function subjectLabel(value) {
    return {
        reparatie: 'Reparatie',
        diagnose: 'Diagnose',
        'data-recovery': 'Data recovery',
        zakelijk: 'Zakelijke IT-dienst',
        stage: 'Stage',
        anders: 'Anders',
    }[value] || value;
}

function typeLabel(value) {
    return {
        reparatie: 'Reparatie',
        zakelijk: 'Zakelijk',
        'algemene-vraag': 'Algemene vraag',
        stage: 'Stage',
    }[value] || value;
}

function statusLabel(value) {
    return {
        new: 'Nieuw',
        in_progress: 'In behandeling',
        replied: 'Beantwoord',
        closed: 'Gesloten',
    }[value] || value;
}

function statusBadge(value) {
    const styles = {
        new: 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        in_progress: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        replied: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        closed: 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
    };
    return `<span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-bold ${styles[value] || styles.closed}">${statusLabel(value)}</span>`;
}

function snippet(message, len = 70) {
    const text = String(message || '').replace(/\s+/g, ' ').trim();
    return esc(text.length > len ? text.slice(0, len) + '…' : text);
}

function initial(name) {
    return esc(String(name || '?').charAt(0).toUpperCase());
}

/* ---------- data loading ---------- */
async function load() {
    try {
        const { data } = await axios.get('/admin/contact-inbox/data', {
            params: {
                search: state.search,
                status: state.status,
                per_page: state.perPage,
                page: state.page,
            },
        });

        renderCounts(data.counts);
        renderList(data.data);
        renderPagination(data.pagination);

        if (state.currentId) {
            const stillExists = data.data.some((s) => s.id === state.currentId);
            if (!stillExists && state.page > 1) {
                state.page = 1;
                load();
            }
        }
    } catch (error) {
        window.SlimmePC.toast.error('Kan de inbox niet laden. Probeer het opnieuw.');
    }
}

function renderCounts(counts) {
    $('#inboxCountNew').text('Nieuw: ' + counts.new);
    $('#inboxCountTotal').text('Totaal: ' + counts.total);

    const $unread = $('#inboxCountUnread');
    if (counts.unread > 0) {
        $unread.removeClass('hidden').text('Ongelezen: ' + counts.unread);
    } else {
        $unread.addClass('hidden');
    }

    state.unreadTotal = counts.unread;
}

function renderList(rows) {
    const $el = $('#inboxList');

    if (!rows.length) {
        $el.html(`
            <div class="flex flex-col items-center gap-3 px-6 py-16 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </span>
                <p class="font-semibold" style="color: var(--c-heading)">Geen aanvragen gevonden</p>
                <p class="text-xs" style="color: var(--c-muted)">Pas je zoekopdracht of filters aan.</p>
            </div>
        `);
        return;
    }

    $el.html(rows.map((s) => {
        const last = s.last_message;
        const preview = last
            ? (last.sender === 'admin' ? 'Jij: ' : '') + last.body
            : s.message;
        const time = last ? last.created_at : s.created_at;
        const unread = parseInt(s.unread, 10) || 0;

        return `
        <button type="button" data-inbox-open="${s.id}"
                class="group flex w-full items-start gap-3 border-b px-4 py-4 text-start transition hover:bg-blue-50/50 dark:hover:bg-slate-800/40 ${state.currentId === s.id ? 'bg-blue-50/60 dark:bg-slate-800/60' : ''}"
                style="border-color: rgba(148, 163, 184, 0.12)">
            <span class="relative mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-sm font-bold text-white">
                ${initial(s.name)}
                ${s.status === 'new' && unread > 0 ? '<span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900"></span>' : ''}
            </span>
            <span class="min-w-0 flex-1">
                <span class="flex items-baseline justify-between gap-2">
                    <span class="truncate text-sm font-bold ${unread ? '' : 'font-semibold'}" style="color: var(--c-heading)">${esc(s.name)}</span>
                    <span class="shrink-0 text-[10px] font-medium" style="color: var(--c-muted)">${formatDate(time)}</span>
                </span>
                <span class="mt-0.5 flex items-center justify-between gap-2">
                    <span class="truncate text-[11px] font-semibold" style="color: var(--c-muted)">${esc(subjectLabel(s.subject))}</span>
                    ${statusBadge(s.status)}
                </span>
                <span class="mt-1 flex items-center justify-between gap-2">
                    <span class="truncate text-xs ${unread ? 'font-bold' : ''}" style="color: var(--c-muted)">${snippet(preview, 55)}</span>
                    ${unread ? `<span class="inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold leading-none text-white">${unread > 99 ? '99+' : unread}</span>` : ''}
                </span>
            </span>
        </button>
        `;
    }).join(''));
}

function renderPagination(pag) {
    const $el = $('#inboxPagination');

    if (pag.last <= 1) {
        $el.html('');
        return;
    }

    const btn = (label, page, disabled = false, arrow = false) => `
        <button type="button" data-inbox-page="${page}" ${disabled ? 'disabled' : ''}
                class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-bold transition
                       ${disabled ? 'cursor-not-allowed opacity-40' : 'hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/30'}"
                       style="color: var(--c-heading); border-color: rgba(148, 163, 184, 0.25)">
            ${arrow ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="${label === 'Vorige' ? 'M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18' : 'm13.5 4.5 7.5 7.5-7.5 7.5M21 12H3'}" />
            </svg>` : label}
        </button>`;

    $el.html(`
        <div class="flex items-center justify-between gap-2">
            <p class="text-[11px]" style="color: var(--c-muted)">
                ${pag.total} · ${pag.current}/${pag.last}
            </p>
            <div class="flex items-center gap-1.5">
                ${btn('Vorige', pag.current - 1, pag.current <= 1, true)}
                ${btn('Volgende', pag.current + 1, pag.current >= pag.last, true)}
            </div>
        </div>
    `);
}

/* ---------- chat ---------- */
let chatRequestSeq = 0;

async function openChat(id) {
    const seq = ++chatRequestSeq;
    state.currentId = id;

    if (isMobile()) showChatPane();

    refreshListHighlight();
    $('#inboxChatEmpty').addClass('hidden');
    $('#inboxChat').removeClass('hidden').addClass('flex');

    /* Show a loading state in the thread so the click feels instant. */
    $('#inboxReply').val('').prop('disabled', true).css('height', '');
    $('#inboxThread').html(`
        <div class="flex h-full min-h-40 flex-col items-center justify-center gap-3 text-center">
            <span class="spinner spinner-sm" aria-hidden="true"></span>
            <p class="text-xs font-semibold" style="color: var(--c-muted)">Gesprek laden...</p>
        </div>
    `);

    try {
        const { data } = await axios.get(`/admin/contact-inbox/${id}`);
        if (seq !== chatRequestSeq) return; /* a newer chat was opened meanwhile */

        const s = data.submission;

        $('#inboxAvatar').text(initial(s.name));
        $('#inboxName').text(s.name);
        $('#inboxMeta').text(esc(s.email) + (s.phone ? ' • ' + esc(s.phone) : ''));

        $('#inboxSubject').text(subjectLabel(s.subject));
        $('#inboxType').text(typeLabel(s.request_type));
        $('#inboxPhone').text(s.phone || '—');
        $('#inboxDate').text(formatDate(s.created_at));

        const $attachment = $('#inboxAttachmentBtn');
        if (data.has_attachment) {
            $attachment.removeClass('hidden').addClass('inline-flex');
            $attachment.attr('href', `/admin/contact-inbox/${id}/attachment`);
        } else {
            $attachment.addClass('hidden').removeClass('inline-flex');
        }

        $('#inboxStatusSelect').val(s.status);
        $('#inboxReply').val('').prop('disabled', false).css('height', '');
        autoGrowReply();

        renderThread(data);
        refreshListHighlight();
        load();
    } catch (error) {
        if (seq !== chatRequestSeq) return;
        $('#inboxChatEmpty').removeClass('hidden');
        $('#inboxChat').addClass('hidden').removeClass('flex');
        $('#inboxReply').prop('disabled', false);
        window.SlimmePC.toast.error('Kon het bericht niet laden.');
    }
}

function renderThread(data) {
    const s = data.submission;

    const bubble = (reply) => {
        const isAdmin = reply.sender === 'admin';
        const hasFile = reply.attachment && reply.source === 'inbound';

        return `
            <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                <div class="max-w-[85%] sm:max-w-[70%]">
                    <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed ${isAdmin
                        ? 'rounded-br-md bg-gradient-to-r from-[#075be8] to-[#064bd7] text-white'
                        : 'rounded-bl-md bg-slate-100 dark:bg-slate-800'}"
                         style="${isAdmin ? '' : 'color: var(--c-heading)'}">
                        ${esc(reply.body).replace(/\n/g, '<br>')}
                        ${hasFile ? attachmentHtml(reply) : ''}
                    </div>
                    <p class="mt-1 text-[10px] ${isAdmin ? 'text-end' : ''}" style="color: var(--c-muted)">
                        ${isAdmin ? 'Jij' : esc(s.name)} • ${formatDateTime(reply.created_at)}
                        ${reply.source === 'inbound' ? ' • <span class="font-semibold">via e-mail</span>' : ''}
                    </p>
                </div>
            </div>
        `;
    };

    function attachmentHtml(reply) {
        const name = (reply.attachment || '').split('/').pop() || '';
        if (!name) return '';
        const url = `/admin/contact-inbox/reply/${reply.id}/attachment`;
        const isImage = /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(name);

        if (isImage) {
            return `<a href="${url}" target="_blank" rel="noopener" class="mt-2.5 block" title="Open: ${esc(name)}">
                <img src="${url}" alt="${esc(name)}" class="max-h-44 w-auto rounded-xl border border-white/20 bg-white/10 object-contain shadow-sm">
            </a>`;
        }

        return `<a href="${url}" download="${esc(name)}"
                class="mt-2.5 inline-flex max-w-full items-center gap-2 rounded-lg border bg-white/10 px-3 py-2 text-xs font-semibold text-slate-100 shadow-sm transition hover:border-white/40 hover:bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                </svg>
                <span class="truncate">${esc(name)}</span>
            </a>`;
    }

    const original = `
        <div class="flex justify-start">
            <div class="max-w-[85%] sm:max-w-[70%]">
                <div class="rounded-2xl rounded-bl-md border px-4 py-3 text-sm leading-relaxed" style="border-color: rgba(148, 163, 184, 0.2); color: var(--c-heading); background-color: var(--c-input-bg)">
                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-wider" style="color: var(--c-muted)">
                        Oorspronkelijke aanvraag • ${formatDateTime(s.created_at)}
                    </p>
                    ${esc(s.message).replace(/\n/g, '<br>')}
                </div>
            </div>
        </div>
    `;

    $('#inboxThread').html(original + data.replies.map(bubble).join(''));
    scrollThreadToBottom();
}

function scrollThreadToBottom() {
    const $thread = $('#inboxThread');
    $thread.scrollTop($thread[0].scrollHeight);
}

function appendAdminBubble(reply) {
    $('#inboxThread').append(`
        <div class="flex justify-end">
            <div class="max-w-[85%] sm:max-w-[70%]">
                <div class="rounded-2xl rounded-br-md bg-gradient-to-r from-[#075be8] to-[#064bd7] px-4 py-3 text-sm leading-relaxed text-white">
                    ${esc(reply.body).replace(/\n/g, '<br>')}
                </div>
                <p class="mt-1 text-end text-[10px]" style="color: var(--c-muted)">
                    Jij • ${formatDateTime(reply.created_at)}
                </p>
            </div>
        </div>
    `);
    scrollThreadToBottom();
}

function refreshListHighlight() {
    $('[data-inbox-open]').each(function () {
        $(this).toggleClass('bg-blue-50/60 dark:bg-slate-800/60', $(this).data('inbox-open') === state.currentId);
    });
}

/* ---------- actions ---------- */
function autoGrowReply() {
    const $el = $('#inboxReply');
    $el.css('height', 'auto');
    $el.css('height', Math.max(52, $el[0].scrollHeight) + 'px');
}

async function sendReply() {
    const $input = $('#inboxReply');
    const body = $input.val().trim();

    if (!state.currentId) return;
    if (!body) {
        window.SlimmePC.toast.error('Typ eerst een antwoord.');
        $input.focus();
        return;
    }
    if (state.replyLoading) return;
    state.replyLoading = true;

    const $btn = $('#inboxReplyBtn').prop('disabled', true).addClass('cursor-not-allowed opacity-70');

    try {
        const { data } = await axios.post(`/admin/contact-inbox/${state.currentId}/reply`, { body });

        $input.val('');
        $input.css('height', '');
        autoGrowReply();
        $('#inboxStatusSelect').val(data.status);
        appendAdminBubble(data.reply);
        updateBadge();
        load();
        window.SlimmePC.toast.success(data.message);
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Antwoord verzenden mislukt.');
    } finally {
        state.replyLoading = false;
        $btn.prop('disabled', false).removeClass('cursor-not-allowed opacity-70');
    }
}

async function changeStatus(id, status) {
    try {
        const { data } = await axios.post(`/admin/contact-inbox/${id}/status`, { status });
        window.SlimmePC.toast.success(data.message);
        updateBadge();
        load();
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Status wijzigen mislukt.');
        await openChat(id);
    }
}

function askDelete(id, name) {
    state.deletingId = id;
    $('#inboxDeleteName').text(`"${esc(name)}"`);
    window.SlimmePC.modal.open('inboxDeleteModal');
}

async function confirmDelete() {
    if (!state.deletingId) return;

    const $btn = $('#inboxDeleteConfirmBtn')
        .prop('disabled', true)
        .addClass('cursor-not-allowed opacity-70')
        .html('<span class="spinner spinner-sm mr-2"></span>Bezig...');

    try {
        await axios.delete(`/admin/contact-inbox/${state.deletingId}`);
        window.SlimmePC.toast.success('Aanvraag succesvol verwijderd.');
        window.SlimmePC.modal.close('inboxDeleteModal');

        if (state.currentId === state.deletingId) {
            state.currentId = null;
            $('#inboxChat').addClass('hidden').removeClass('flex');
            $('#inboxChatEmpty').removeClass('hidden');
            if (isMobile()) showListPane();
        }

        state.deletingId = null;
        updateBadge();
        await load();
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Verwijderen mislukt.');
    } finally {
        $btn.prop('disabled', false).removeClass('cursor-not-allowed opacity-70')
            .html('Ja, verwijderen');
    }
}

/* ---------- badge ---------- */
async function updateBadge() {
    try {
        const { data } = await axios.get('/admin/contact-inbox/new-count');
        const $badge = $('#sidebarInboxBadge');
        if (data.count > 0) {
            $badge.removeClass('hidden').text(data.count > 99 ? '99+' : data.count);
        } else {
            $badge.addClass('hidden');
        }
    } catch (error) {
        // silent — badge refresh is best-effort
    }
}

/* ---------- inbound sync (30s poll) ---------- */
function playNotificationSound() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;

        const ctx = new Ctx();
        if (ctx.state === 'suspended') ctx.resume();

        const beep = (freq, start, dur) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            osc.connect(gain);
            gain.connect(ctx.destination);
            gain.gain.setValueAtTime(0.0001, ctx.currentTime + start);
            gain.gain.exponentialRampToValueAtTime(0.25, ctx.currentTime + start + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + start + dur);
            osc.start(ctx.currentTime + start);
            osc.stop(ctx.currentTime + start + dur);
        };

        beep(880, 0, 0.18);
        beep(1174.66, 0.22, 0.22);
    } catch (error) {
        // sound is optional
    }
}

async function syncInbound() {
    try {
        const { data } = await axios.post('/admin/contact-inbox/sync');

        // New unread messages arrived while the page was open → notify.
        if (state.unreadTotal !== null && data.counts.unread > state.unreadTotal) {
            playNotificationSound();
        }

        renderCounts(data.counts);

        const $badge = $('#sidebarInboxBadge');
        if (data.counts.new > 0) {
            $badge.removeClass('hidden').text(data.counts.new > 99 ? '99+' : data.counts.new);
        } else {
            $badge.addClass('hidden');
        }

        await load();

        // Refresh the open thread so new inbound replies appear — but never
        // clobber a reply the admin is currently typing.
        if (state.currentId && !$('#inboxReply').val().trim()) {
            try {
                const chat = await axios.get(`/admin/contact-inbox/${state.currentId}`);
                renderThread(chat.data);
            } catch (error) {
                // best-effort
            }
        }
    } catch (error) {
        // best-effort — polling continues
    }
}

/* ---------- init / bindings ---------- */
$(function () {
    if (!$('#inboxList').length) return;

    load();

    // Deep link from the admin notification e-mail (?submission={id}): open that thread
    const deepId = parseInt(new URLSearchParams(window.location.search).get('submission'), 10);
    if (deepId) openChat(deepId);

    // Search (debounced)
    $('#inboxSearch').on('input', function () {
        window.clearTimeout(state.searchTimer);
        const value = $(this).val().trim();
        state.searchTimer = window.setTimeout(() => {
            state.search = value;
            state.page = 1;
            load();
        }, 350);
    });

    // Filters
    $('#inboxStatusFilter').on('change', function () {
        state.status = $(this).val();
        state.page = 1;
        load();
    });

    $('#inboxPerPage').on('change', function () {
        state.perPage = parseInt($(this).val(), 10);
        state.page = 1;
        load();
    });

    // Pagination
    $(document).on('click', '[data-inbox-page]', function () {
        state.page = parseInt($(this).data('inbox-page'), 10);
        load();
    });

    // Open chat
    $(document).on('click', '[data-inbox-open]', function () {
        openChat($(this).data('inbox-open'));
    });

    // Back (mobile)
    $('#inboxBackBtn').on('click', function () {
        $('#inboxChat').addClass('hidden').removeClass('flex');
        $('#inboxChatEmpty').removeClass('hidden');
        if (isMobile()) showListPane();
    });

    // Status select
    $('#inboxStatusSelect').on('change', function () {
        if (state.currentId) changeStatus(state.currentId, $(this).val());
    });

    // Reply
    $('#inboxReplyBtn').on('click', sendReply);
    $('#inboxReply').on('input', autoGrowReply);
    $('#inboxReply').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendReply();
        }
    });

    // Delete
    $('#inboxDeleteBtn').on('click', function () {
        if (state.currentId) askDelete(state.currentId, $('#inboxName').text());
    });
    $('#inboxDeleteConfirmBtn').on('click', confirmDelete);

    // Pull inbound e-mail replies every 30s while the inbox page is open
    window.setInterval(syncInbound, 30000);
});
})();
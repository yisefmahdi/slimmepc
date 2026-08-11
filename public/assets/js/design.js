
/* =====================================================
   Theme (light / dark) — persists in localStorage
   ===================================================== */
window.SlimmePC = window.SlimmePC || {};

window.SlimmePC.theme = {
    get: () => {
        const saved = localStorage.getItem('slimmepc-theme');
        if (saved === 'light' || saved === 'dark') return saved;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },

    apply: (theme) => {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem('slimmepc-theme', theme);

        $('[data-theme-toggle]').each(function () {
            $(this).attr('aria-pressed', theme === 'dark');
            $(this).find('[data-theme-icon-light]').toggle(theme !== 'dark');
            $(this).find('[data-theme-icon-dark]').toggle(theme === 'dark');
        });
    },

    toggle: () => {
        const next = window.SlimmePC.theme.get() === 'dark' ? 'light' : 'dark';
        window.SlimmePC.theme.apply(next);
    },
};

$(function () {
    window.SlimmePC.theme.apply(window.SlimmePC.theme.get());

    $(document).on('click', '[data-theme-toggle]', (e) => {
        e.preventDefault();
        window.SlimmePC.theme.toggle();
    });
});

/* =====================================================
   Loading states — submit buttons
   Usage: <button type="submit" data-loading>...</button>
   Optionally: data-loading-text="Saving..."
   Spinner element is created automatically.
   ===================================================== */
function setButtonLoading($btn, loading) {
    if (loading) {
        const $spinner = $('<span>', { class: 'spinner spinner-sm mr-2', 'aria-hidden': 'true' });
        $btn.data('original-content', $btn.html());
        $btn.data('original-text', $btn.text().trim());
        $btn.prop('disabled', true).attr('aria-busy', 'true').addClass('cursor-not-allowed');
        $btn.empty().append($spinner);

        const text = $btn.data('loading-text') || $btn.data('original-text');
        if (text) $btn.append(document.createTextNode(' ' + text));
    } else {
        const original = $btn.data('original-content');
        $btn.prop('disabled', false).removeAttr('aria-busy').removeClass('cursor-not-allowed');
        if (original !== undefined) $btn.html(original);
    }
}

$(function () {
    $(document).on('submit', 'form[data-loading]', function () {
        const $btn = $(this).find('[type="submit"][data-loading]').first();
        if ($btn.length) setButtonLoading($btn, true);
    });

    $(document).on('submit', 'form', function () {
        const $btn = $(this).find('[type="submit"]').first();
        if ($btn.length && !$btn.data('original-content') && $btn.attr('data-loading') !== undefined) {
            setButtonLoading($btn, true);
        }
    });
});

/* =====================================================
   Loading states — links / buttons (data-loading)
   A click triggers a full-page loading overlay.
   ===================================================== */
$(function () {
    $(document).on('click', 'a[data-loading], button[data-loading][type="button"], [data-link-loading]', function (e) {
        const $el = $(this);
        const $submitBtn = $el.closest('form').find('[type="submit"]').first();

        if ($el.attr('data-link-loading') === 'stay' || $el.hasClass('link-stay')) {
            setButtonLoading($el, true);
            return;
        }

        setButtonLoading($el, true);
    });
});

/* =====================================================
   Password visibility toggle
   Usage: <button type="button" data-toggle-password="fieldId">
   ===================================================== */
$(function () {
    $(document).on('click', '[data-toggle-password]', function () {
        const $btn = $(this);
        const $input = $('#' + $btn.data('toggle-password'));

        if (!$input.length) return;

        const hidden = $input.attr('type') === 'password';
        $input.attr('type', hidden ? 'text' : 'password');

        $btn.find('[data-eye-open]').toggle(hidden);
        $btn.find('[data-eye-closed]').toggle(!hidden);
        $btn.attr('aria-label', hidden ? 'Hide password' : 'Show password');
    });
});

/* =====================================================
   Auto-dismiss alerts
   ===================================================== */
$(function () {
    $(document).on('click', '[data-dismiss-alert]', function () {
        $(this).closest('[data-alert]').fadeOut(200);
    });

    window.setTimeout(() => {
        $('[data-alert][data-auto-dismiss]').fadeOut(300);
    }, 4000);
});

/* =====================================================
   Modals
   Usage: <div id="modal-{id}" class="hidden">...</div>
   window.SlimmePC.modal.open('id') / .close('id')
   Elements: [data-modal-close], [data-modal-overlay]
   ===================================================== */
window.SlimmePC.modal = {
    open(id) {
        const $modal = $('#modal-' + id);
        if (!$modal.length) return;
        $modal.removeClass('hidden');
        $modal.attr('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        const $firstFocusable = $modal.find('input, select, textarea, button:not([data-modal-close])').first();
        window.setTimeout(() => $firstFocusable.trigger('focus'), 150);
    },

    close(id) {
        const $modal = $('#modal-' + id);
        if (!$modal.length) return;
        $modal.addClass('hidden');
        $modal.attr('aria-hidden', 'true');
        document.body.style.overflow = '';
    },
};

$(function () {
    $(document).on('click', '[data-modal-close]', function () {
        window.SlimmePC.modal.close($(this).closest('[id^="modal-"]').attr('id').replace('modal-', ''));
    });

    $(document).on('click', '[data-modal-overlay]', function () {
        window.SlimmePC.modal.close($(this).closest('[id^="modal-"]').attr('id').replace('modal-', ''));
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('[id^="modal-"]:not(.hidden)').each(function () {
                window.SlimmePC.modal.close($(this).attr('id').replace('modal-', ''));
            });
        }
    });
});

/* =====================================================
   Toasts
   Usage: window.SlimmePC.toast.success('msg') / .error('msg')
   ===================================================== */
function showToast(message, type) {
    const $toast = $(
        '<div data-toast class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl border px-4 py-3 shadow-xl" style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.25)">' +
            '<span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full ' + (type === 'error' ? 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400') + '">' +
                (type === 'error'
                    ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>') +
            '</span>' +
            '<p class="flex-1 text-sm font-medium leading-5" style="color: var(--c-heading)"></p>' +
            '<button type="button" class="shrink-0" style="color: var(--c-muted)" aria-label="Sluiten">' +
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>' +
            '</button>' +
        '</div>'
    );

    $toast.find('p').text(message);

    let $container = $('[data-toast-container]');
    if (!$container.length) {
        $container = $(
            '<div data-toast-container class="pointer-events-none fixed bottom-5 right-5 z-[70] flex w-auto max-w-sm flex-col gap-3"></div>'
        ).appendTo('body');
    }

    $toast.appendTo($container).css({ opacity: 0, transform: 'translateY(12px)' });
    $toast.animate({ opacity: 1, transform: 'translateY(0)' }, 200);

    const timeout = window.setTimeout(() => dismiss($toast), 4000);

    function dismiss($el) {
        window.clearTimeout(timeout);
        $el.animate({ opacity: 0, transform: 'translateY(8px)' }, 200, () => $el.remove());
    }

    $toast.find('button').on('click', () => dismiss($toast));
}

window.SlimmePC.toast = {
    success: (message) => showToast(message, 'success'),
    error: (message) => showToast(message, 'error'),
};

/* =====================================================
   Generic confirm dialog (built dynamically)
   Usage: window.SlimmePC.confirm(message, onConfirm, onCancel)
   ===================================================== */
function escHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

window.SlimmePC.confirm = function (message, onConfirm, onCancel) {
    const id = 'generic-confirm';

    $('#modal-' + id).remove();

    const $modal = $(
        '<div id="modal-' + id + '" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-' + id + '-title">' +
            '<div data-modal-overlay class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm modal-overlay-anim"></div>' +
            '<div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">' +
                '<div class="modal-panel-anim w-full max-w-md overflow-hidden rounded-2xl border shadow-2xl" style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2)">' +
                    '<div class="flex items-center justify-between gap-4 border-b px-6 py-4" style="border-color: rgba(148, 163, 184, 0.15)">' +
                        '<h3 id="modal-' + id + '-title" class="text-base font-bold" style="color: var(--c-heading)">Weet je het zeker?</h3>' +
                        '<button type="button" data-modal-close aria-label="Sluiten" class="rounded-lg p-2 transition hover:bg-slate-100 dark:hover:bg-slate-800" style="color: var(--c-muted)">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>' +
                        '</button>' +
                    '</div>' +
                    '<div class="p-6">' +
                        '<p class="text-sm font-medium leading-6" style="color: var(--c-heading)">' + escHtml(message) + '</p>' +
                    '</div>' +
                    '<div class="flex flex-col-reverse items-stretch justify-end gap-3 border-t px-6 py-4 sm:flex-row sm:items-center" style="border-color: rgba(148, 163, 184, 0.15)">' +
                        '<button type="button" data-confirm-cancel class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>' +
                        '<button type="button" data-confirm-ok class="inline-flex h-11 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">Bevestigen</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>'
    ).appendTo('body');

    window.SlimmePC.modal.open(id);

    const done = (callback) => () => {
        window.SlimmePC.modal.close(id);
        $modal.remove();
        if (callback) callback();
    };

    $modal.find('[data-confirm-ok]').on('click', done(onConfirm));
    $modal.find('[data-confirm-cancel], [data-modal-close], [data-modal-overlay]').on('click', done(onCancel));
};

/* =====================================================
   Axios defaults (global) — CSRF + JSON headers
   ===================================================== */
window.axios = axios;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content || '';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
(function () {
    'use strict';

    var form = document.getElementById('contactForm');
    if (!form) return;

    var submitBtn = document.getElementById('contactFormSubmit');
    var attaching = document.getElementById('contactAttachmentZone');
    var attachInput = document.getElementById('contact-attachment');
    var defaultZone = attaching ? attaching.innerHTML : '';
    var submitting = false;

    function esc(value) {
        return String(value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function formatBytes(bytes) {
        if (!bytes) return '';
        var units = ['B', 'KB', 'MB', 'GB'];
        var n = bytes, i = 0;
        while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
        return n.toFixed(n >= 10 || i === 0 ? 0 : 1) + ' ' + units[i];
    }

    var attachmentPreviewUrl = null;

    function revokePreviewUrl() {
        if (attachmentPreviewUrl) {
            URL.revokeObjectURL(attachmentPreviewUrl);
            attachmentPreviewUrl = null;
        }
    }

    function renderAttachment() {
        if (!attaching || !attachInput) return;
        var file = attachInput.files && attachInput.files[0];

        if (!file) {
            revokePreviewUrl();
            attaching.innerHTML = defaultZone;
            return;
        }

        var isImage = file.type.indexOf('image/') === 0;
        var isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
        var visual;

        if (isImage) {
            attachmentPreviewUrl = URL.createObjectURL(file);
            visual = '<img src="' + attachmentPreviewUrl + '" alt="Voorbeeld" class="h-16 w-16 shrink-0 rounded-xl border border-blue-100 object-cover shadow-sm">';
        } else {
            visual = '<span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-2xl shadow-sm" aria-hidden="true">' + (isPdf ? '&#128221;' : '&#128206;') + '</span>';
        }

        attaching.innerHTML =
            '<span class="flex w-full items-center gap-4">' +
            visual +
            '<span class="min-w-0 flex-1 text-start">' +
            '<span class="block truncate text-sm font-bold text-[#0b1f4d]">' + esc(file.name) + '</span>' +
            '<span class="mt-0.5 block text-xs text-slate-500">' + esc(file.type || 'Bestand') + ' &middot; ' + formatBytes(file.size) + '</span>' +
            '</span>' +
            '<button type="button" id="contactAttachmentRemove" aria-label="Bestand verwijderen" ' +
            'class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-red-300 hover:text-red-500">' +
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>' +
            '</button>' +
            '</span>';

        attaching.querySelector('#contactAttachmentRemove').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            attachInput.value = '';
            renderAttachment();
        });
    }

    if (attachInput) {
        attachInput.addEventListener('change', renderAttachment);
    }

    function showPopup(type, title, message) {
        var success = type === 'success';

        var overlay = document.createElement('div');
        overlay.className = 'cf-popup-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');

        overlay.innerHTML =
            '<div class="cf-popup">' +
            '<span class="cf-popup-icon ' + (success ? 'is-success' : 'is-error') + '" aria-hidden="true">' +
            (success ? '&#10003;' : '&#33;') +
            '</span>' +
            '<h3 class="cf-popup-title">' + title + '</h3>' +
            '<p class="cf-popup-text">' + message + '</p>' +
            '<button type="button" class="cf-popup-btn">' + (success ? 'Sluiten' : 'Probeer opnieuw') + '</button>' +
            '</div>';

        var close = function () {
            overlay.remove();
            document.body.classList.remove('cf-popup-open');
            document.removeEventListener('keydown', onKey);
            if (!success) submitBtn.focus();
        };

        var onKey = function (e) {
            if (e.key === 'Escape') close();
        };

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) close();
        });
        overlay.querySelector('.cf-popup-btn').addEventListener('click', close);

        document.body.appendChild(overlay);
        document.body.classList.add('cf-popup-open');
        document.addEventListener('keydown', onKey);
        overlay.querySelector('.cf-popup-btn').focus();
    }

    function setLoading(loading) {
        if (!submitBtn) return;
        submitting = loading;
        submitBtn.disabled = loading;
        submitBtn.setAttribute('aria-busy', String(loading));
        submitBtn.classList.toggle('cursor-not-allowed', loading);

        var arrow = submitBtn.lastChild;

        if (loading) {
            if (arrow) {
                submitBtn.dataset.originalArrow = arrow.innerHTML;
                arrow.innerHTML = '<span class="cf-spinner" aria-hidden="true"></span>';
            }
            submitBtn.style.opacity = '0.85';
            submitBtn.style.pointerEvents = 'none';
        } else {
            if (arrow && submitBtn.dataset.originalArrow !== undefined) {
                arrow.innerHTML = submitBtn.dataset.originalArrow;
                delete submitBtn.dataset.originalArrow;
            }
            submitBtn.style.opacity = '';
            submitBtn.style.pointerEvents = '';
        }
    }

    function firstError(errors) {
        var keys = Object.keys(errors || {});
        return keys.length ? errors[keys[0]][0] : null;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        setLoading(true);

        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(form),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok) {
                    form.reset();
                    renderAttachment();
                    showPopup(
                        'success',
                        'Bedankt!',
                        'Je bericht is verzonden. We nemen zo snel mogelijk contact met je op.'
                    );
                } else {
                    var msg = firstError(result.data && result.data.errors)
                        || (result.data && result.data.message)
                        || 'Er is iets misgegaan. Probeer het opnieuw.';
                    showPopup('error', 'Verzenden mislukt', msg);
                }
            })
            .catch(function () {
                showPopup('error', 'Verzenden mislukt', 'Er is iets misgegaan. Probeer het opnieuw.');
            })
            .finally(function () {
                setLoading(false);
            });
    });
})();
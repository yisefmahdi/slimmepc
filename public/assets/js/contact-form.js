(function () {
    'use strict';

    var form = document.getElementById('contactForm');
    if (!form) return;

    var submitBtn = document.getElementById('contactFormSubmit');
    var submitting = false;

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
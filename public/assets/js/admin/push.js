/* Admin push notifications (Firebase Cloud Messaging, web).
 * - Registers the service worker + FCM token and stores it via the notificaties routes.
 * - Handles foreground messages with a toast, and the dashboard permission banner.
 * - Uses the compat builds loaded in the admin layout (pinned, gstatic CDN —
 *   FCM itself requires Google network anyway).
 */
(function () {
    'use strict';

    function cfg() {
        var el = document.getElementById('firebase-web-config');
        if (!el) return null;
        try {
            return JSON.parse(el.textContent || '{}');
        } catch (e) {
            return null;
        }
    }

    function toast(msg, type) {
        if (window.SlimmePC && window.SlimmePC.toast) {
            window.SlimmePC.toast[type === 'error' ? 'error' : 'success'](msg);
        } else {
            alert(msg);
        }
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    async function post(url, data) {
        var res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data || {}),
        });
        var json = await res.json().catch(function () { return {}; });
        if (!res.ok) throw new Error(json.message || ('HTTP ' + res.status));
        return json;
    }

    var config = cfg();
    var supported = ('Notification' in window)
        && ('serviceWorker' in navigator)
        && typeof firebase !== 'undefined'
        && config && config.apiKey && config.appId;

    var messaging = null;

    function init() {
        if (!supported) {
            var w = document.getElementById('pushConfigWarning');
            if (w) w.classList.remove('hidden');
            return;
        }
        try {
            if (!firebase.apps.length) firebase.initializeApp(config);
            messaging = firebase.messaging();

            // Foreground messages: toast + a real system notification
            // (so it is visible even when the admin looks at another tab).
            messaging.onMessage(function (payload) {
                var title = (payload.notification && payload.notification.title) || 'Slimme-PC';
                var body = (payload.notification && payload.notification.body) || '';
                var url = (payload.data && payload.data.url) || '/admin';
                toast(title + (body ? ' — ' + body : ''), 'success');

                try {
                    if (Notification.permission === 'granted') {
                        var n = new Notification(title, { body: body, icon: '/assets/img/logo.webp', data: { url: url } });
                        n.onclick = function (ev) {
                            ev.preventDefault();
                            window.focus();
                            window.open(url, '_blank');
                            n.close();
                        };
                    }
                } catch (e) {
                    console.warn('[push] foreground notification failed', e);
                }
            });

            autoRegisterIfGranted();
        } catch (e) {
            console.warn('[push] init failed', e);
        }
    }

    async function autoRegisterIfGranted() {
        try {
            if (Notification.permission !== 'granted') return;
            var reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            var token = await messaging.getToken({ vapidKey: config.vapidKey, serviceWorkerRegistration: reg });
            if (token) await post(config.storeUrl, { token: token, platform: 'web' });
        } catch (e) {
            console.warn('[push] auto-register failed', e);
        }
    }

    async function enable() {
        if (!supported) {
            toast('Push is niet ingesteld (Firebase-config ontbreekt).', 'error');
            return;
        }
        if (!config.vapidKey) {
            toast('FIREBASE_VAPID_KEY ontbreekt in .env.', 'error');
            return;
        }
        try {
            var permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                toast('Meldingen zijn geblokkeerd. Sta ze toe via de browser-instellingen.', 'error');
                return;
            }
            var reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            var token = await messaging.getToken({ vapidKey: config.vapidKey, serviceWorkerRegistration: reg });
            var json = await post(config.storeUrl, {
                token: token,
                platform: 'web',
                device_label: navigator.platform || 'Browser',
            });
            toast(json.message || 'Notificaties ingeschakeld.', 'success');
            hideBanner();
            setTimeout(function () { window.location.reload(); }, 900);
        } catch (e) {
            toast('Inschakelen mislukt: ' + e.message, 'error');
        }
    }

    /* --- dashboard banner (permission on dashboard entry) --- */
    var BANNER_KEY = 'slimmepc-push-banner-snooze';

    function snoozed() {
        try {
            var until = parseInt(localStorage.getItem(BANNER_KEY) || '0', 10);
            return Date.now() < until;
        } catch (e) {
            return false;
        }
    }

    function hideBanner() {
        var b = document.getElementById('pushBanner');
        if (b) b.style.display = 'none';
    }

    function setupBanner() {
        var banner = document.getElementById('pushBanner');
        if (!banner) return;
        // Only prompt when the browser hasn't decided yet.
        if (!('Notification' in window) || Notification.permission !== 'default' || snoozed()) {
            banner.style.display = 'none';
            return;
        }
        banner.style.display = '';
        var ok = document.getElementById('pushBannerEnable');
        var later = document.getElementById('pushBannerLater');
        if (ok) ok.addEventListener('click', enable);
        if (later) later.addEventListener('click', function () {
            try {
                localStorage.setItem(BANNER_KEY, String(Date.now() + 7 * 24 * 3600 * 1000));
            } catch (e) {}
            hideBanner();
        });
    }

    function setupButtons() {        var enableBtn = document.getElementById('pushEnableBtn');
        if (enableBtn) enableBtn.addEventListener('click', enable);

        var testBtn = document.getElementById('pushTestBtn');
        if (testBtn) testBtn.addEventListener('click', async function () {
            try {
                var json = await post(config.storeUrl && config.testUrl, {});
                toast(json.message || 'Testmelding verzonden.', 'success');
            } catch (e) {
                toast(e.message, 'error');
            }
        });

        document.querySelectorAll('.push-delete-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                if (!confirm('Dit apparaat verwijderen voor pushmeldingen?')) return;
                try {
                    var res = await fetch('/admin/notificaties/tokens/' + btn.getAttribute('data-id'), {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    btn.closest('div').remove();
                    toast('Apparaat verwijderd.', 'success');
                } catch (e) {
                    toast('Verwijderen mislukt: ' + e.message, 'error');
                }
            });
        });
    }

    /* --- self-diagnostics on the notificaties page --- */
    async function updateDiag() {
        function set(id, text, ok) {
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = text;
            el.style.color = ok ? '#15803d' : '#b91c1c';
        }

        if (!document.getElementById('pushDiagList')) return;

        set('pushDiagPermission',
            ('Notification' in window) ? Notification.permission : 'niet ondersteund',
            ('Notification' in window) && Notification.permission === 'granted');

        set('pushDiagSDK', (typeof firebase !== 'undefined') ? 'geladen' : 'NIET geladen',
            typeof firebase !== 'undefined');

        set('pushDiagConfig', (config && config.apiKey && config.appId) ? 'ok' : 'onvolledig (.env)',
            !!(config && config.apiKey && config.appId));

        if (!('serviceWorker' in navigator)) {
            set('pushDiagSW', 'niet ondersteund', false);
            return;
        }
        try {
            var reg = await navigator.serviceWorker.getRegistration('/firebase-messaging-sw.js');
            if (!reg) reg = await navigator.serviceWorker.getRegistration();
            set('pushDiagSW', reg ? ('actief (' + (reg.active ? reg.active.state : 'geen active worker') + ')') : 'NIET geregistreerd',
                !!(reg && reg.active));
        } catch (e) {
            set('pushDiagSW', 'fout: ' + e.message, false);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        init();
        setupBanner();
        setupButtons();
        updateDiag();
    });
})();

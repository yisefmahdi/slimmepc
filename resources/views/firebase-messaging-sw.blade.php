/* Firebase Cloud Messaging service worker.
 * Served via the /firebase-messaging-sw.js route (config injected server-side),
 * so no secrets are hardcoded in a public file.
 * Background push -> notification with deep-link; click opens/focuses the URL.
 */
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

firebase.initializeApp(@json($firebaseWebConfig));

var messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    var title = (payload.notification && payload.notification.title) || 'Slimme-PC';
    var body = (payload.notification && payload.notification.body) || '';
    var url = (payload.data && payload.data.url) || '/admin';

    self.registration.showNotification(title, {
        body: body,
        icon: '/assets/img/logo.webp',
        badge: '/assets/img/logo.webp',
        data: { url: url },
    });
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var url = (event.notification.data && event.notification.data.url) || '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            for (var i = 0; i < list.length; i++) {
                var c = list[i];
                if (c.url === url && 'focus' in c) return c.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});

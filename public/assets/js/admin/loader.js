(function () {
    'use strict';

    var KEY = 'adminPageNav';

    function getLoader() {
        return document.getElementById('admin-loader');
    }

    function markAndShow() {
        try {
            sessionStorage.setItem(KEY, '1');
        } catch (e) {}
        var loader = getLoader();
        if (loader) loader.classList.add('loader-visible');
    }

    /* Intercept internal link clicks -> show loader + mark navigation */
    document.addEventListener('click', function (e) {
        var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
        if (!a) return;
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        var href = a.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
        if (a.target && a.target !== '' && a.target !== '_self') return;
        if (a.hasAttribute('download')) return;
        if (a.origin && a.origin !== window.location.origin) return;

        markAndShow();
    }, true);

    /* Intercept native form submissions (e.g. logout) */
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        var form = e.target;
        if (!form || !form.method || !form.getAttribute('action')) return;
        var method = form.method.toLowerCase();
        if (method !== 'post' && method !== 'get') return;
        markAndShow();
    });
})();
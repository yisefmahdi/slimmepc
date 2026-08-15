(function () {
    'use strict';

    var pickerNames = null;

    function toPascal(name) {
        return name.replace(/(^\w|-\w)/g, function (m) {
            return m.replace(/-/, '').toUpperCase();
        });
    }

    function toKebab(name) {
        return name
            .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
            .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
            .replace(/([a-z])([0-9]+)/g, '$1-$2')
            .toLowerCase();
    }

    function getIconNames() {
        if (pickerNames) return pickerNames;
        if (!window.lucide || !window.lucide.icons) return [];
        pickerNames = Object.keys(window.lucide.icons).sort().map(function (pascal) {
            var kebab = toKebab(pascal);
            return window.lucide.icons[toPascal(kebab)] ? kebab : pascal;
        });
        return pickerNames;
    }

    function buildSvg(ns, spec) {
        var el = document.createElementNS(ns, spec[0]);
        var attrs = spec[1] || {};
        Object.keys(attrs).forEach(function (k) {
            el.setAttribute(k, attrs[k]);
        });
        (spec[2] || []).forEach(function (child) {
            el.appendChild(buildSvg(ns, child));
        });
        return el;
    }

    function renderIcon(element, name) {
        if (!window.lucide || !window.lucide.icons) return;
        var icon = window.lucide.icons[toPascal(name)] || window.lucide.icons.Circle;
        if (!icon) return;

        var ns = 'http://www.w3.org/2000/svg';
        var svg = document.createElementNS(ns, icon[0] || 'svg');
        svg.setAttribute('xmlns', ns);
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '2');
        svg.setAttribute('stroke-linecap', 'round');
        svg.setAttribute('stroke-linejoin', 'round');

        var cls = element.getAttribute('class');
        if (cls) svg.setAttribute('class', cls);

        var attrs = icon[1] || {};
        Object.keys(attrs).forEach(function (k) {
            if (k !== 'xmlns') svg.setAttribute(k, attrs[k]);
        });

        (icon[2] || []).forEach(function (child) {
            svg.appendChild(buildSvg(ns, child));
        });

        element.replaceWith(svg);
        return svg;
    }

    function buildGrid(grid) {
        if (grid.dataset.built) return;
        var names = getIconNames();
        var fragment = document.createDocumentFragment();

        names.forEach(function (name) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'icon-picker-option';
            btn.setAttribute('data-icon-option', name);

            var icon = document.createElement('i');
            icon.setAttribute('data-lucide', name);
            btn.appendChild(icon);

            var span = document.createElement('span');
            span.textContent = name;
            btn.appendChild(span);

            fragment.appendChild(btn);
        });

        grid.textContent = '';
        grid.appendChild(fragment);
        grid.querySelectorAll('[data-lucide]').forEach(function (el) {
            renderIcon(el, el.getAttribute('data-lucide'));
        });
        grid.dataset.built = '1';
    }

    function filterGrid(picker, term) {
        var grid = picker.querySelector('[data-icon-grid]');
        if (!grid) return;
        buildGrid(grid);
        var q = (term || '').trim().toLowerCase();
        grid.querySelectorAll('[data-icon-option]').forEach(function (btn) {
            var name = btn.getAttribute('data-icon-option');
            btn.style.display = (q && name.toLowerCase().indexOf(q) === -1) ? 'none' : '';
        });
    }

    function toggleDropdown(picker, open) {
        var dropdown = picker.querySelector('.icon-picker-dropdown');
        if (!dropdown) return;

        if (open) {
            var search = picker.querySelector('.icon-picker-search');
            var grid = picker.querySelector('[data-icon-grid]');
            if (grid) buildGrid(grid);
            if (search) {
                search.value = '';
                filterGrid(picker, '');
            }

            var trigger = picker.querySelector('.icon-picker-trigger');
            var rect = trigger.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            picker.classList.toggle('icon-picker-dropdown-up', spaceBelow < 340);

            dropdown.hidden = false;
            if (search) search.focus();
        } else {
            dropdown.hidden = true;
        }
    }

    function setValue(picker, name) {
        var hidden = picker.querySelector('input[type="hidden"]');
        if (hidden) hidden.value = name;

        var nameEl = picker.querySelector('.icon-picker-name');
        if (nameEl) nameEl.textContent = name;

        var preview = picker.querySelector('.icon-picker-trigger .icon-picker-preview');
        if (preview) renderIcon(preview, name);
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.icon-picker-trigger');
        var option = e.target.closest('[data-icon-option]');

        if (trigger) {
            var picker = trigger.closest('[data-icon-picker]');
            var dropdown = picker.querySelector('.icon-picker-dropdown');
            if (dropdown && dropdown.hidden) {
                document.querySelectorAll('.icon-picker-dropdown').forEach(function (d) { d.hidden = true; });
                toggleDropdown(picker, true);
            } else if (dropdown) {
                toggleDropdown(picker, false);
            }
            return;
        }

        if (option) {
            var picker = option.closest('[data-icon-picker]');
            setValue(picker, option.getAttribute('data-icon-option'));
            toggleDropdown(picker, false);
            return;
        }

        if (!e.target.closest('[data-icon-picker]')) {
            document.querySelectorAll('.icon-picker-dropdown').forEach(function (d) { d.hidden = true; });
        }
    });

    document.addEventListener('input', function (e) {
        if (!e.target.classList || !e.target.classList.contains('icon-picker-search')) return;
        var picker = e.target.closest('[data-icon-picker]');
        if (picker) filterGrid(picker, e.target.value);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.icon-picker-dropdown').forEach(function (d) { d.hidden = true; });
        }
    });

    function init() {
        document.querySelectorAll('.icon-picker-trigger [data-lucide]').forEach(function (el) {
            renderIcon(el, el.getAttribute('data-lucide'));
        });

        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (!node.querySelectorAll && !node.matches) return;
                    var nodes = node.matches && node.matches('[data-lucide]') ? [node] : node.querySelectorAll('[data-lucide]');
                    nodes.forEach(function (el) {
                        if (el.closest && el.closest('.icon-picker-trigger')) {
                            renderIcon(el, el.getAttribute('data-lucide'));
                        }
                    });
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
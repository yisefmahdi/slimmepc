/**
 * SlimmePC Cart - reusable optimistic add-to-cart
 * Single code used everywhere via [data-cart-add]
 */
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function getQty(btn) {
        const source = btn.getAttribute('data-quantity-source');
        if (source === 'qtyInput') {
            const el = document.getElementById('quantity');
            if (el) {
                const n = parseInt(el.textContent || el.value || '1', 10);
                return isNaN(n) || n < 1 ? 1 : n;
            }
        }
        const q = parseInt(btn.getAttribute('data-quantity') || '1', 10);
        return isNaN(q) || q < 1 ? 1 : q;
    }

    function formatMoney(v) {
        return new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v);
    }

    function updateHeaderBadge(count) {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = count;
        });
        const badge = document.getElementById('cartCountBadge');
        if (badge) badge.textContent = count;
        document.querySelectorAll('.cart-badge').forEach(el => el.textContent = count);
    }

    function toast(msg, type) {
        if (window.SlimmePC && window.SlimmePC.toast) {
            if (type === 'error' && window.SlimmePC.toast.error) { window.SlimmePC.toast.error(msg); return; }
            if (window.SlimmePC.toast.success) { window.SlimmePC.toast.success(msg); return; }
            // fallback if toast is function
            try { window.SlimmePC.toast(msg, type || 'success'); return; } catch(e) {}
        }
        // fallback
        let t = document.getElementById('cartToast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'cartToast';
            t.className = 'fixed bottom-5 right-5 z-[80] rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition';
            t.style.display = 'none';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.className = 'fixed bottom-5 right-5 z-[80] max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg ' + (type === 'error' ? 'bg-red-600 text-white' : 'bg-emerald-600 text-white');
        t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 3500);
    }

    async function addToCart(productId, qty, btn) {
        // Optimistic UI
        const originalQty = qty;
        let badgeEls = document.querySelectorAll('[data-cart-count]');
        let prevCounts = Array.from(badgeEls).map(e => parseInt(e.textContent || '0', 10));

        // Immediately bump header
        if (badgeEls.length) {
            badgeEls.forEach(el => {
                const cur = parseInt(el.textContent || '0', 10);
                el.textContent = cur + qty;
            });
        }

        // Button feedback
        let origHTML = '';
        let isMainBtn = btn.classList.contains('cart-add-main');
        if (btn) {
            origHTML = btn.innerHTML;
            btn.disabled = true;
            if (isMainBtn) {
                const text = btn.querySelector('.cart-text');
                if (text) text.textContent = 'Toegevoegd ✓';
                else btn.innerHTML = '<i class="fa-solid fa-check"></i> Toegevoegd';
            } else {
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                if (window.lucide) lucide.createIcons();
            }
        }

        try {
            const res = await fetch('/cart/items', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ product_id: productId, quantity: qty }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Fout bij toevoegen.');

            updateHeaderBadge(data.count);
            toast(data.message || 'Toegevoegd aan winkelwagen.');
            // Notify cart page for dynamic injection (no refresh)
            document.dispatchEvent(new CustomEvent('cart:itemAdded', { detail: data }));

            if (window.lucide) lucide.createIcons();

            setTimeout(() => {
                if (btn) {
                    btn.innerHTML = origHTML;
                    btn.disabled = false;
                    if (window.lucide) lucide.createIcons();
                }
            }, 1200);

        } catch (err) {
            // Rollback header
            badgeEls.forEach((el, i) => el.textContent = prevCounts[i] || 0);
            // rollback cartCountBadge if exists
            const badge = document.getElementById('cartCountBadge');
            if (badge) {
                const cur = parseInt(badge.textContent || '0', 10);
                badge.textContent = Math.max(0, cur - qty);
            }
            toast(err.message || 'Er ging iets mis.', 'error');
            if (btn) {
                btn.innerHTML = origHTML;
                btn.disabled = false;
                if (window.lucide) lucide.createIcons();
            }
        }
    }

    // Delegated click
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-cart-add]');
        if (!btn) return;
        if (btn.disabled) return;
        const pid = btn.getAttribute('data-product-id');
        if (!pid) return;
        e.preventDefault();
        const qty = getQty(btn);
        addToCart(pid, qty, btn);
    });

    // Expose
    window.SlimmeCart = {
        add: addToCart,
        updateHeaderBadge,
        formatMoney,
        getCount: async function () {
            try {
                const r = await fetch('/cart/count', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
                const d = await r.json();
                updateHeaderBadge(d.count);
                return d;
            } catch (e) { return null; }
        }
    };
})();

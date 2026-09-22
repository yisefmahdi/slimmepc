<x-admin.layout title="Prijs-instelling lidmaatschap">
    <div class="mx-auto w-full max-w-2xl">
        <div class="mb-4">
            <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Prijs-instelling</h2>
            <p class="mt-0.5 text-xs" style="color: var(--c-muted)">De jaarprijs van het lidmaatschap (incl. btw). Deze prijs wordt getoond op de lid-worden pagina.</p>
        </div>

        <form id="lidPriceForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                Lidmaatschapsprijs per jaar (€, incl. btw) *
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">€</span>
                <input type="number" id="lidPrice" name="subscription_price" min="1" max="9999" step="0.01" required
                       value="{{ number_format((float) $price, 2, '.', '') }}"
                       class="form-input h-12 w-full pl-9 text-base font-bold">
            </div>
            <p id="lidPriceError" class="mt-1 hidden text-xs font-semibold text-red-600"></p>

            <button type="submit" id="lidPriceBtn"
                    class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 disabled:opacity-60">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <span>Prijs opslaan</span>
            </button>
        </form>
    </div>

    <script>
        (function () {
            const form = document.getElementById('lidPriceForm');
            if (!form) return;
            const input = document.getElementById('lidPrice');
            const err = document.getElementById('lidPriceError');
            const btn = document.getElementById('lidPriceBtn');
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            function toast(msg, type) {
                if (window.SlimmePC && window.SlimmePC.toast) {
                    if (type === 'success') window.SlimmePC.toast.success(msg);
                    else window.SlimmePC.toast.error(msg);
                }
            }
            const SPINNER = '<svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
            function btnLoading(on) {
                if (on) {
                    if (btn.dataset.origHtml === undefined) btn.dataset.origHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = SPINNER + '<span>Opslaan…</span>';
                } else {
                    if (btn.dataset.origHtml !== undefined) btn.innerHTML = btn.dataset.origHtml;
                    btn.disabled = false;
                }
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                err.classList.add('hidden');
                btnLoading(true);
                try {
                    const res = await fetch('{{ route('admin.lidmaatschap.price') }}', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ subscription_price: input.value }),
                    });
                    const d = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        const msg = (d.errors && d.errors.subscription_price && d.errors.subscription_price[0]) || d.message || 'Opslaan mislukt.';
                        err.textContent = msg;
                        err.classList.remove('hidden');
                        throw new Error(msg);
                    }
                    toast(d.message || 'Opgeslagen.', 'success');
                } catch (e2) {
                    if (err.classList.contains('hidden')) toast(e2.message || 'Opslaan mislukt.', 'error');
                } finally {
                    btnLoading(false);
                }
            });
        })();
    </script>
</x-admin.layout>

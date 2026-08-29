<x-admin.layout title="Nieuwe factuur — Hardware">
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold tracking-tight" style="color: var(--c-heading)">Handmatig Factuur Aanmaken</h2>
                <p class="mt-1 text-sm" style="color: var(--c-muted)">Vul de gegevens in. De factuur wordt als PDF gegenereerd en per e-mail verzonden.</p>
            </div>
            <a href="{{ route('admin.bevestiging-mail.hardware.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800" style="color: var(--c-heading); border-color: var(--c-input-border)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Overzicht
            </a>
        </div>

        <form id="hardwareInvoiceForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Naam klant *
                    </label>
                    <input type="text" name="name" required placeholder="Bijv. Yousef Ziad Mahdi"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        E-mailadres *
                    </label>
                    <input type="email" name="email" required placeholder="klant@voorbeeld.nl"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25M12 12a2.25 2.25 0 00-2.25 2.25v2.625a2.25 2.25 0 002.25 2.25h2.25A2.25 2.25 0 0016.5 16.875V14.25A2.25 2.25 0 0014.25 12H12z" /></svg>
                        Apparaat info
                    </label>
                    <input type="text" name="device_info" placeholder="Bijv. Gaming-pc, HP Laptop"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" /></svg>
                        Probleembeschrijving
                    </label>
                    <textarea name="description" rows="4" placeholder="Omschrijving van werkzaamheden..."
                              class="form-input w-full py-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" /></svg>
                        Subtotaal (€) *
                    </label>
                    <input type="number" name="subtotal" id="subtotal" required min="0" step="0.01" placeholder="Bijv. 330.58"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m6-3a9 9 0 11-9 9 9 9 0 019-9z" /></svg>
                        BTW (%) *
                    </label>
                    <input type="number" name="tax_percentage" id="tax_percentage" required min="0" max="100" step="1" value="21"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414-.336.75-.75.75h-.75m-18 0v9m18-10.5v6m-18 0v9m18 0v.75c0 .414-.336.75-.75.75h-.75m-18 0v9m18 0v.75c0 .414-.336.75-.75.75H5.25m13.5 0v-9" /></svg>
                        Totaal (€) *
                    </label>
                    <input type="number" name="total" id="total" required min="0" step="0.01" placeholder="Bijv. 400.00"
                           class="form-input h-11 w-full text-sm font-bold">
                    <p class="mt-1 text-xs" style="color: var(--c-muted)">Inclusief BTW — kan handmatig worden aangepast</p>
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
            </div>

            <button type="submit" id="hardwareSubmitBtn"
                    class="mt-8 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60">
                <svg id="hardwareSubmitSpinner" class="hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span id="hardwareSubmitLabel">Factuur aanmaken &amp; verzenden</span>
            </button>
            <div id="hardwareFormMsg" class="mt-4 hidden rounded-xl border px-4 py-3 text-sm font-bold"></div>
        </form>
    </div>

    <script>
        (function(){
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax_percentage');
            const totalEl = document.getElementById('total');
            let totalManuallyEdited = false;
            if (totalEl) {
                totalEl.addEventListener('input', () => { totalManuallyEdited = true; });
            }
            function recalc(){
                // Inclusief: Totaal wordt niet automatisch berekend (handmatig aanpasbaar)
                // Als Totaal nog leeg is en niet handmatig aangepast, vul met Subtotaal als suggestie
                const sub = parseFloat(subtotalEl.value) || 0;
                if (totalEl && !totalManuallyEdited) {
                    if (!totalEl.value || totalEl.value === '') {
                        totalEl.value = sub ? sub.toFixed(2) : '';
                    }
                }
            }
            if (subtotalEl) subtotalEl.addEventListener('input', recalc);
            if (taxEl) taxEl.addEventListener('input', recalc);

            const form = document.getElementById('hardwareInvoiceForm');
            const btn = document.getElementById('hardwareSubmitBtn');
            const spinner = document.getElementById('hardwareSubmitSpinner');
            const label = document.getElementById('hardwareSubmitLabel');
            const msg = document.getElementById('hardwareFormMsg');

            form.addEventListener('submit', async (e)=>{
                e.preventDefault();
                if(!form.reportValidity()) return;
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Bezig met verzenden…';
                msg.className = 'mt-3 hidden text-sm font-semibold';
                msg.textContent = '';
                // clear previous errors
                form.querySelectorAll('.field-error').forEach(el=>{ el.textContent=''; el.classList.add('hidden'); });
                form.querySelectorAll('.form-input').forEach(el=> el.classList.remove('!border-red-500'));

                const fd = new FormData(form);
                const payload = Object.fromEntries(fd.entries());

                try{
                    const res = await fetch('{{ route('admin.bevestiging-mail.hardware.store') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || document.querySelector('input[name=_token]')?.value || '',
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json().catch(()=>({}));
                    if(res.ok && (data.invoice || data.message)){
                        msg.textContent = '✓ ' + (data.message || 'Factuur succesvol aangemaakt en verzonden!');
                        msg.className = 'mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700';
                        msg.classList.remove('hidden');
                        form.reset();
                        taxEl.value = '21';
                        recalc();
                        // keep message visible, redirect after 1.8s so user can read it
                        setTimeout(()=>{ window.location.href = '{{ route('admin.bevestiging-mail.hardware.index') }}'; }, 1800);
                        return;
                    }
                    if(res.status===422 && data.errors){
                        Object.entries(data.errors).forEach(([field, msgs])=>{
                            const input = form.querySelector(`[name="${field}"]`);
                            if(input){
                                input.classList.add('!border-red-500');
                                const errEl = input.parentElement.querySelector('.field-error');
                                if(errEl){ errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
                            }
                        });
                        const firstErr = Object.values(data.errors)[0]?.[0] || 'Controleer de velden.';
                        msg.textContent = '✗ ' + firstErr;
                        msg.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700';
                        msg.classList.remove('hidden');
                        return;
                    }
                    throw new Error(data.message || 'Er ging iets mis.');
                }catch(err){
                    msg.textContent = '✗ ' + (err.message || 'Er ging iets mis.');
                    msg.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700';
                    msg.classList.remove('hidden');
                }finally{
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    label.textContent = 'Factuur aanmaken & verzenden';
                }
            });
        })();
    </script>
</x-admin.layout>

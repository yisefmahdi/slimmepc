<x-admin.layout title="RekenMachine">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">RekenMachine</h1>
            <p class="text-xs" style="color: var(--c-muted)">BTW berekenen: verwijderen (incl. naar excl.) of toevoegen (excl. naar incl.).</p>
        </div>
        <x-admin.back-button :href="route('admin.purchase-sales.index')" label="Kopen+verkopen" />
    </div>

    <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">BTW-rekenmachine (standaard 21%)</h3>
        </div>
        <div class="p-4 sm:p-6">
            <div class="mb-5 flex gap-2 rounded-xl p-1.5" role="tablist" aria-label="BTW-rekenmachine modi" style="background-color: var(--c-page)">
                <button type="button" id="tab-incl" role="tab" aria-selected="true" class="h-10 flex-1 rounded-lg bg-blue-600 px-3 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)]">Btw verwijderen (Incl. naar Excl.)</button>
                <button type="button" id="tab-excl" role="tab" aria-selected="false" class="h-10 flex-1 rounded-lg px-3 text-sm font-bold transition hover:bg-slate-200 dark:hover:bg-slate-800" style="color: var(--c-heading)">Btw toevoegen (Excl. naar Incl.)</button>
            </div>

            <div class="mb-5 max-w-[220px]">
                <x-input-label for="rate">Btw-percentage %</x-input-label>
                <x-text-input id="rate" type="number" value="21" min="0" step="0.01" />
            </div>

            {{-- Modus 1: Incl -> Excl --}}
            <div id="panel-incl" role="tabpanel" aria-labelledby="tab-incl">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="incl">Bedrag inclusief btw (incl.)</x-input-label>
                        <x-text-input id="incl" type="text" inputmode="decimal" placeholder="Bijv. 1.131 of 1.131,50" />
                        <p class="mt-1 text-xs" style="color: var(--c-muted)">Voer het bedrag inclusief btw in. Exclusief en btw worden automatisch berekend.</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button type="button" id="calc-incl" class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">Bereken</button>
                    <button type="button" id="clear-incl" class="inline-flex h-10 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Wissen</button>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="excl">Bedrag exclusief btw (excl.)</x-input-label>
                        <x-text-input id="excl" type="text" readonly class="border-dashed" />
                    </div>
                    <div>
                        <x-input-label for="vat">Btw-bedrag (btw)</x-input-label>
                        <x-text-input id="vat" type="text" readonly class="border-dashed" />
                    </div>
                </div>
            </div>

            {{-- Modus 2: Excl -> Incl --}}
            <div id="panel-excl" role="tabpanel" aria-labelledby="tab-excl" hidden>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="exclIn">Bedrag exclusief btw (excl.)</x-input-label>
                        <x-text-input id="exclIn" type="text" inputmode="decimal" placeholder="Bijv. 934,71" />
                        <p class="mt-1 text-xs" style="color: var(--c-muted)">Voer het bedrag exclusief btw in. Inclusief en btw worden automatisch berekend.</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button type="button" id="calc-excl" class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">Bereken</button>
                    <button type="button" id="clear-excl" class="inline-flex h-10 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Wissen</button>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="inclOut">Bedrag inclusief btw (incl.)</x-input-label>
                        <x-text-input id="inclOut" type="text" readonly class="border-dashed" />
                    </div>
                    <div>
                        <x-input-label for="vat2">Btw-bedrag (btw)</x-input-label>
                        <x-text-input id="vat2" type="text" readonly class="border-dashed" />
                    </div>
                </div>
            </div>

            <p class="mt-5 text-xs leading-6" style="color: var(--c-muted)">Formules: verwijderen <code>excl. = incl. / (1 + percentage/100)</code> · toevoegen <code>incl. = excl. x (1 + percentage/100)</code> · Notatie: nl-NL (2 decimalen). Invoer herkent automatisch punt, komma en duizendtallen.</p>
        </div>
    </div>

    <script>
        const fmt = new Intl.NumberFormat('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        function parseFlexibleNumber(str) {
            if (typeof str !== 'string') return NaN;
            let s = str.trim();
            if (!s) return NaN;
            s = s.replace(/\s+/g, '');
            const hasComma = s.includes(','), hasDot = s.includes('.');
            if (hasComma && hasDot) {
                if (s.lastIndexOf(',') > s.lastIndexOf('.')) { s = s.replace(/\./g, '').replace(',', '.'); }
                else { s = s.replace(/,/g, ''); }
            } else if (hasComma && !hasDot) { s = s.replace(',', '.'); }
            return Number(s);
        }
        function toastWarn(msg) {
            if (window.SlimmePC && window.SlimmePC.toast) window.SlimmePC.toast.error(msg);
            else alert(msg);
        }
        function calcInclToExcl() {
            const incl = parseFlexibleNumber(document.getElementById('incl').value);
            const rate = parseFlexibleNumber(document.getElementById('rate').value);
            const exclEl = document.getElementById('excl'), vatEl = document.getElementById('vat');
            if (!isFinite(incl) || incl < 0) { exclEl.value = ''; vatEl.value = ''; toastWarn('Voer een geldig bedrag inclusief btw in.'); return; }
            if (!isFinite(rate) || rate < 0) { exclEl.value = ''; vatEl.value = ''; toastWarn('Voer een geldig btw-percentage in.'); return; }
            const excl = incl / (1 + rate / 100);
            exclEl.value = fmt.format(excl);
            vatEl.value = fmt.format(incl - excl);
        }
        function calcExclToIncl() {
            const excl = parseFlexibleNumber(document.getElementById('exclIn').value);
            const rate = parseFlexibleNumber(document.getElementById('rate').value);
            const inclOutEl = document.getElementById('inclOut'), vatEl = document.getElementById('vat2');
            if (!isFinite(excl) || excl < 0) { inclOutEl.value = ''; vatEl.value = ''; toastWarn('Voer een geldig bedrag exclusief btw in.'); return; }
            if (!isFinite(rate) || rate < 0) { inclOutEl.value = ''; vatEl.value = ''; toastWarn('Voer een geldig btw-percentage in.'); return; }
            const incl = excl * (1 + rate / 100);
            inclOutEl.value = fmt.format(incl);
            vatEl.value = fmt.format(incl - excl);
        }
        const tabIncl = document.getElementById('tab-incl'), tabExcl = document.getElementById('tab-excl');
        const panelIncl = document.getElementById('panel-incl'), panelExcl = document.getElementById('panel-excl');
        const tabActive = 'h-10 flex-1 rounded-lg bg-blue-600 px-3 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)]';
        const tabIdle = 'h-10 flex-1 rounded-lg px-3 text-sm font-bold transition hover:bg-slate-200 dark:hover:bg-slate-800';
        function activate(tab) {
            const isIncl = tab === 'incl';
            tabIncl.className = isIncl ? tabActive : tabIdle;
            tabExcl.className = !isIncl ? tabActive : tabIdle;
            tabIncl.style.color = isIncl ? '' : 'var(--c-heading)';
            tabExcl.style.color = !isIncl ? '' : 'var(--c-heading)';
            tabIncl.setAttribute('aria-selected', String(isIncl));
            tabExcl.setAttribute('aria-selected', String(!isIncl));
            panelIncl.hidden = !isIncl;
            panelExcl.hidden = isIncl;
        }
        tabIncl.addEventListener('click', () => activate('incl'));
        tabExcl.addEventListener('click', () => activate('excl'));
        document.getElementById('calc-incl').addEventListener('click', calcInclToExcl);
        document.getElementById('calc-excl').addEventListener('click', calcExclToIncl);
        document.getElementById('clear-incl').addEventListener('click', () => { document.getElementById('incl').value = ''; document.getElementById('excl').value = ''; document.getElementById('vat').value = ''; document.getElementById('incl').focus(); });
        document.getElementById('clear-excl').addEventListener('click', () => { document.getElementById('exclIn').value = ''; document.getElementById('inclOut').value = ''; document.getElementById('vat2').value = ''; document.getElementById('exclIn').focus(); });
        document.getElementById('incl').addEventListener('input', () => { if (document.getElementById('incl').value.trim()) calcInclToExcl(); });
        document.getElementById('exclIn').addEventListener('input', () => { if (document.getElementById('exclIn').value.trim()) calcExclToIncl(); });
        document.getElementById('rate').addEventListener('input', () => {
            if (!panelIncl.hidden) { if (document.getElementById('incl').value.trim()) calcInclToExcl(); }
            else { if (document.getElementById('exclIn').value.trim()) calcExclToIncl(); }
        });
        document.getElementById('incl').addEventListener('keydown', e => { if (e.key === 'Enter') calcInclToExcl(); });
        document.getElementById('exclIn').addEventListener('keydown', e => { if (e.key === 'Enter') calcExclToIncl(); });
    </script>
</x-admin.layout>

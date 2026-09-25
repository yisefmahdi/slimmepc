<x-admin.layout title="E-mails verzenden">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">E-mails verzenden</h1>
            <p class="text-xs" style="color: var(--c-muted)">Selecteer ontvangers uit de mailinglijst. Verzending loopt via de wachtrij.</p>
        </div>
        <x-admin.back-button :href="route('admin.customerlist.index')" label="Klantenlijst" />
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.mailinglist.send') }}" method="POST" class="space-y-6">
        @csrf

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Ontvangers</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="relative mb-3">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </span>
                    <input type="text" id="recipientSearch" placeholder="Zoek ontvangers..." class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                </div>
                <div class="mb-2 flex items-center gap-3">
                    <button type="button" id="selectAllRecipients" class="text-xs font-bold text-blue-600 hover:text-blue-800">Alles selecteren</button>
                    <span class="text-xs" style="color: var(--c-muted)">·</span>
                    <button type="button" id="deselectAllRecipients" class="text-xs font-bold text-blue-600 hover:text-blue-800">Wissen</button>
                    <span id="recipientCount" class="ml-auto text-xs font-semibold" style="color: var(--c-muted)">0 geselecteerd</span>
                </div>
                <div id="recipientList" class="max-h-60 space-y-1 overflow-y-auto rounded-xl border p-2" style="border-color: rgba(148,163,184,.25)">
                    @forelse($emails as $email)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-blue-50/60 dark:hover:bg-slate-800/60">
                            <input type="checkbox" name="recipients[]" value="{{ $email->email }}" class="recipient-check h-4 w-4 shrink-0 rounded text-blue-600">
                            <span class="min-w-0 text-sm" style="color: var(--c-heading)"><span class="font-semibold">{{ $email->email }}</span>@if($email->name)<span style="color: var(--c-muted)"> ({{ $email->name }})</span>@endif</span>
                        </label>
                    @empty
                        <p class="px-3 py-6 text-center text-sm" style="color: var(--c-muted)">Geen adressen in de lijst.</p>
                    @endforelse
                </div>
                @error('recipients')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Bericht</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="ml-type">Type bericht</x-input-label>
                        <select name="type" id="ml-type" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="">-- Selecteer --</option>
                            <option value="Algemeen">Algemeen</option>
                            <option value="Herinnering">Herinnering</option>
                            <option value="special_offer">Speciale aanbieding</option>
                            <option value="marketing">Marketingbericht</option>
                            <option value="maintenance">Onderhoudsbericht</option>
                            <option value="important_notice">Belangrijke mededeling</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ml-subject">Onderwerp <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="ml-subject" name="subject" value="{{ old('subject') }}" placeholder="Onderwerp van de e-mail" />
                        @error('subject')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="ml-message">Bericht <span class="text-red-500">*</span></x-input-label>
                        <textarea name="message" id="ml-message" rows="6" required placeholder="Typ hier het bericht..." class="form-input w-full text-sm">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.customerlist.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border px-6 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</a>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-8 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] hover:-translate-y-0.5">Verzenden</button>
        </div>
    </form>

    <script>
        (function () {
            const boxes = () => Array.from(document.querySelectorAll('.recipient-check'));
            const countEl = document.getElementById('recipientCount');
            const updateCount = () => {
                const n = boxes().filter(b => b.checked).length;
                if (countEl) countEl.textContent = n + ' geselecteerd';
            };
            boxes().forEach(b => b.addEventListener('change', updateCount));
            document.getElementById('selectAllRecipients')?.addEventListener('click', () => {
                boxes().forEach(b => { if (b.offsetParent !== null) b.checked = true; });
                updateCount();
            });
            document.getElementById('deselectAllRecipients')?.addEventListener('click', () => {
                boxes().forEach(b => b.checked = false);
                updateCount();
            });
            document.getElementById('recipientSearch')?.addEventListener('keyup', function () {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll('#recipientList label').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
                });
            });
            updateCount();
        })();
    </script>
</x-admin.layout>

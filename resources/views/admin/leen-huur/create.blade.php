<x-admin.layout title="{{ $loan ? 'Uitgifte bewerken — ' . $loan->loanNumber() : 'Laptop uitgeven' }}">
    @php $isEdit = (bool) $loan; @endphp
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold tracking-tight" style="color: var(--c-heading)">{{ $isEdit ? 'Uitgifte bewerken — ' . $loan->loanNumber() : 'Laptop uitgeven' }}</h2>
                <p class="mt-1 text-sm" style="color: var(--c-muted)">{{ $isEdit ? 'Pas de gegevens aan. Er wordt geen nieuwe e-mail verzonden.' : 'Vul de gegevens in. Er wordt een bevestigingsmail met overeenkomst naar de klant verzonden.' }}</p>
            </div>
            <a href="{{ route('admin.leen-huur.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800" style="color: var(--c-heading); border-color: var(--c-input-border)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Overzicht
            </a>
        </div>

        <form id="leenForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            @csrf
            @if($isEdit)
                <input type="hidden" name="_method" value="PUT">
            @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Naam klant *
                    </label>
                    <input type="text" name="customer_name" required placeholder="Bijv. Jan Jansen" value="{{ old('customer_name', $loan->customer_name ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        Adres *
                    </label>
                    <input type="text" name="address" required placeholder="Straat + huisnummer" value="{{ old('address', $loan->address ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">Postcode *</label>
                    <input type="text" name="postcode" required placeholder="7311EL" value="{{ old('postcode', $loan->postcode ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">Plaats *</label>
                    <input type="text" name="city" required placeholder="Apeldoorn" value="{{ old('city', $loan->city ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        Telefoonnummer *
                    </label>
                    <input type="text" name="phone" required placeholder="Bijv. 0612345678" value="{{ old('phone', $loan->phone ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        E-mailadres *
                    </label>
                    <input type="email" name="customer_email" required placeholder="klant@voorbeeld.nl" value="{{ old('customer_email', $loan->customer_email ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" /></svg>
                        Reparatienummer
                    </label>
                    <input type="text" id="leenRepairNumber" name="repair_number" placeholder="Bijv. SP-2026-00001 (optioneel)" value="{{ old('repair_number', $loan->repair_number ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <div class="mt-2 flex items-center gap-2">
                        <button type="button" id="leenRepairSearchBtn"
                                class="inline-flex h-9 items-center gap-2 rounded-xl border border-blue-200 bg-blue-50/60 px-3.5 text-xs font-bold text-blue-700 transition hover:border-blue-500 hover:bg-blue-50 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                            Reparatie zoeken
                        </button>
                        <span id="leenRepairPicked" class="hidden text-xs font-bold text-emerald-600 dark:text-emerald-400">✓ Gekoppeld</span>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879.879l-4.5 4.5a3 3 0 01-.879.879V12a3 3 0 013-3h6a3 3 0 013 3v1.757a3 3 0 01-.879.879l-4.5 4.5a3 3 0 01-.879-.879V17.25M9 17.25a3 3 0 003 3h0a3 3 0 003-3M9 17.25h6" /></svg>
                        Type laptop *
                    </label>
                    <input type="text" name="laptop_type" required placeholder="Bijv. Dell Latitude 5420" value="{{ old('laptop_type', $loan->laptop_type ?? '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        Datum &amp; tijd van uitgifte *
                    </label>
                    <input type="datetime-local" name="given_at" required value="{{ old('given_at', isset($loan) && $loan ? $loan->given_at->format('Y-m-d\TH:i') : '') }}"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>

                @if($isEdit && $loan->photos->count())
                <div class="sm:col-span-2">
                    <label class="mb-1.5 text-sm font-semibold" style="color: var(--c-heading)">Bestaande foto's ({{ $loan->photos->count() }})</label>
                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        @foreach($loan->photos as $photo)
                        <div class="group relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900" data-existing-photo="{{ $photo->id }}">
                            <img src="{{ route('admin.leen-huur.photo', ['loan' => $loan->id, 'photo' => $photo->id]) }}" alt="Foto" class="h-full w-full object-cover" loading="lazy">
                            <button type="button" data-delete-photo="{{ $photo->id }}" aria-label="Verwijderen" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-lg bg-black/60 text-white opacity-0 transition group-hover:opacity-100 hover:bg-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between">
                        <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            Foto's van de laptop
                        </label>
                        <span id="leenPhotoCounter" class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">0 / 20</span>
                    </div>
                    <input type="file" id="leenPhotos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">
                    <input type="file" id="leenPhotosCamera" accept="image/*" capture="environment" class="hidden">
                    <div id="leenDropzone"
                         class="group relative flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-blue-200/80 bg-blue-50/30 p-5 text-center transition hover:border-blue-500 hover:bg-blue-50/70 dark:border-blue-900/40 dark:bg-slate-900/20 dark:hover:border-blue-500">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm transition group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white dark:bg-slate-800 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                Sleep foto's hierheen of <span class="text-blue-600 hover:underline">klik om te selecteren</span>
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">Optioneel · Kies uit bestanden of maak direct een foto met de camera · Maximaal 20 foto's (PNG, JPG, WEBP, AVIF · 10MB per foto)</p>
                        </div>
                    </div>
                    <div id="leenPhotosPreview" class="mt-3 flex flex-wrap items-center gap-2.5 sm:gap-3"></div>
                    <p id="leenPhotosFeedback" class="hidden mt-1 text-xs font-semibold text-amber-600 dark:text-amber-400"></p>
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600" data-error-for="photos"></p>
                </div>
            </div>

            <button type="submit" id="leenSubmitBtn"
                    class="mt-8 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60">
                <svg id="leenSubmitSpinner" class="hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span id="leenSubmitLabel">{{ $isEdit ? 'Wijzigingen opslaan' : 'Laptop uitgeven & bevestigen' }}</span>
            </button>
            <div id="leenFormMsg" class="mt-4 hidden rounded-xl border px-4 py-3 text-sm font-bold"></div>
        </form>
    </div>

    {{-- Foto-bron kiezen --}}
    <x-admin.modal id="leenPhotoSource" title="Foto toevoegen" subtitle="Kies uit je bestanden of maak direct een foto." size="sm">
        <div id="leenSourceChoice" class="grid grid-cols-2 gap-3">
            <button type="button" id="leenPickFiles"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-blue-300 bg-blue-50/40 px-4 py-6 text-blue-700 transition hover:border-blue-500 hover:bg-blue-50/80 dark:bg-slate-900/40 dark:text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-9 w-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                </svg>
                <span class="text-sm font-bold">Bestanden</span>
                <span class="text-[11px] font-medium opacity-70">Kies één of meer foto's</span>
            </button>
            <button type="button" id="leenPickCamera"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40 px-4 py-6 text-emerald-700 transition hover:border-emerald-500 hover:bg-emerald-50/80 dark:bg-slate-900/40 dark:text-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-9 w-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                </svg>
                <span class="text-sm font-bold">Camera</span>
                <span class="text-[11px] font-medium opacity-70">Maak direct een foto</span>
            </button>
        </div>

        <div id="leenCameraView" class="hidden flex-col gap-3">
            <div class="relative overflow-hidden rounded-2xl bg-slate-950">
                <video id="leenCameraVideo" autoplay playsinline muted class="aspect-[4/3] w-full object-cover"></video>
                <span class="absolute left-2 top-2 flex items-center gap-1.5 rounded-full bg-black/60 px-2.5 py-1 text-[11px] font-bold text-white">
                    <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-red-500"></span>
                    LIVE
                </span>
            </div>
            <label class="text-xs font-semibold" style="color: var(--c-heading)">
                Camera kiezen
                <select id="leenCameraDevice" class="form-input mt-1 h-10 w-full text-xs"></select>
            </label>
            <p id="leenCameraError" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300"></p>
            <div class="flex gap-2">
                <button type="button" id="leenCaptureBtn"
                        class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white shadow-[0_10px_25px_rgba(5,150,105,.25)] transition hover:bg-emerald-700 disabled:opacity-60">
                    Foto maken
                </button>
                <button type="button" id="leenCameraBackBtn"
                        class="inline-flex h-11 items-center justify-center rounded-xl border px-4 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                        style="color: var(--c-heading); border-color: var(--c-input-border)">
                    Terug
                </button>
            </div>
            <canvas id="leenCameraCanvas" class="hidden"></canvas>
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- Reparatie zoeken (koppel reparatienummer + optioneel klantgegevens) --}}
    <x-admin.modal id="leenRepairSearch" title="Reparatie zoeken" subtitle="Zoek op naam of e-mail van de klant en kies een apparaat." size="md">
        <div class="relative">
            <input type="text" id="leenRepairQuery" placeholder="Bijv. Jan Jansen of jan@voorbeeld.nl…" autocomplete="off"
                   class="form-input h-11 w-full pl-10 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            <svg id="leenRepairSpinner" class="hidden absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
        </div>
        <div id="leenRepairResults" class="mt-3 max-h-[46vh] space-y-2 overflow-y-auto"></div>
        <p id="leenRepairHint" class="mt-3 text-xs" style="color: var(--c-muted)">Typ minimaal 2 tekens om te zoeken in de reparatie-aanmeldingen.</p>
        <p id="leenRepairError" class="hidden mt-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300"></p>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Sluiten
            </button>
        </x-slot>
    </x-admin.modal>

    <script>
        window.LEEN_IS_EDIT = @json($isEdit);
        window.LEEN_LOAN_ID = @json($loan->id ?? null);
        window.LEEN_SUBMIT_URL = @json($isEdit ? route('admin.leen-huur.update', $loan) : route('admin.leen-huur.store'));
        window.LEEN_INDEX_URL = @json(route('admin.leen-huur.index'));
    </script>
    <script>
        (function(){
            const form = document.getElementById('leenForm');
            const btn = document.getElementById('leenSubmitBtn');
            const spinner = document.getElementById('leenSubmitSpinner');
            const label = document.getElementById('leenSubmitLabel');
            const msg = document.getElementById('leenFormMsg');
            const defaultLabel = label.textContent;

            const dt = form.querySelector('input[name=given_at]');
            if (dt && !dt.value) {
                const now = new Date();
                const pad = n => String(n).padStart(2,'0');
                dt.value = now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())+'T'+pad(now.getHours())+':'+pad(now.getMinutes());
            }

            function getToken(){
                const m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.content : (document.querySelector('input[name=_token]')?.value || '');
            }

            // ---- Reparatie zoeken: koppel reparatienummer (+ optioneel klantgegevens) ----
            const repairBtn = document.getElementById('leenRepairSearchBtn');
            const repairQuery = document.getElementById('leenRepairQuery');
            const repairResults = document.getElementById('leenRepairResults');
            const repairSpinner = document.getElementById('leenRepairSpinner');
            const repairHint = document.getElementById('leenRepairHint');
            const repairError = document.getElementById('leenRepairError');
            const repairPicked = document.getElementById('leenRepairPicked');
            const REPAIR_MODAL = 'leenRepairSearch';
            let repairTimer = null;
            let lastRepairQuery = '';

            function openRepairModal(){
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.open(REPAIR_MODAL); }
                else { const m = document.getElementById('modal-' + REPAIR_MODAL); if(m) m.classList.remove('hidden'); }
                setTimeout(() => { if(repairQuery) repairQuery.focus(); }, 150);
            }
            function statusLabel(s){
                if(s === 'completed') return '<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Voltooid</span>';
                if(s === 'in_progress') return '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">In behandeling</span>';
                return '<span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Nieuw</span>';
            }
            function escapeAttr(v){
                return String(v ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;');
            }
            function renderRepairResults(items){
                if(!repairResults) return;
                if(!items.length){
                    repairResults.innerHTML = '<div class="rounded-xl border border-dashed px-4 py-8 text-center" style="border-color: var(--c-input-border)"><p class="text-sm font-bold" style="color:var(--c-heading)">Geen reparaties gevonden</p><p class="mt-1 text-xs" style="color:var(--c-muted)">Probeer een andere naam of e-mail.</p></div>';
                    return;
                }
                repairResults.innerHTML = items.map((r, i) => {
                    const device = [r.brand, r.model].filter(Boolean).join(' ') || (r.device || '—');
                    return '<div class="rounded-xl border p-3 transition hover:border-blue-400 hover:shadow-sm" style="background-color: var(--c-page); border-color: rgba(148,163,184,.25)" data-repair-idx="' + i + '">'
                        + '<div class="flex items-start justify-between gap-2">'
                        + '<div class="min-w-0"><p class="text-sm font-bold text-blue-600">' + escapeAttr(r.repair_number || ('#' + r.id)) + '</p>'
                        + '<p class="truncate text-xs font-semibold" style="color:var(--c-heading)">' + escapeAttr(r.name || '—') + ' <span class="font-normal" style="color:var(--c-muted)">· ' + escapeAttr(r.email || '') + '</span></p>'
                        + '<p class="mt-0.5 truncate text-xs" style="color:var(--c-muted)">' + escapeAttr(device) + '</p></div>'
                        + '<div class="shrink-0">' + statusLabel(r.status) + '</div></div>'
                        + '<div class="mt-2 flex gap-2">'
                        + '<button type="button" data-pick="number" class="inline-flex h-8 flex-1 items-center justify-center rounded-lg bg-blue-600 px-3 text-[11px] font-bold text-white transition hover:bg-blue-700">Nummer</button>'
                        + '<button type="button" data-pick="full" class="inline-flex h-8 flex-1 items-center justify-center rounded-lg border border-blue-200 bg-blue-50/60 px-3 text-[11px] font-bold text-blue-700 transition hover:border-blue-500 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300">Nummer + klant</button>'
                        + '</div></div>';
                }).join('');
                repairResults.querySelectorAll('[data-repair-idx]').forEach(card => {
                    const item = items[parseInt(card.dataset.repairIdx, 10)];
                    card.querySelectorAll('button[data-pick]').forEach(b => {
                        b.addEventListener('click', () => pickRepair(item, b.dataset.pick === 'full'));
                    });
                });
            }
            function fillField(name, value){
                if(value === undefined || value === null || String(value).trim() === '') return false;
                const input = form.querySelector('[name="' + name + '"]');
                if(!input) return false;
                input.value = value;
                input.classList.remove('!border-red-500');
                input.style.transition = 'background-color .4s';
                input.style.backgroundColor = 'rgba(16,185,129,.12)';
                setTimeout(() => { input.style.backgroundColor = ''; }, 1200);
                return true;
            }
            function pickRepair(item, withCustomer){
                const numInput = document.getElementById('leenRepairNumber');
                if(numInput && item.repair_number) numInput.value = item.repair_number;
                if(withCustomer){
                    fillField('customer_name', item.name);
                    fillField('customer_email', item.email);
                    fillField('phone', item.phone);
                    fillField('postcode', item.postcode);
                }
                if(repairPicked) repairPicked.classList.remove('hidden');
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.close(REPAIR_MODAL); }
                else { const m = document.getElementById('modal-' + REPAIR_MODAL); if(m) m.classList.add('hidden'); }
                if(window.SlimmePC && window.SlimmePC.toast){
                    window.SlimmePC.toast(withCustomer ? 'Reparatienummer + klantgegevens overgenomen.' : 'Reparatienummer overgenomen.', 'success');
                }
            }
            async function searchRepairs(q){
                lastRepairQuery = q;
                if(repairError){ repairError.textContent = ''; repairError.classList.add('hidden'); }
                if(repairSpinner) repairSpinner.classList.remove('hidden');
                try{
                    const res = await fetch('/admin/reparatie-aanmeldingen/data?search=' + encodeURIComponent(q) + '&per_page=8', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if(!res.ok) throw new Error('Zoeken mislukt (status ' + res.status + ').');
                    const data = await res.json();
                    if(q !== lastRepairQuery) return;
                    if(repairHint) repairHint.classList.add('hidden');
                    renderRepairResults(data.data || []);
                }catch(err){
                    if(repairError){ repairError.textContent = '✗ ' + (err.message || 'Er ging iets mis bij het zoeken.'); repairError.classList.remove('hidden'); }
                }finally{
                    if(repairSpinner) repairSpinner.classList.add('hidden');
                }
            }
            if(repairBtn){ repairBtn.addEventListener('click', openRepairModal); }
            if(repairQuery){
                repairQuery.addEventListener('input', () => {
                    clearTimeout(repairTimer);
                    const q = repairQuery.value.trim();
                    if(q.length < 2){
                        if(repairHint) repairHint.classList.remove('hidden');
                        if(repairResults) repairResults.innerHTML = '';
                        if(repairSpinner) repairSpinner.classList.add('hidden');
                        return;
                    }
                    repairTimer = setTimeout(() => searchRepairs(q), 300);
                });
                repairQuery.addEventListener('keydown', (e) => {
                    if(e.key === 'Enter'){ e.preventDefault(); clearTimeout(repairTimer); const q = repairQuery.value.trim(); if(q.length >= 2) searchRepairs(q); }
                });
            }
            const repairNumInput = document.getElementById('leenRepairNumber');
            if(repairNumInput && repairPicked){
                repairNumInput.addEventListener('input', () => repairPicked.classList.add('hidden'));
            }

            // Bestaande foto verwijderen (edit-modus)
            document.querySelectorAll('[data-delete-photo]').forEach(b => {
                b.addEventListener('click', async () => {
                    const photoId = b.dataset.deletePhoto;
                    const loanId = window.LEEN_LOAN_ID;
                    if (!confirm('Deze foto verwijderen?')) return;
                    try {
                        const res = await fetch('/admin/leen-huur/' + loanId + '/photo/' + photoId, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getToken(), 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) throw new Error('Verwijderen mislukt.');
                        const tile = document.querySelector('[data-existing-photo="' + photoId + '"]');
                        if (tile) tile.remove();
                    } catch(e){ alert(e.message); }
                });
            });

            // ---- Foto's: dashboard-stijl preview (dropzone + tiles + DataTransfer) ----
            const photoInput = document.getElementById('leenPhotos');
            const cameraInput = document.getElementById('leenPhotosCamera');
            const pickFilesBtn = document.getElementById('leenPickFiles');
            const pickCameraBtn = document.getElementById('leenPickCamera');
            const SOURCE_MODAL = 'leenPhotoSource';

            function openSourcePicker(){
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.open(SOURCE_MODAL); return; }
                const m = document.getElementById('modal-' + SOURCE_MODAL);
                if(m) m.classList.remove('hidden');
                else if(photoInput) photoInput.click();
            }
            function closeSourcePicker(){
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.close(SOURCE_MODAL); return; }
                const m = document.getElementById('modal-' + SOURCE_MODAL);
                if(m) m.classList.add('hidden');
            }

            if(pickFilesBtn && photoInput){
                pickFilesBtn.addEventListener('click', () => { photoInput.click(); closeSourcePicker(); });
            }

            const choiceView = document.getElementById('leenSourceChoice');
            const cameraView = document.getElementById('leenCameraView');
            const video = document.getElementById('leenCameraVideo');
            const deviceSelect = document.getElementById('leenCameraDevice');
            const camError = document.getElementById('leenCameraError');
            const captureBtn = document.getElementById('leenCaptureBtn');
            const camBackBtn = document.getElementById('leenCameraBackBtn');
            const canvas = document.getElementById('leenCameraCanvas');
            const sourceModalEl = document.getElementById('modal-' + SOURCE_MODAL);
            let camStream = null;

            function showCamError(text){
                if(!camError) return;
                if(!text){ camError.textContent = ''; camError.classList.add('hidden'); return; }
                camError.textContent = text;
                camError.classList.remove('hidden');
            }
            function stopCamera(){
                if(camStream){ camStream.getTracks().forEach(t => { try{ t.stop(); }catch(e){} }); camStream = null; }
                if(video) video.srcObject = null;
            }
            function showChoice(){
                stopCamera();
                if(cameraView){ cameraView.classList.add('hidden'); cameraView.classList.remove('flex'); }
                if(choiceView){ choiceView.classList.remove('hidden'); }
            }
            async function listCameras(){
                if(!deviceSelect) return;
                try{
                    const devs = await navigator.mediaDevices.enumerateDevices();
                    const cams = devs.filter(d => d.kind === 'videoinput');
                    deviceSelect.innerHTML = '';
                    if(!cams.length){ deviceSelect.innerHTML = '<option value="">Geen camera gevonden</option>'; return; }
                    cams.forEach((c, i) => {
                        const o = document.createElement('option');
                        o.value = c.deviceId;
                        o.textContent = c.label || ('Camera ' + (i + 1));
                        deviceSelect.appendChild(o);
                    });
                }catch(e){}
            }
            async function startCamera(deviceId){
                stopCamera();
                showCamError('');
                const base = { width: { ideal: 1920 }, height: { ideal: 1080 } };
                const wanted = deviceId ? Object.assign({ deviceId: { exact: deviceId } }, base) : Object.assign({ facingMode: 'environment' }, base);
                try{
                    camStream = await navigator.mediaDevices.getUserMedia({ audio: false, video: wanted });
                }catch(err){
                    if(deviceId){
                        try{ camStream = await navigator.mediaDevices.getUserMedia({ audio: false, video: base }); }
                        catch(e2){ showCamError('Camera niet beschikbaar. Controleer de cameratoestemming in de browser en probeer opnieuw.'); return; }
                    }else{
                        showCamError('Camera niet beschikbaar. Controleer de cameratoestemming in de browser en probeer opnieuw.');
                        return;
                    }
                }
                if(video){
                    video.srcObject = camStream;
                    try{ await video.play(); }catch(e){}
                }
            }
            async function openCameraView(){
                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                    closeSourcePicker();
                    if(cameraInput) cameraInput.click();
                    return;
                }
                if(choiceView) choiceView.classList.add('hidden');
                if(cameraView){ cameraView.classList.remove('hidden'); cameraView.classList.add('flex'); }
                if(captureBtn) captureBtn.disabled = true;
                try{
                    const tmp = await navigator.mediaDevices.getUserMedia({ audio: false, video: true });
                    tmp.getTracks().forEach(t => { try{ t.stop(); }catch(e){} });
                }catch(e){
                    showCamError('Geen toegang tot de camera. Sta cameragebruik toe in de browser en probeer opnieuw.');
                    if(captureBtn) captureBtn.disabled = false;
                    return;
                }
                await listCameras();
                await startCamera(deviceSelect && deviceSelect.value ? deviceSelect.value : null);
                if(captureBtn) captureBtn.disabled = false;
            }

            if(pickCameraBtn){ pickCameraBtn.addEventListener('click', () => { openCameraView(); }); }
            if(deviceSelect){ deviceSelect.addEventListener('change', function(){ startCamera(this.value || null); }); }
            if(camBackBtn){ camBackBtn.addEventListener('click', () => { showChoice(); }); }
            if(captureBtn && canvas){
                captureBtn.addEventListener('click', () => {
                    if(!camStream || !video || !video.videoWidth){
                        showCamError('Camera is nog aan het opstarten — wacht een moment en probeer opnieuw.');
                        return;
                    }
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob((blob) => {
                        if(!blob){ showCamError('Foto maken mislukt, probeer opnieuw.'); return; }
                        const file = new File([blob], 'camera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                        addFiles([file]);
                        closeSourcePicker();
                        showChoice();
                    }, 'image/jpeg', 0.92);
                });
            }
            if(sourceModalEl){
                sourceModalEl.addEventListener('click', (e) => {
                    if(e.target.closest('[data-modal-close],[data-modal-overlay]')) showChoice();
                });
            }
            document.addEventListener('keydown', (e) => { if(e.key === 'Escape') showChoice(); });
            if(cameraInput){ cameraInput.addEventListener('change', function(){ addFiles(this.files); this.value = ''; }); }

            const dropzone = document.getElementById('leenDropzone');
            const previewGrid = document.getElementById('leenPhotosPreview');
            const counter = document.getElementById('leenPhotoCounter');
            const feedback = document.getElementById('leenPhotosFeedback');
            const MAX_PHOTOS = 20;
            let stagedFiles = [];

            function showFeedback(text){
                if(!feedback) return;
                if(!text){ feedback.textContent=''; feedback.classList.add('hidden'); return; }
                feedback.textContent = text;
                feedback.classList.remove('hidden');
            }
            function syncInput(){
                const dt2 = new DataTransfer();
                stagedFiles.forEach(f => dt2.items.add(f));
                photoInput.files = dt2.files;
                if(counter) counter.textContent = stagedFiles.length + ' / ' + MAX_PHOTOS;
                if(dropzone) dropzone.classList.toggle('hidden', stagedFiles.length > 0);
            }
            function renderPreview(){
                if(!previewGrid) return;
                previewGrid.innerHTML = '';
                stagedFiles.forEach((file, idx) => {
                    const url = URL.createObjectURL(file);
                    const card = document.createElement('div');
                    card.className = 'group relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900';
                    card.innerHTML = '<img src="'+url+'" alt="Foto '+(idx+1)+'" class="h-full w-full object-cover">'
                        + '<button type="button" data-idx="'+idx+'" aria-label="Verwijderen" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-lg bg-black/60 text-white opacity-0 transition group-hover:opacity-100 hover:bg-red-600">'
                        + '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>'
                        + '<span class="absolute bottom-1 left-1 rounded-md bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">'+(idx+1)+'</span>';
                    previewGrid.appendChild(card);
                });
                const addTile = document.createElement('button');
                addTile.type = 'button';
                addTile.title = 'Foto toevoegen';
                addTile.className = 'flex h-20 w-20 sm:h-24 sm:w-24 shrink-0 cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/40 text-blue-600 transition hover:border-blue-500 hover:bg-blue-50/80 shadow-sm';
                addTile.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg><span class="text-[10px] font-bold">Toevoegen</span>';
                addTile.addEventListener('click', () => openSourcePicker());
                previewGrid.appendChild(addTile);
                previewGrid.querySelectorAll('button[data-idx]').forEach(b => {
                    b.addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        stagedFiles.splice(parseInt(b.dataset.idx, 10), 1);
                        syncInput(); renderPreview(); showFeedback('');
                    });
                });
            }
            function addFiles(files){
                const incoming = Array.from(files || []).filter(f => f.type.startsWith('image/'));
                if(!incoming.length) return;
                if(stagedFiles.length + incoming.length > MAX_PHOTOS){
                    showFeedback('Maximaal '+MAX_PHOTOS+' foto\'s per uitgifte. Eerste '+MAX_PHOTOS+' worden bewaard.');
                    incoming.splice(MAX_PHOTOS - stagedFiles.length);
                } else {
                    showFeedback('');
                }
                const oversized = incoming.filter(f => f.size > 10*1024*1024);
                if(oversized.length) showFeedback('Elke foto mag maximaal 10MB zijn — te grote bestanden zijn overgeslagen.');
                incoming.filter(f => f.size <= 10*1024*1024).forEach(f => stagedFiles.push(f));
                syncInput(); renderPreview();
            }

            if(dropzone && photoInput){
                dropzone.addEventListener('click', () => openSourcePicker());
                ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('border-blue-500','bg-blue-100/50'); }));
                ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('border-blue-500','bg-blue-100/50'); }));
                dropzone.addEventListener('drop', (e) => addFiles(e.dataTransfer.files));
                photoInput.addEventListener('change', function(){ addFiles(this.files); this.value = ''; });
                syncInput(); renderPreview();
            }

            form.addEventListener('submit', async (e)=>{
                e.preventDefault();
                if(!form.reportValidity()) return;
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Bezig met verzenden…';
                msg.className = 'mt-4 hidden text-sm font-semibold';
                msg.textContent = '';
                form.querySelectorAll('.field-error').forEach(el=>{ el.textContent=''; el.classList.add('hidden'); });
                form.querySelectorAll('.form-input').forEach(el=> el.classList.remove('!border-red-500'));

                const fd = new FormData(form);

                try{
                    const res = await fetch(window.LEEN_SUBMIT_URL, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: fd,
                    });
                    const data = await res.json().catch(()=>({}));
                    if(res.ok){
                        msg.textContent = '✓ ' + (data.message || 'Succesvol opgeslagen!');
                        msg.className = 'mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700';
                        msg.classList.remove('hidden');
                        if(!window.LEEN_IS_EDIT){
                            form.reset();
                            stagedFiles = []; syncInput(); renderPreview();
                            if (dt) {
                                const now2 = new Date();
                                const pad2 = n => String(n).padStart(2,'0');
                                dt.value = now2.getFullYear()+'-'+pad2(now2.getMonth()+1)+'-'+pad2(now2.getDate())+'T'+pad2(now2.getHours())+':'+pad2(now2.getMinutes());
                            }
                        }
                        setTimeout(()=>{ window.location.href = window.LEEN_INDEX_URL; }, 1500);
                        return;
                    }
                    if(res.status===422 && data.errors){
                        Object.entries(data.errors).forEach(([field, msgs])=>{
                            const base = field.split('.')[0];
                            const input = form.querySelector('[name="'+field+'"]') || form.querySelector('[name="'+base+'"]') || form.querySelector('[name="'+base+'[]"]');
                            const errEl = (input && input.parentElement) ? input.parentElement.querySelector('.field-error') : document.querySelector('[data-error-for="photos"]');
                            if(input) input.classList.add('!border-red-500');
                            if(errEl){ errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
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
                    label.textContent = defaultLabel;
                }
            });
        })();
    </script>
</x-admin.layout>

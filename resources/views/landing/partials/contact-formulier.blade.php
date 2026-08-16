@php $f = $p['formulier'] ?? []; @endphp

<section id="contactformulier" class="px-4 pb-14 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-br from-[#eaf4ff] via-[#f7fbff] to-white p-5 shadow-sm lg:p-8">
        <div class="grid gap-8 lg:grid-cols-[0.7fr_1.5fr]">

            {{-- Form intro (editable) --}}
            <div class="p-3 lg:p-6">
                @if (!empty($f['badge']))
                <p class="text-sm font-bold uppercase tracking-wide text-blue-600">{{ $f['badge'] }}</p>
                @endif

                <h2 class="mt-3 text-3xl font-black leading-tight text-[#0b1f4d] lg:text-4xl">
                    {{ $f['title_line1'] ?? '' }}
                    @if (!empty($f['title_line2']))
                    <br><span class="text-blue-600">{{ $f['title_line2'] }}</span>
                    @endif
                </h2>

                @if (!empty($f['description']))
                <p class="mt-5 max-w-md leading-relaxed text-slate-600">{{ $f['description'] }}</p>
                @endif

                @if (count($f['benefits'] ?? []))
                <div class="mt-8 space-y-4 text-sm text-slate-700">
                    @foreach ($f['benefits'] as $b)
                    <div class="flex items-center gap-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs font-bold text-blue-600 shadow-sm" aria-hidden="true">✓</span>
                        {{ $b['label'] ?? '' }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Contact form --}}
            <form id="contactForm" action="{{ route('contact.submit') }}" method="POST" novalidate class="rounded-3xl border border-blue-100 bg-white p-6 card-soft lg:p-8">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="contact-name" class="mb-2 block text-sm font-bold text-[#0b1f4d]">Naam *</label>
                        <input id="contact-name" type="text" name="name" required placeholder="Vul je naam in"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="contact-phone" class="mb-2 block text-sm font-bold text-[#0b1f4d]">Telefoon</label>
                        <input id="contact-phone" type="tel" name="phone" placeholder="Bijvoorbeeld 06 12345678"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="contact-email" class="mb-2 block text-sm font-bold text-[#0b1f4d]">E-mailadres *</label>
                        <input id="contact-email" type="email" name="email" required placeholder="naam@email.nl"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label for="contact-subject" class="mb-2 block text-sm font-bold text-[#0b1f4d]">Onderwerp *</label>
                        <select id="contact-subject" name="subject" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                            <option value="">Kies een onderwerp</option>
                            <option value="reparatie">Reparatie</option>
                            <option value="diagnose">Diagnose</option>
                            <option value="data-recovery">Data recovery</option>
                            <option value="zakelijk">Zakelijke IT-dienst</option>
                            <option value="stage">Stage</option>
                            <option value="anders">Anders</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="mb-3 text-sm font-bold text-[#0b1f4d]">Type aanvraag</p>
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="request_type" value="reparatie" class="peer sr-only" checked>
                            <span class="flex min-h-[82px] flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-center text-sm font-bold text-slate-700 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                <span class="text-xl" aria-hidden="true">🛠</span> Reparatie
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="request_type" value="zakelijk" class="peer sr-only">
                            <span class="flex min-h-[82px] flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-center text-sm font-bold text-slate-700 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                <span class="text-xl" aria-hidden="true">💼</span> Zakelijk
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="request_type" value="algemene-vraag" class="peer sr-only">
                            <span class="flex min-h-[82px] flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-center text-sm font-bold text-slate-700 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                <span class="text-xl" aria-hidden="true">?</span> Algemene vraag
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="request_type" value="stage" class="peer sr-only">
                            <span class="flex min-h-[82px] flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-center text-sm font-bold text-slate-700 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                <span class="text-xl" aria-hidden="true">🎓</span> Stage
                            </span>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="contact-message" class="mb-2 block text-sm font-bold text-[#0b1f4d]">Bericht *</label>
                    <textarea id="contact-message" name="message" required rows="6"
                              placeholder="Beschrijf je vraag of probleem zo duidelijk mogelijk..."
                              class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"></textarea>
                </div>

                <div class="mt-6">
                    <label for="contact-attachment" class="mb-2 block text-sm font-bold text-[#0b1f4d]">
                        Bestand toevoegen <span class="font-normal text-slate-400">(optioneel)</span>
                    </label>
                    <label for="contact-attachment"
                           class="flex min-h-[130px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/50 px-5 text-center transition hover:border-blue-400 hover:bg-blue-50">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-blue-600 shadow-sm" aria-hidden="true">📎</span>
                        <span class="mt-3 text-sm font-bold text-[#0b1f4d]">Klik om een bestand te selecteren</span>
                        <span class="mt-1 text-xs text-slate-500">Afbeelding, PDF of document – maximaal 10 MB</span>
                        <input id="contact-attachment" type="file" name="attachment" class="hidden">
                    </label>
                </div>

                <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex cursor-pointer items-start gap-3 text-xs leading-relaxed text-slate-500">
                        <input type="checkbox" name="privacy_consent" value="1" required class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Ik ga akkoord met de verwerking van mijn gegevens voor het beantwoorden van mijn aanvraag.</span>
                    </label>
                    <button type="submit" id="contactFormSubmit"
                            class="inline-flex shrink-0 items-center justify-center gap-3 rounded-xl bg-blue-600 px-6 py-3.5 font-bold text-white shadow-lg shadow-blue-600/20 transition duration-300 hover:-translate-y-0.5 hover:bg-blue-500">
                        Bericht versturen
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

<script src="{{ asset('assets/js/contact-form.js') }}?v={{ filemtime(public_path('assets/js/contact-form.js')) }}"></script>
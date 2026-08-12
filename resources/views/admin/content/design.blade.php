<x-admin.layout :title="$title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl" style="color: var(--c-heading)">
                Ontwerp &amp; SEO
            </h2>
            <p class="text-xs" style="color: var(--c-muted)">Titel en omschrijving van de website voor zoekmachines</p>
        </div>

        <a href="{{ url('/') }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40"
           style="border-color: rgba(96,165,250,.35)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            Website bekijken
        </a>
    </div>

    {{-- Info banner --}}
    <div class="mb-6 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-2xl border px-5 py-3.5 text-xs font-semibold"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25); color: var(--c-muted)">
        <span class="inline-flex items-center gap-2" style="color: var(--c-heading)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-blue-600 dark:text-blue-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informatie
        </span>
        <span>Pas de SEO-gegevens hieronder aan en klik op opslaan om zoekmachines te helpen de website te indexeren.</span>
    </div>

    {{-- Main form card --}}
    <div class="overflow-hidden rounded-2xl border shadow-sm"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="px-5 py-5">
            <form id="design-form" data-url="{{ route('admin.content.design') }}" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($designGroups as $groupKey => $group)
                        @foreach ($group['fields'] as $field)
                            @php $value = $design[$field['key']] ?? ($field['default'] ?? ''); @endphp
                            <div class="{{ ($field['type'] ?? 'text') === 'textarea' ? 'sm:col-span-2' : '' }}">
                                <label class="mb-1.5 block text-xs font-semibold" style="color: var(--c-heading)">
                                    {{ $field['label'] }}
                                </label>

                                @if (($field['type'] ?? 'text') === 'textarea')
                                    <textarea name="design[{{ $groupKey }}][{{ $field['key'] }}]" rows="3"
                                              class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}</textarea>
                                @else
                                    <input type="text" name="design[{{ $groupKey }}][{{ $field['key'] }}]" value="{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}"
                                           class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div class="-mx-5 -mb-5 mt-6 flex items-center justify-between border-t px-5 py-4 bg-slate-50 dark:bg-slate-800/20 rounded-b-2xl"
                     style="border-color: rgba(148, 163, 184, 0.15)">
                    <span class="form-status text-xs font-semibold text-emerald-500"></span>
                    <x-admin.save-button label="SEO-instellingen opslaan" />
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>

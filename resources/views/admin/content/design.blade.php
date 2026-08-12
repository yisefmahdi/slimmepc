<x-admin.layout :title="$title">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3">

            <div>
                <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl" style="color: var(--c-heading)">
                    Ontwerp &amp; SEO (Zoekmachine optimalisatie)
                </h1>
                <p class="text-xs sm:text-sm" style="color: var(--c-muted)">
                    Beheer hier de paginatitel en beschrijving die Google en andere zoekmachines tonen wanneer bezoekers uw website zoeken.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="overflow-hidden rounded-2xl border shadow-sm"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="bg-blue-50/50 px-6 py-4 border-b dark:bg-slate-800/40" style="border-color: rgba(148,163,184,.15)">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">SEO &amp; Meta Gegevens</span>
        </div>
        <div class="px-6 py-6">
            <form id="design-form" data-url="{{ route('admin.content.design') }}" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach ($designGroups as $groupKey => $group)
                        @foreach ($group['fields'] as $field)
                            @php $value = $design[$field['key']] ?? ($field['default'] ?? ''); @endphp
                            <div class="{{ ($field['type'] ?? 'text') === 'textarea' ? 'sm:col-span-2' : '' }}">
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wide" style="color: var(--c-heading)">
                                    {{ $field['label'] }}
                                </label>
                                <p class="mb-2 text-xs" style="color: var(--c-muted)">
                                    {{ $field['key'] === 'meta_title' ? 'De hoofdtitel van de website in het browsetabblad en Google.' : 'Een korte, pakkende samenvatting van de website (max 160 tekens).' }}
                                </p>

                                @if (($field['type'] ?? 'text') === 'textarea')
                                    <textarea name="design[{{ $groupKey }}][{{ $field['key'] }}]" rows="3"
                                              class="w-full rounded-xl border px-4 py-3 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40 shadow-sm"
                                              style="background-color: var(--c-input, rgba(0,0,0,.02)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}</textarea>
                                @else
                                    <input type="text" name="design[{{ $groupKey }}][{{ $field['key'] }}]" value="{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}"
                                           class="w-full rounded-xl border px-4 py-3 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40 shadow-sm"
                                           style="background-color: var(--c-input, rgba(0,0,0,.02)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>

                {{-- Action Footer --}}
                <div class="-mx-6 -mb-6 mt-8 flex items-center justify-between border-t px-6 py-4 bg-slate-50 dark:bg-slate-800/30 rounded-b-2xl"
                     style="border-color: rgba(148, 163, 184, 0.15)">
                    <span class="form-status text-xs font-bold"></span>
                    <x-admin.save-button label="Wijzigingen opslaan" />
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>

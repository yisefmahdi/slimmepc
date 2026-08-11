@props(['id', 'title', 'size' => 'md', 'subtitle' => null])

@php
$sizes = [
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
];
@endphp

<div id="modal-{{ $id }}" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-{{ $id }}-title">
    {{-- Overlay --}}
    <div data-modal-overlay class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm modal-overlay-anim"></div>

    {{-- Panel --}}
    <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="modal-panel-anim w-full {{ $sizes[$size] ?? 'max-w-lg' }} overflow-hidden rounded-2xl border shadow-2xl"
             style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2)">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-4 border-b px-6 py-4" style="border-color: rgba(148, 163, 184, 0.15)">
                <div>
                    <h3 id="modal-{{ $id }}-title" class="text-base font-bold" style="color: var(--c-heading)">{{ $title }}</h3>
                    @if ($subtitle)
                        <p class="mt-0.5 text-xs" style="color: var(--c-muted)">{{ $subtitle }}</p>
                    @endif
                </div>

                <button type="button" data-modal-close aria-label="Sluiten"
                        class="rounded-lg p-2 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                        style="color: var(--c-muted)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="max-h-[70vh] overflow-y-auto p-6">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="flex flex-col-reverse items-stretch justify-end gap-3 border-t px-6 py-4 sm:flex-row sm:items-center" style="border-color: rgba(148, 163, 184, 0.15)">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>

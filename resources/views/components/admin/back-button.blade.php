@props(['href', 'label' => 'Overzicht'])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition']) }} style="color: var(--c-heading); border-color: var(--c-input-border)">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    {{ $label }}
</a>

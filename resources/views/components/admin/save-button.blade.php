@props(['label' => 'Opslaan'])

<button type="submit" {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-8 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500/50 disabled:cursor-not-allowed disabled:opacity-60 min-w-[200px] p-2']) }}>
    <svg class="btn-icon h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
    <span class="btn-label">{{ $label }}</span>
    <svg class="btn-spinner h-4 w-4 animate-spin" style="display: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
    </svg>
</button>


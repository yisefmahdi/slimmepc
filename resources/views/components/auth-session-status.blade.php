@props(['status'])

@if ($status)
    <div data-alert data-auto-dismiss {{ $attributes->merge(['class' => 'mb-4 flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-400']) }}>
        <span class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 shrink-0">
                <circle cx="12" cy="12" r="10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
            </svg>
            {{ $status }}
        </span>
        <button type="button" data-dismiss-alert aria-label="Dismiss" class="shrink-0 text-green-600 transition hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif


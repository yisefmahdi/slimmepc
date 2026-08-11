@props(['label', 'value', 'trend' => null, 'trendUp' => true, 'icon' => null])

<div class="fade-in-up rounded-2xl border p-5 transition duration-300 hover:-translate-y-0.5"
     style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2); box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06)">
    <div class="flex items-center justify-between">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
            @if ($icon)
                {{ $icon }}
            @endif
        </div>

        @if ($trend)
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold {{ $trendUp ? 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3">
                    @if ($trendUp)
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    @endif
                </svg>
                {{ $trend }}
            </span>
        @endif
    </div>

    <p class="mt-4 text-sm font-medium" style="color: var(--c-muted)">{{ $label }}</p>
    <p class="mt-1 text-2xl font-extrabold tracking-tight" style="color: var(--c-heading)">{{ $value }}</p>
</div>

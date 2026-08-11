@props(['title' => null])

<div class="rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2); box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06)">
    @if ($title || isset($action) && $action->isNotEmpty())
        <div class="flex items-center justify-between gap-4 border-b px-6 py-4" style="border-color: rgba(148, 163, 184, 0.15)">
            @if ($title)
                <h3 class="text-base font-bold" style="color: var(--c-heading)">{{ $title }}</h3>
            @endif

            @if (isset($action) && $action->isNotEmpty())
                {{ $action }}
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>

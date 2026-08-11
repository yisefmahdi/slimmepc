@props(['active' => false])

@php
$classes = $active
    ? 'group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.35)] bg-gradient-to-r from-[#075be8] to-[#064bd7] transition duration-200'
    : 'group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/10 hover:text-white';
$color = $active ? '#ffffff' : 'rgba(203,213,225,0.85)';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="color: {{ $color }}">
    <span class="shrink-0">
        {{ $icon }}
    </span>
    <span class="truncate">{{ $slot }}</span>

    @if ($badge ?? false)
        <span class="ms-auto rounded-full bg-white/20 px-2 py-0.5 text-xs font-bold text-white">{{ $badge }}</span>
    @endif
</a>

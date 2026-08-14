@props(['active' => false])

@php
$classes = $active
    ? 'group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#1d4ed8] bg-white shadow-[0_10px_25px_rgba(0,0,0,0.15)] transition duration-200'
    : 'group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200 hover:bg-white/15 hover:text-white';
$color = $active ? '#1d4ed8' : '#ffffff';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="color: {{ $color }}">
    <span class="shrink-0">
        {{ $icon }}
    </span>
    <span class="truncate">{{ $slot }}</span>

    @if ($badge ?? false)
        <span class="ms-auto rounded-full bg-[#1d4ed8]/10 px-2 py-0.5 text-xs font-bold text-[#1d4ed8]">{{ $badge }}</span>
    @endif
</a>


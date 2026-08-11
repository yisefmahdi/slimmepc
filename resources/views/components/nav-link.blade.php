@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-blue-600 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none dark:border-blue-400'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out hover:border-gray-300 focus:outline-none dark:hover:border-slate-600';
$textColor = ($active ?? false) ? 'color: var(--c-heading)' : 'color: var(--c-muted)';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="{{ $textColor }}">
    {{ $slot }}
</a>

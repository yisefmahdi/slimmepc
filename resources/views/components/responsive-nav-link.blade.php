@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-blue-600 text-start text-base font-medium transition duration-150 ease-in-out focus:outline-none dark:border-blue-400 dark:bg-slate-800/60'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium transition duration-150 ease-in-out hover:border-gray-300 hover:bg-gray-50 focus:outline-none dark:hover:border-slate-600 dark:hover:bg-slate-800/40';
$textColor = ($active ?? false) ? 'color: var(--c-heading)' : 'color: var(--c-muted)';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="{{ $textColor }}">
    {{ $slot }}
</a>

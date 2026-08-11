@props(['compact' => false, 'type' => 'submit'])

<button
    type="{{ $type }}"
    data-loading
    {{ $attributes->merge(['class' => $compact ? 'btn-primary btn-primary--compact' : 'btn-primary']) }}
>
    @if ($icon ?? false)
        {!! $icon !!}
    @endif

    {{ $slot }}
</button>

@props(['value', 'compact' => false])

<label {{ $attributes->merge(['class' => $compact ? 'form-label form-label--compact' : 'form-label']) }}>
    {{ $value ?? $slot }}
</label>


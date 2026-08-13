@props(['disabled' => false, 'compact' => false, 'toggle' => false, 'inputId' => null])

<div class="{{ (isset($icon) && $icon->isNotEmpty()) || $toggle ? 'relative' : '' }}">
    @if (isset($icon) && $icon->isNotEmpty())
        <span class="{{ $compact ? 'form-icon form-icon--compact' : 'form-icon' }}">
            {{ $icon }}
        </span>
    @endif

    <input
        @disabled($disabled)
        {{ $attributes->merge([
            'id' => $inputId ?? $attributes->get('id'),
            'class' => ($compact ? 'form-input form-input--compact' : 'form-input')
                       . ($toggle ? ' pr-12' : ''),
        ]) }}
    >

    @if ($toggle)
        <button
            type="button"
            data-toggle-password="{{ $inputId ?? $attributes->get('id') }}"
            aria-label="Show password"
            class="form-input-toggle"
        >
            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                <circle cx="12" cy="12" r="2.5" />
            </svg>

            <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="hidden h-5 w-5">
                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                <path d="m2 2 20 20" />
            </svg>
        </button>
    @endif
</div>


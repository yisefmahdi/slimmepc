@props(['size' => 100])

<a href="/" {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    <img
        src="{{ asset(\App\Support\Cms::page('home')['header']['logo_image'] ?? 'assets/img/logo.webp') }}"
        alt="{{ \App\Support\Cms::page('home')['header']['logo_text'] ?? 'Slimme-PC' }}"
        class="object-contain"
        style="width: {{ is_numeric($size) ? $size : 100 }}px"
    >
</a>

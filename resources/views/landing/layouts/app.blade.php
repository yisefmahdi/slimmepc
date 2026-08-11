<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $design['meta_title'] ?? 'Slimme-PC' }}</title>
    <meta name="description" content="{{ $design['meta_description'] ?? '' }}">

    <link rel="icon" href="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
        @php
            $fonts = [
                'Figtree' => 'figtree:400,500,600,700,800',
                'Inter' => 'inter:400,500,600,700',
                'Poppins' => 'poppins:400,500,600,700',
                'Roboto' => 'roboto:400,500,700',
                'Merriweather' => 'merriweather:400,700',
            ];
            $fontKey = $design['font_family'] ?? 'Figtree';
        @endphp
        <link href="https://fonts.bunny.net/css?family={{ $fonts[$fontKey] ?? $fonts['Figtree'] }}&display=swap"
            rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">

    <style>
        :root {
            --brand-primary: {{ $design['brand_primary'] ?? '#2563eb' }};
            --brand-primary-dark: {{ $design['brand_primary_dark'] ?? '#1d4ed8' }};
            --brand-accent: {{ $design['brand_accent'] ?? '#84cc16' }};
            --brand-heading: {{ $design['brand_heading'] ?? '#020617' }};
            --brand-gradient-from: {{ $design['gradient_from'] ?? '#1d4ed8' }};
            --brand-gradient-to: {{ $design['gradient_to'] ?? '#3b82f6' }};
        }

        body {
            font-family: '{{ $design['font_family'] ?? 'Figtree' }}', sans-serif;
        }
    </style>
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/js/vendor/lucide.min.js') }}"></script>
    <script src="{{ asset('assets/js/landing.js') }}"></script>
</body>

</html>

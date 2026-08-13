<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $design['meta_title'] ?? 'Slimme-PC' }}</title>
    <meta name="description" content="{{ $design['meta_description'] ?? '' }}">

    <link rel="icon" href="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
            rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">

    <style>
        /* Brand colors + font are FIXED in code (not editable via CMS) */
        :root {
            --brand-primary: #2563eb;
            --brand-primary-dark: #1d4ed8;
            --brand-accent: #84cc16;
            --brand-heading: #020617;
            --brand-gradient-from: #1d4ed8;
            --brand-gradient-to: #3b82f6;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/js/vendor/lucide.min.js') }}"></script>
    <script src="{{ asset('assets/js/landing.js') }}"></script>
</body>

</html>


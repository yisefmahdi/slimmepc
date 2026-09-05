<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $design['meta_title'] ?? 'Slimme-PC' }}</title>
    <meta name="description" content="{{ $design['meta_description'] ?? '' }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.webp') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
            rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}?v={{ filemtime(public_path('assets/css/landing.css')) }}">

    <style>
        /* Brand colors + font are FIXED in code (not editable via CMS) */
        :root {
            --brand-primary: #2563eb;
            --brand-primary-dark: #1d4ed8;
            --brand-accent: #84cc16;
            --brand-heading: #020617;
            --brand-gradient-from: #1d4ed8;
            --brand-gradient-to: #3b82f6;
            /* Design system vars for toasts/modals (admin uses app.css, landing needs fallback — was transparent) */
            --c-card: #ffffff;
            --c-heading: #07173A;
            --c-body: #3E547F;
            --c-muted: #64748b;
            --c-input-bg: #ffffff;
            --c-input-border: #DCE4EF;
            --c-page: #ffffff;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }
        /* Ensure toasts are opaque on landing (design.js uses var(--c-card)) */
        [data-toast] { background: #fff !important; border-color: rgba(148,163,184,.25) !important; }
        [data-toast] p { color: var(--c-heading) !important; }
    </style>
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/js/vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/axios.min.js') }}"></script>
    <script src="{{ asset('assets/js/design.js') }}?v={{ filemtime(public_path('assets/js/design.js')) }}"></script>
    <script src="{{ asset('assets/js/vendor/lucide.min.js') }}"></script>
    <script src="{{ asset('assets/js/landing.js') }}?v={{ filemtime(public_path('assets/js/landing.js')) }}"></script>
    <script src="{{ asset('assets/js/cart.js') }}?v={{ filemtime(public_path('assets/js/cart.js')) }}"></script>
</body>

</html>


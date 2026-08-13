<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Slimme-PC') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        {{-- Scripts / Styles --}}
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        <script src="{{ asset('assets/js/vendor/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/alpine.min.js') }}"></script>
        <script src="{{ asset('assets/js/design.js') }}"></script>
    </head>

    <body class="font-sans antialiased">
        {{-- Theme toggle (floating, top-right) --}}
        <div class="fixed right-5 top-5 z-50">
            <x-theme-toggle class="border-slate-200 bg-white/80 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/80" />
        </div>

        {{ $slot }}
    </body>
</html>


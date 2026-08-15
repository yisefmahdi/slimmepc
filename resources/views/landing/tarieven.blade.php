@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        @include('landing.partials.tarieven-hero')
        @include('landing.partials.tarieven-pricing')
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <script src="{{ asset('assets/js/tarieven.js') }}?v={{ filemtime(public_path('assets/js/tarieven.js')) }}"></script>
@endsection
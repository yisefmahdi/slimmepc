@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        @include('landing.partials.overons-hero')
        @include('landing.partials.overons-meet')
        @include('landing.partials.overons-why')
        @include('landing.partials.overons-werkplaats')
        @include('landing.partials.overons-reis')
        @include('landing.partials.overons-reviews')
        @include('landing.partials.overons-trust')
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <link rel="stylesheet" href="{{ asset('assets/css/overons.css') }}?v={{ filemtime(public_path('assets/css/overons.css')) }}">
@endsection
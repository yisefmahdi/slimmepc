@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        @include('landing.partials.contact-hero')
        @include('landing.partials.contact-gegevens')
        @include('landing.partials.contact-formulier')
        @include('landing.partials.contact-locatie')
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
@endsection
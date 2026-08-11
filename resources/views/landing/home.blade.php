@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        @include('landing.partials.hero')
        @include('landing.partials.why')
        @include('landing.partials.services')
        @include('landing.partials.shop')
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
@endsection

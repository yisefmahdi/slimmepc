@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main>
        @include('landing.partials.legal-page', ['l' => $l ?? []])
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
    @include('landing.partials.ai-chat')
@endsection

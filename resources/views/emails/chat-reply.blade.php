@extends('emails.layout')

@section('kicker', 'Live Chat · Antwoord')
@section('title')
{{ $conversation->name }}, een medewerker heeft gereageerd
@endsection

@section('body')
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">{{ $reply->body }}</p>
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">Houd deze e-mail bij de hand. Je kunt direct op dit bericht reageren.</p>
@endsection

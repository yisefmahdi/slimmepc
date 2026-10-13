@extends('emails.layout')

@section('badge', 'Bericht')
@section('kicker', 'Slimme-PC · Bericht')
@section('title', $subjectText)

@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">{!! nl2br(e($bodyText)) !!}</p>
@if($type === 'maintenance')
<p style="margin:12px 0 0; font-size:14px; line-height:1.7; color:#3E547F;">Vragen? Wij staan voor je klaar.</p>
<p style="margin:12px 0 0; font-size:14px; line-height:1.7; color:#3E547F;">Met vriendelijke groet,</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Het Slimme-PC team</p>
@endif
@endsection

@if($type === 'maintenance')
@section('cta_url', config('app.url') . '/afspraak')
@section('cta_label', 'Afspraak maken')
@endif

@extends('emails.layout')

@section('kicker', 'Contact · Reactie')
@section('title')Hallo {{ $submission->name }},@endsection

@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">{!! nl2br(e($replyBody)) !!}</p>
@endsection

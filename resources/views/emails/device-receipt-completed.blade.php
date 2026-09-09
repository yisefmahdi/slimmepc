@extends('emails.layout')

@section('badge', 'Gerepareerd')
@section('kicker', 'Apparaat · Statusupdate')
@section('title', 'Uw apparaat is gerepareerd!')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Beste {{ $receipt->customer_name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">We zijn blij u te kunnen informeren dat de reparatie van uw <strong style="color:#0b1734;">{{ $receipt->device_type }}</strong> is voltooid. Uw apparaat ligt nu klaar om te worden opgehaald bij onze vestiging.</p>
@endsection

@section('cta_url', route('tracking.index', ['t_number' => $receipt->receiptNumber()]))
@section('cta_label', 'Bekijk status van mijn apparaat')

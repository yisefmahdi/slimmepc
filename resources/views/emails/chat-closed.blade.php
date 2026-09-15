@extends('emails.layout')

@section('kicker', 'Live Chat · Afgesloten')
@section('title')
Bedankt voor je chat, {{ $conversation->name }}!
@endsection

@section('body')
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">Je gesprek is afgesloten. Hieronder vind je een samenvatting. Voor nieuwe vragen kun je altijd een nieuwe chat starten op onze website.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Gesprek #{{ $conversation->id }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  @foreach ($conversation->messages->take(-10) as $msg)
  <tr>
    <td style="font-size:12px; font-weight:700; color:#64748b; padding:5px 8px 5px 0; vertical-align:top; white-space:nowrap;">{{ $msg->sender === 'customer' ? 'Jij' : ($msg->sender === 'admin' ? 'Medewerker' : 'AI') }}</td>
    <td style="font-size:13px; color:#0b1734; padding:5px 0;">{{ $msg->body ?: '(foto)' }}</td>
  </tr>
  @endforeach
</table>
@endsection

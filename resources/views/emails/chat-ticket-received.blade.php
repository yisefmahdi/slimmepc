@extends('emails.layout')

@section('kicker', 'Live Chat · Ticket')
@section('title')
Ticket aangemaakt
@endsection

@section('body')
<p dir="auto" style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">{{ $ticketText }}</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Gesprek #{{ $conversation->id }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Status</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">In behandeling</td>
  </tr>
</table>
@endsection

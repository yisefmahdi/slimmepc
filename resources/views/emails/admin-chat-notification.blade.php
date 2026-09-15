@extends('emails.layout')

@section('kicker', $reason === 'handover' ? 'Live Chat · Medewerker gevraagd' : 'Live Chat · Offline bericht')
@section('title')
@if ($reason === 'handover')
Een klant vraagt om een medewerker
@else
Nieuw offline chatbericht
@endif
@endsection

@section('body')
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">
@if ($reason === 'handover')
{{ $conversation->name }} ({{ $conversation->email }}) wil graag door een medewerker geholpen worden. Open het gesprek in het dashboard en neem het over.
@else
{{ $conversation->name }} ({{ $conversation->email }}) heeft een bericht achtergelaten buiten openingstijden.
@endif
</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Gesprek #{{ $conversation->id }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Naam</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $conversation->name }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">E-mail</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $conversation->email }}</td>
  </tr>
</table>
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin:14px 0 8px;">Berichten van de klant</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  @forelse ($conversation->messages->where('sender', 'customer')->take(-3) as $msg)
  <tr>
    <td style="font-size:13px; color:#0b1734; padding:6px 0; border-top:1px solid #eef2f7;">{{ $msg->body ?: '(alleen foto)' }}{{ $msg->attachment ? ' — [foto bijgevoegd]' : '' }}</td>
  </tr>
  @empty
  <tr>
    <td style="font-size:13px; color:#64748b; padding:6px 0;">(nog geen berichten)</td>
  </tr>
  @endforelse
</table>
@endsection

@section('cta_url')
{{ route('admin.chat.inbox.index', ['conversation' => $conversation->id]) }}
@endsection
@section('cta_label')
Open gesprek
@endsection

@extends('emails.layout')

@section('kicker', 'Contact · Bevestiging')
@section('title')Bedankt, {{ $submission->name }}!@endsection

@section('body')
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">We hebben je bericht ontvangen en nemen zo snel mogelijk contact met je op. Dat is meestal binnen één werkdag.</p>
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">Houd deze e-mail bij de hand. Je kunt direct op dit bericht reageren en je antwoord komt dan automatisch bij ons terecht.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Samenvatting van je aanvraag</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Onderwerp</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->subject }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Type</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->request_type }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Bericht</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->message }}</td>
  </tr>
</table>
@endsection

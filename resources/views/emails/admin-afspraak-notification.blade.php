@extends('emails.layout')

@section('badge', 'Nieuwe aanvraag')
@section('kicker', 'Afspraak aan huis · Adminmelding')
@section('title', 'Nieuwe afspraak-aan-huis aanvraag')

@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Er is een nieuwe aanvraag voor een <strong style="color:#0b1734;">afspraak aan huis</strong> binnengekomen.</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Aanvraagnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->afspraak_number }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Naam</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->name }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">E-mail</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->email }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Telefoon</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->phone }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Adres</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->street }} {{ $submission->house_number }}, {{ $submission->postcode }} {{ $submission->city }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Apparaat</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->device }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Gewenste datum</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->preferred_date->format('d-m-Y') }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Gewenst tijdstip</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->preferred_time }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Status</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->status }}</td>
  </tr>
</table>
<p style="margin:12px 0 0; font-size:13px; line-height:1.7; color:#3E547F;"><strong style="color:#0b1734;">Omschrijving probleem:</strong><br>{{ $submission->problem }}</p>
@endsection

@section('cta_url', config('app.url') . '/admin/afspraak-aanvragen/' . $submission->id)
@section('cta_label', 'Bekijk in admin')

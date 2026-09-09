@extends('emails.layout')

@section('kicker', 'Afspraak aan huis · Bevestiging')
@section('title', 'Bedankt voor uw aanvraag aan huis')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Beste {{ $submission->name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">We hebben uw aanvraag voor een <strong style="color:#0b1734;">afspraak aan huis</strong> goed ontvangen. Hieronder vindt u een overzicht van uw gegevens.</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Aanvraagnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->afspraak_number }}</td>
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
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Adres</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->street }} {{ $submission->house_number }}, {{ $submission->postcode }} {{ $submission->city }}</td>
  </tr>
</table>
<p style="margin:12px 0 0; font-size:13px; line-height:1.7; color:#3E547F;"><strong style="color:#0b1734;">Uw omschrijving:</strong><br>{{ $submission->problem }}</p>
<p style="margin:12px 0 0; font-size:13px; line-height:1.7; color:#3E547F;">We nemen zo spoedig mogelijk (binnen 24 uur) contact met u op om de afspraak te bevestigen. Heeft u ondertussen vragen? Antwoord dan gerust op deze e-mail.</p>
@endsection

@section('cta_url', config('app.url'))
@section('cta_label', 'Naar de website')

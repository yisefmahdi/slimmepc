@extends('emails.layout')

@section('kicker', 'Reparatie · Bevestiging')
@section('title')Bedankt, {{ $submission->name }}!@endsection

@section('body')
<p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#3E547F;">We hebben je reparatieaanvraag ontvangen (aanmeldnummer <strong style="color:#0b1734;">{{ $submission->repair_number }}</strong>). We nemen zo snel mogelijk contact met je op, meestal binnen één werkdag.</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Houd deze e-mail bij de hand. Je kunt direct op dit bericht reageren en je antwoord komt dan automatisch bij ons terecht.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Samenvatting van je aanvraag</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Aanmeldnummer</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->repair_number }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Apparaat</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->device }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Merk</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->brand }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Model</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->model }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Serienummer</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->serial ?: 'Niet opgegeven' }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Probleem</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ implode(', ', (array) $submission->problems) }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Omschrijving</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->description }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Belangrijke gegevens</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->data_importance }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Eerder geopend</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->opened_before }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Naam</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->name }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">E-mail</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->email }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Telefoon</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->phone }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Postcode</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->postcode }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Vervolg</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->delivery_method }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Contactvoorkeur</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->contact_preference }}</td></tr>
</table>
@endsection

@extends('emails.layout')

@section('badge', 'Nieuwe aanvraag')
@section('kicker', 'Reparatie · Adminmelding')
@section('title', 'Nieuwe reparatieaanvraag')

@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Er is zojuist een nieuwe reparatieaanvraag binnengekomen via het aanmeldformulier op de website. Van: <strong style="color:#0b1734;">{{ $submission->name }}</strong> ({{ $submission->email }}, {{ $submission->phone }})</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Aanmeldnummer</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->repair_number }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Apparaat</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->device }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Merk</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->brand }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Model</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->model }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Serienummer</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->serial ?: '—' }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Probleem</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ implode(', ', (array) $submission->problems) }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Omschrijving</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->description }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Belangrijke gegevens</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->data_importance }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Eerder geopend</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->opened_before }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Vervolg</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->delivery_method }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Contactvoorkeur</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->contact_preference }}</td></tr>
  <tr><td style="font-size:13px; color:#64748b; padding:4px 0;">Ontvangen op</td><td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $submission->created_at->format('d-m-Y H:i') }}</td></tr>
</table>
@endsection

@section('cta_url', $inboxUrl)
@section('cta_label', 'Open in dashboard')

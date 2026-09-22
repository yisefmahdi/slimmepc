@extends('emails.layout')

@section('kicker', 'Nieuwe klant')
@section('title')Nieuwe klant toegevoegd@endsection

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Een nieuwe klant is succesvol aangemaakt.</p>
<p style="margin:12px 0 0; font-size:14px; line-height:1.7; color:#3E547F;">Bedankt,<br>Slimme-PC</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Klantnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $user->klantnummer }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Naam</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $user->name }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">E-mail</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $user->email }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Telefoon</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $user->phone ?: '—' }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Adres</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ trim(($user->street ?? '').' '.($user->house_number ?? '').', '.($user->postcode ?? '').' '.($user->city ?? ''), ', ') ?: '—' }}</td>
  </tr>
</table>
@endsection

@section('cta_url', route('technician.login'))
@section('cta_label', 'Login als technicus')

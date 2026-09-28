@extends('emails.layout')

@section('kicker', 'Leen-laptop · Retourbevestiging')
@section('title', 'Laptop retour ontvangen')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Geachte {{ $loan->customer_name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Hartelijk dank! Wij hebben uw leenlaptop <strong style="color:#0b1734;">{{ $loan->laptop_type }}</strong> ({{ $loan->loanNumber() }}) in goede orde retour ontvangen.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Details retour {{ $loan->loanNumber() }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Type laptop</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->laptop_type }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Uitgegeven op</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->given_at->format('d-m-Y H:i') }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Status</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#16a34a; padding:4px 0;">Teruggebracht</td>
  </tr>
</table>
@endsection

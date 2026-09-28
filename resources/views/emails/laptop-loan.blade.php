@extends('emails.layout')

@section('kicker', 'Leen-laptop · Uitgiftebevestiging')
@section('title', 'Bevestiging lenen laptop')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Geachte {{ $loan->customer_name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Hierbij bevestigen wij dat u op <strong style="color:#0b1734;">{{ $loan->given_at->format('d-m-Y H:i') }}</strong> onderstaande laptop van ons in bruikleen heeft ontvangen. In de bijlage vindt u de overeenkomst als PDF.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Details bruikleen {{ $loan->loanNumber() }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Leennummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->loanNumber() }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Type laptop</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->laptop_type }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Datum uitgifte</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->given_at->format('d-m-Y H:i') }}</td>
  </tr>
  @if($loan->repair_number)
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Reparatienummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $loan->repair_number }}</td>
  </tr>
  @endif
</table>
@endsection

@extends('emails.layout')

@section('kicker', 'Apparaat · Ontvangstbevestiging')
@section('title', 'Bevestiging ontvangst apparaat')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Geachte {{ $receipt->customer_name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Hierbij ontvangt u van ons een bevestiging dat wij op <strong style="color:#0b1734;">{{ $receipt->received_at->format('d-m-Y H:i') }}</strong> uw apparaat hebben ontvangen. U ontvangt morgen een update over het probleem en de reparatiekosten.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Details apparaat</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Type</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $receipt->device_type }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Serienummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $receipt->serial_number ?: '—' }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Opmerking</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $receipt->notes ?: '—' }}</td>
  </tr>
</table>
@endsection

@section('cta_url', route('tracking.index', ['t_number' => $receipt->receiptNumber()]))
@section('cta_label', 'Bekijk status van mijn apparaat')

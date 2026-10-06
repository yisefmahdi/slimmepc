@extends('emails.layout')

@section('badge', 'Actie vereist')
@section('kicker', 'Webshop · Voorraadwaarschuwing')
@section('title', 'Licentiecodes op!')
@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Bestelling <strong>{{ $order->order_number }}</strong> is betaald, maar voor de volgende digitale producten waren <strong>geen (voldoende) licentiecodes</strong> beschikbaar. Vul de pool aan bij <em>Webshop → Licentiecodes</em>; de codes worden niet automatisch nagestuurd.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Tekorten</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  @foreach($shortages as $s)
  <tr>
    <td style="font-size:13px; font-weight:700; color:#0b1734; padding:6px 0;">{{ $s['title'] }}</td>
    <td align="right" style="font-size:13px; color:#dc2626; padding:6px 0; white-space:nowrap;">besteld {{ $s['quantity'] }} · beschikbaar {{ $s['available'] }}</td>
  </tr>
  @endforeach
  <tr>
    <td colspan="2" style="border-top:1px solid #e2e8f0; padding:0; font-size:0; line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:10px 0 0;">Klant</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:10px 0 0;">{{ $order->customer_email }}</td>
  </tr>
</table>
@endsection

@section('cta_url', $adminUrl)
@section('cta_label', 'Open bestelling')

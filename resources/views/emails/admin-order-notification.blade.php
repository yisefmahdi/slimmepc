@extends('emails.layout')

@section('badge', 'Nieuwe bestelling')
@section('kicker', 'Webshop · Adminmelding')
@section('title')Nieuwe bestelling {{ $order->order_number }}@endsection

@section('body')
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Er is een nieuwe betaalde bestelling binnengekomen:</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Bestelnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Klant</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $order->billingAddress?->fullName() ?? $order->customer_email }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Contact</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $order->customer_email }}, {{ $order->customer_phone }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Verzendmethode</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $order->shipping_method === 'pickup' ? 'Afhalen' : 'Verzending' }}</td>
  </tr>
  <tr>
    <td colspan="2" style="border-top:1px solid #e2e8f0; padding:0; font-size:0; line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:14px; font-weight:800; color:#0b1734; padding:10px 0 0;">Totaal</td>
    <td align="right" style="font-size:18px; font-weight:800; color:#2563eb; padding:10px 0 0; white-space:nowrap;">€{{ number_format($order->total_price, 2, ',', '.') }}</td>
  </tr>
</table>
@endsection

@section('cta_url', $adminUrl)
@section('cta_label', 'Bekijk bestelling')

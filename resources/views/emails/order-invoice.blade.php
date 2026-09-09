@extends('emails.layout')

@section('badge', 'Betaling gelukt')
@section('kicker', 'Webshop · Bestelbevestiging')
@section('title', 'Bestelling gelukt!')

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Beste {{ $invoice->customer_name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Bedankt voor je bestelling bij Slimme-PC! De betaling is gelukt. De factuur vind je als PDF in de bijlage van deze e-mail.</p>
@endsection

@section('card')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:12px;">Factuur {{ $invoice->invoice_number }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Factuurdatum</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $invoice->invoice_date->format('d-m-Y') }}</td>
  </tr>
  <tr>
    <td colspan="2" style="border-top:1px solid #e2e8f0; padding:0; font-size:0; line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:14px; font-weight:800; color:#0b1734; padding:10px 0 0;">Totaal (incl. btw)</td>
    <td align="right" style="font-size:18px; font-weight:800; color:#2563eb; padding:10px 0 0; white-space:nowrap;">€{{ number_format($invoice->total, 2, ',', '.') }}</td>
  </tr>
</table>
@endsection

@section('cta_url', route('account.orders.index'))
@section('cta_label', 'Bekijk mijn bestelling')

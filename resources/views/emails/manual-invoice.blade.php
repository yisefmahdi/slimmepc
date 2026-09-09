@extends('emails.layout')

@section('kicker', 'Factuur')
@section('title')Uw factuur {{ $invoice->invoice_number }}@endsection

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Geachte {{ $invoice->name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Hierbij ontvangt u in de bijlage de factuur van <strong style="color:#0b1734;">Slimme PC</strong> voor de uitgevoerde werkzaamheden. Heeft u vragen over deze factuur? Neem gerust contact met ons op.</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Factuurnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $invoice->invoice_number }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Datum</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $invoice->created_at->format('d-m-Y') }}</td>
  </tr>
  <tr>
    <td colspan="2" style="border-top:1px solid #e2e8f0; padding:0; font-size:0; line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:14px; font-weight:800; color:#0b1734; padding:10px 0 0;">Bedrag (incl. {{ $invoice->tax_percentage }}% btw)</td>
    <td align="right" style="font-size:18px; font-weight:800; color:#2563eb; padding:10px 0 0; white-space:nowrap;">€{{ number_format($invoice->total, 2, ',', '.') }}</td>
  </tr>
</table>
@endsection

@section('cta_url', 'https://maps.google.com/?q=Slimme-PC+Apeldoorn')
@section('cta_label', 'Geef uw beoordeling')

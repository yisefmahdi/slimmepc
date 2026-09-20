@extends('emails.layout')

@section('kicker', 'Lidmaatschap')
@section('title')Welkom als lid bij Slimme-PC!@endsection

@section('body')
<p style="margin:0 0 8px; font-size:14px; line-height:1.7; color:#3E547F;">Beste {{ $membership->name }},</p>
<p style="margin:0; font-size:14px; line-height:1.7; color:#3E547F;">Bedankt voor je aanmelding! Je bent nu officieel lid van <strong style="color:#0b1734;">Slimme-PC</strong>. Je lidmaatschap loopt van <strong style="color:#0b1734;">{{ $membership->start_date?->format('d-m-Y') }}</strong> tot <strong style="color:#0b1734;">{{ $membership->end_date?->format('d-m-Y') }}</strong>. In de bijlage vind je de factuur van je betaling.</p>
@endsection

@section('card')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Klantnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $membership->klantnummer }}</td>
  </tr>
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Looptijd</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $membership->start_date?->format('d-m-Y') }} – {{ $membership->end_date?->format('d-m-Y') }}</td>
  </tr>
  @if($invoice)
  <tr>
    <td style="font-size:13px; color:#64748b; padding:4px 0;">Factuurnummer</td>
    <td align="right" style="font-size:13px; font-weight:700; color:#0b1734; padding:4px 0;">{{ $invoice->invoice_number }}</td>
  </tr>
  @endif
  <tr>
    <td colspan="2" style="border-top:1px solid #e2e8f0; padding:0; font-size:0; line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:14px; font-weight:800; color:#0b1734; padding:10px 0 0;">Bedrag betaald (incl. btw)</td>
    <td align="right" style="font-size:18px; font-weight:800; color:#2563eb; padding:10px 0 0; white-space:nowrap;">€{{ number_format($membership->total, 2, ',', '.') }}</td>
  </tr>
</table>
@endsection

@section('cta_url', route('home'))
@section('cta_label', 'Naar Slimme-PC')

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

@php
    $digitalItems = collect();
    try {
        $orderForMail = $invoice->order()->with(['items.product', 'items.licenseCodes'])->first();
        $digitalItems = $orderForMail ? $orderForMail->items->filter(fn ($it) => (bool) ($it->product?->is_digital)) : collect();
    } catch (\Throwable $e) {
        $digitalItems = collect();
    }
@endphp

@if($digitalItems->isNotEmpty())
@section('digital')
<div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin:20px 0 12px;">Jouw digitale producten</div>
@foreach($digitalItems as $dItem)
@php $dProduct = $dItem->product; @endphp
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #e2e8f0; border-radius:12px; margin-bottom:12px;">
  <tr>
    <td style="padding:16px 18px;">
      <div style="font-size:14px; font-weight:800; color:#0b1734;">{{ $dItem->product_name }} <span style="display:inline-block; background-color:#ecfeff; color:#0e7490; font-size:10px; font-weight:800; padding:2px 10px; border-radius:999px; margin-left:6px;">Digitaal</span></div>
      @if($dItem->licenseCodes->isNotEmpty())
      <div style="font-size:12px; font-weight:700; color:#64748b; margin:12px 0 6px;">Licentiecode{{ $dItem->licenseCodes->count() > 1 ? 's' : '' }}</div>
      @foreach($dItem->licenseCodes as $lc)
      <div style="font-family:ui-monospace, Menlo, Consolas, monospace; font-size:13px; font-weight:700; color:#0b1734; background-color:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:8px 12px; margin-bottom:6px;">{{ $lc->code }}</div>
      @endforeach
      @endif
      @php
        $links = [];
        if ($dProduct?->download_32bit_url) $links[] = ['label' => 'Download 32-bit versie', 'url' => $dProduct->download_32bit_url];
        if ($dProduct?->download_64bit_url) $links[] = ['label' => 'Download 64-bit versie', 'url' => $dProduct->download_64bit_url];
        if ($dProduct?->manual_url) $links[] = ['label' => 'Installatiehandleiding', 'url' => $dProduct->manual_url];
      @endphp
      @if(!empty($links))
      <div style="font-size:12px; font-weight:700; color:#64748b; margin:12px 0 6px;">Downloadlinks</div>
      @foreach($links as $lnk)
      <div style="margin-bottom:8px;">
        <a href="{{ $lnk['url'] }}" style="display:inline-block; background-color:#0757ef; color:#ffffff; font-size:13px; font-weight:700; text-decoration:none; padding:10px 20px; border-radius:10px;">{{ $lnk['label'] }} &rarr;</a>
      </div>
      @endforeach
      @endif
      <div style="font-size:11px; color:#94a3b8; margin-top:8px;">Bewaar deze e-mail goed — hier staan je licentiegegevens.</div>
    </td>
  </tr>
</table>
@endforeach
@endsection
@endif

@section('cta_url', route('account.orders.index'))
@section('cta_label', 'Bekijk mijn bestelling')

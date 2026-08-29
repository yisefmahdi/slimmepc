<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Factuur {{ $invoice->invoice_number }}</title>
<style>
    @page { margin: 28px 32px; }
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; margin: 0; }
    .header { width: 100%; margin-bottom: 18px; }
    .header-right { text-align: right; }
    .brand { font-size: 18px; font-weight: 800; color: #2563eb; margin: 0; }
    .header-right p { margin: 1px 0; font-size: 9px; color: #475569; line-height: 1.4; }
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0 16px; }
    .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .meta td { padding: 2px 0; font-size: 10.5px; vertical-align: top; }
    .meta .label { font-weight: 700; color: #0f172a; width: 118px; }
    .meta .value { color: #334155; }
    .price-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .price-table th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 6px; font-size: 9.5px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .02em; }
    .price-table td { border: 1px solid #e2e8f0; padding: 10px 6px; text-align: center; font-size: 11px; color: #0f172a; }
    .price-table td.amount { font-weight: 600; }
    .footer { margin-top: 28px; text-align: center; font-size: 8.5px; color: #94a3b8; line-height: 1.6; }
    .footer strong { color: #ef4444; font-weight: 700; }
    .small { font-size: 9px; color: #64748b; }
</style>
</head>
<body>

@php
    $logoPath = public_path('assets/img/logo.png');
    if (!file_exists($logoPath)) {
        $logoPath = public_path('assets/img/landing/logo.png');
    }
@endphp

<table class="header">
<tr>
    <td style="vertical-align: top; width: 130px; padding-right: 16px;">
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" style="width: 120px; height: auto; display: block;" alt="Slimme-PC">
        @else
            <div style="width: 110px; height: 38px; background: #2563eb; color: white; font-weight: 800; font-size: 13px; text-align: center; line-height: 38px; border-radius: 6px;">Slimme-PC</div>
        @endif
    </td>
    <td class="header-right">
        <p class="brand">Slimme-PC</p>
        <p>asselsestraat 24 apeldoorn, 7311EL Apeldoorn</p>
        <p>info@slimme-pc.nl</p>
        <p>Tel: 0617100945 / 0557850547</p>
        <p>KVK: 82348478</p>
        <p>BTW: NL003670746B07</p>
        <p>IBAN: NL55INGB0009592427</p>
    </td>
</tr>
</table>

<hr class="divider">

<table class="meta">
    <tr><td class="label">Factuurnummer:</td><td class="value">{{ $invoice->invoice_number }}</td></tr>
    <tr><td class="label">Datum:</td><td class="value">{{ $invoice->created_at->format('d-m-Y') }}</td></tr>
    <tr><td class="label">Klant:</td><td class="value">{{ $invoice->name }}</td></tr>
    <tr><td class="label">Email:</td><td class="value">{{ $invoice->email }}</td></tr>
    <tr><td class="label">Apparaat:</td><td class="value">{{ $invoice->device_info ?: '—' }}</td></tr>
    <tr><td class="label">Omschrijving:</td><td class="value">{{ $invoice->description ?: '—' }}</td></tr>
</table>

<table class="price-table">
    <thead>
        <tr>
            <th>Subtotaal</th>
            <th>BTW ({{ $invoice->tax_percentage }}%)</th>
            <th>Totaal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="amount">€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
            <td class="amount">€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td>
            <td class="amount">€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Bedankt voor uw vertrouwen in <strong>Slimme-PC</strong>.<br>
    Deze factuur is automatisch gegenereerd. Neem contact met ons op bij vragen.
</div>

</body>
</html>

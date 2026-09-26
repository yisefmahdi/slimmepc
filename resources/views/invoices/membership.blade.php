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
    .brand { font-size: 26px; font-weight: 800; color: #000; margin: 0; }
    .header-right p { margin: 1px 0; font-size: 9px; color: #000; line-height: 1.4; }
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0 16px; }
    .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .meta td { padding: 2px 0; font-size: 10.5px; vertical-align: top; }
    .meta .label { font-weight: 700; color: #0f172a; width: 132px; }
    .meta .value { color: #334155; }
    .price-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .price-table th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 6px; font-size: 9.5px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .02em; }
    .price-table td { border: 1px solid #e2e8f0; padding: 10px 6px; text-align: center; font-size: 11px; color: #0f172a; }
    .price-table td.left { text-align: left; }
    .price-table td.amount { font-weight: 600; }
    .totals { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .totals td { padding: 4px 6px; font-size: 11px; color: #334155; border: none; }
    .totals td.right { text-align: right; white-space: nowrap; }
    .totals tr.grand td { font-weight: 800; font-size: 12.5px; color: #0f172a; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    .footer { margin-top: 28px; text-align: center; font-size: 8.5px; color: #94a3b8; line-height: 1.6; }
    .footer strong { color: #ef4444; font-weight: 700; }
</style>
</head>
<body>

<table class="header">
<tr>
    <td style="vertical-align: top;">
        <p class="brand">Slimme-PC</p>
        <p style="font-size: 12px; font-weight: 700; color: #0f172a; margin: 2px 0 0;">Lidmaatschapsfactuur</p>
    </td>
    <td class="header-right">
        <p class="brand">Slimme-PC</p>
        <p><strong>Adres:</strong> Asselestraat 24, 7311EL Apeldoorn</p>
        <p><strong>Email:</strong> info@slimme-pc.nl</p>
        <p><strong>Tel:</strong> 0617100945 / 0557850547</p>
        <p><strong>KVK:</strong> 82348478</p>
        <p><strong>BTW:</strong> NL003670746B07</p>
        <p><strong>IBAN:</strong> NL55INGB0009592427</p>
    </td>
</tr>
</table>

<hr class="divider">

<table class="meta">
    <tr><td class="label">Factuurnummer:</td><td class="value">{{ $invoice->invoice_number }}</td></tr>
    <tr><td class="label">Datum:</td><td class="value">{{ $invoice->invoice_date->format('d-m-Y') }}</td></tr>
    <tr><td class="label">Klantnummer:</td><td class="value">{{ $membership->klantnummer }}</td></tr>
    <tr><td class="label">Klant:</td><td class="value">{{ $membership->name }}</td></tr>
    <tr><td class="label">Email:</td><td class="value">{{ $membership->customer_email }}</td></tr>
    <tr><td class="label">Telefoon:</td><td class="value">{{ $membership->customer_phone ?: '—' }}</td></tr>
    <tr><td class="label">Adres:</td><td class="value">{{ trim($membership->customer_address.' '.$membership->postcode.' '.$membership->city) ?: '—' }}</td></tr>
    <tr><td class="label">Lidmaatschap:</td><td class="value">{{ $membership->start_date->format('d-m-Y') }} t/m {{ $membership->end_date->format('d-m-Y') }}</td></tr>
    <tr><td class="label">Betaalmethode:</td><td class="value">{{ $membership->payment_method ?: '—' }}</td></tr>
</table>

<table class="price-table">
    <thead>
        <tr>
            <th style="text-align: left;">Omschrijving</th>
            <th>Prijs (incl.)</th>
            <th>Totaal (incl.)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="left">Slimme-PC lidmaatschap (1 jaar, {{ $membership->customer_type === 'business' ? 'zakelijk' : 'particulier' }})<br><span style="font-size:9px; color:#64748b;">{{ $membership->start_date->format('d-m-Y') }} t/m {{ $membership->end_date->format('d-m-Y') }}</span></td>
            <td class="amount">€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
            <td class="amount">€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotaal (excl. btw)</td><td class="right">€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
    <tr><td>BTW ({{ number_format($invoice->tax_percentage, 0) }}%)</td><td class="right">€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td></tr>
    <tr class="grand"><td>Totaal (incl. btw)</td><td class="right">€ {{ number_format($invoice->total, 2, ',', '.') }}</td></tr>
</table>

<div class="footer">
    Bedankt voor uw vertrouwen in <strong>Slimme-PC</strong>.<br>
    Deze factuur is automatisch gegenereerd. Neem contact met ons op bij vragen.
</div>

</body>
</html>

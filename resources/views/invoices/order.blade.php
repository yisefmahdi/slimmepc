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
    .price-table td.left { text-align: left; }
    .price-table td.amount { font-weight: 600; }
    .totals { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .totals td { padding: 4px 6px; font-size: 11px; color: #334155; border: none; }
    .totals td.right { text-align: right; white-space: nowrap; }
    .totals tr.grand td { font-weight: 800; font-size: 12.5px; color: #0f172a; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    .footer { margin-top: 28px; text-align: center; font-size: 8.5px; color: #94a3b8; line-height: 1.6; }
    .footer strong { color: #ef4444; font-weight: 700; }
    .small { font-size: 9px; color: #64748b; }
</style>
</head>
<body>

<table class="header">
<tr>
    <td style="vertical-align: top; width: 140px; padding-right: 16px;">
        @php
            $candidates = [];
            $cmsLogo = \App\Support\Cms::page('home')['header']['logo_image'] ?? null;
            if ($cmsLogo) { $candidates[] = public_path($cmsLogo); }
            $candidates[] = public_path('assets/img/logo.png');
            $candidates[] = public_path('assets/img/landing/logo.png');
            $candidates[] = public_path('assets/img/logo.webp');
            $candidates[] = public_path('assets/img/landing/logo.webp');
            $logoSrc = '';
            foreach ($candidates as $cand) {
                if ($cand && file_exists($cand) && is_file($cand)) {
                    $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                    // Voor PDF: converteer webp naar png zodat dompdf geen imagecreatefromwebp nodig heeft
                    if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
                        $img = @imagecreatefromwebp($cand);
                        if ($img) {
                            ob_start();
                            imagepng($img);
                            $pngData = ob_get_clean();
                            imagedestroy($img);
                            $logoSrc = 'data:image/png;base64,'.base64_encode($pngData);
                            break;
                        }
                        // als conversie faalt, probeer volgende kandidaat
                        continue;
                    }
                    // webp zonder GD support -> skip
                    if ($ext === 'webp') { continue; }
                    $mime = match($ext) {
                        'svg' => 'image/svg+xml',
                        'jpg','jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        default => mime_content_type($cand) ?: 'image/png',
                    };
                    $logoSrc = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($cand));
                    break;
                }
            }
        @endphp
        @if($logoSrc)
            <img src="{{ $logoSrc }}" style="width: 120px; height: auto; display: block;" alt="Slimme-PC">
        @else
            <div style="width: 120px; height: 40px; background: #000; color: white; font-weight: bold; text-align: center; line-height: 40px; border-radius: 6px;">Slimme-PC</div>
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
    <tr><td class="label">Datum:</td><td class="value">{{ $invoice->invoice_date->format('d-m-Y') }}</td></tr>
    <tr><td class="label">Bestelling:</td><td class="value">{{ $invoice->order?->order_number ?? '—' }}</td></tr>
    <tr><td class="label">Klant:</td><td class="value">{{ $invoice->customer_name }}</td></tr>
    <tr><td class="label">Email:</td><td class="value">{{ $invoice->customer_email }}</td></tr>
    <tr><td class="label">Telefoon:</td><td class="value">{{ $invoice->customer_phone ?: '—' }}</td></tr>
    <tr><td class="label">Adres:</td><td class="value">{{ trim($invoice->street_address.' '.$invoice->postal_code.' '.$invoice->city) ?: '—' }}</td></tr>
    <tr><td class="label">Betaalmethode:</td><td class="value">{{ $invoice->payment_method ?: '—' }}</td></tr>
</table>

<table class="price-table">
    <thead>
        <tr>
            <th style="text-align: left;">Product</th>
            <th>Aantal</th>
            <th>Prijs (incl.)</th>
            <th>Totaal (incl.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->order?->items ?? [] as $item)
            <tr>
                <td class="left">{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td class="amount">€ {{ number_format($item->product_price, 2, ',', '.') }}</td>
                <td class="amount">€ {{ number_format($item->total_price, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotaal (excl. btw)</td><td class="right">€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
    @if((float) $invoice->discount_amount > 0)
        <tr><td>Korting</td><td class="right">−€ {{ number_format($invoice->discount_amount, 2, ',', '.') }}</td></tr>
    @endif
    <tr><td>Verzending</td><td class="right">@if((float) $invoice->shipping_cost > 0) € {{ number_format($invoice->shipping_cost, 2, ',', '.') }} @else Gratis @endif</td></tr>
    <tr><td>BTW ({{ number_format($invoice->tax_percentage, 0) }}%)</td><td class="right">€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td></tr>
    <tr class="grand"><td>Totaal (incl. btw)</td><td class="right">€ {{ number_format($invoice->total, 2, ',', '.') }}</td></tr>
</table>

<div class="footer">
    Bedankt voor uw vertrouwen in <strong>Slimme-PC</strong>.<br>
    Deze factuur is automatisch gegenereerd. Neem contact met ons op bij vragen.
</div>

</body>
</html>

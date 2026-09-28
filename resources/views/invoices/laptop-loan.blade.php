<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Overeenkomst {{ $loan->loanNumber() }}</title>
<style>
    @page { margin: 28px 32px; }
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; margin: 0; }
    .header { width: 100%; margin-bottom: 18px; }
    .header-right { text-align: right; }
    .brand { font-size: 20px; font-weight: 800; color: #2563eb; margin: 0; }
    .header-right p { margin: 1px 0; font-size: 9px; color: #000; line-height: 1.4; }
    .header-right p.brand { font-size: 20px; font-weight: 800; color: #2563eb; margin: 0; }
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
    .terms { margin: 14px 0 0; padding-left: 16px; font-size: 9.5px; color: #475569; line-height: 1.7; }
    .terms li { margin-bottom: 3px; }
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
            <img src="{{ $logoSrc }}" style="width: 90px; height: auto; display: block;" alt="Slimme-PC">
        @else
            <div style="width: 90px; height: 32px; background: #000; color: white; font-weight: bold; text-align: center; line-height: 32px; border-radius: 6px;">Slimme-PC</div>
        @endif
    </td>
    <td class="header-right">
        <p class="brand">Slimme-PC</p>
        <p><strong>Adres:</strong> Asselsestraat 24, 7311EL Apeldoorn</p>
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
    <tr><td class="label">Leennummer:</td><td class="value">{{ $loan->loanNumber() }}</td></tr>
    <tr><td class="label">Datum uitgifte:</td><td class="value">{{ $loan->given_at->format('d-m-Y H:i') }}</td></tr>
    <tr><td class="label">Status:</td><td class="value">{{ $loan->status === 'teruggebracht' ? 'Teruggebracht' : 'Uitgeleend' }}</td></tr>
    <tr><td class="label">Klant:</td><td class="value">{{ $loan->customer_name }}</td></tr>
    <tr><td class="label">Email:</td><td class="value">{{ $loan->customer_email }}</td></tr>
    <tr><td class="label">Telefoon:</td><td class="value">{{ $loan->phone ?: '—' }}</td></tr>
    <tr><td class="label">Adres:</td><td class="value">{{ trim($loan->address.' '.$loan->postcode.' '.$loan->city) ?: '—' }}</td></tr>
    @if($loan->repair_number)
    <tr><td class="label">Reparatienummer:</td><td class="value">{{ $loan->repair_number }}</td></tr>
    @endif
</table>

<table class="price-table">
    <thead>
        <tr>
            <th style="text-align: left;">Omschrijving</th>
            <th>Datum uitgifte</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="left">Bruikleen laptop — {{ $loan->laptop_type }}</td>
            <td class="amount">{{ $loan->given_at->format('d-m-Y') }}</td>
        </tr>
    </tbody>
</table>

<ol class="terms">
    <li>De laptop blijft eigendom van Slimme-PC en wordt uitsluitend in bruikleen gegeven.</li>
    <li>De klant levert de laptop in dezelfde staat retour als bij uitgifte.</li>
    <li>Bij schade of vermissing is de klant aansprakelijk voor de herstel- of vervangingskosten.</li>
    <li>Deze overeenkomst is automatisch gegenereerd naar aanleiding van de uitgifte.</li>
</ol>

<div class="footer">
    Bedankt voor uw vertrouwen in <strong>Slimme-PC</strong>.<br>
    Deze overeenkomst is automatisch gegenereerd. Neem contact met ons op bij vragen.
</div>

</body>
</html>

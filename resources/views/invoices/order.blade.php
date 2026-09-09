<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { margin-bottom: 20px; }
        .header h1 { font-size: 22px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .totals td { border: none; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factuur {{ $invoice->invoice_number }}</h1>
        <p>Slimme-PC &middot; Apeldoorn &middot; info@slimme-pc.nl</p>
        <p>Factuurdatum: {{ $invoice->invoice_date->format('d-m-Y') }} &middot; Bestelling: {{ $invoice->order?->order_number }}</p>
    </div>

    <p>
        <strong>{{ $invoice->customer_name }}</strong><br>
        {{ $invoice->street_address }}<br>
        {{ $invoice->postal_code }} {{ $invoice->city }}<br>
        {{ $invoice->customer_email }} &middot; {{ $invoice->customer_phone }}
    </p>

    <table>
        <thead>
            <tr><th>Product</th><th>Aantal</th><th class="right">Prijs (incl.)</th><th class="right">Totaal (incl.)</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->order?->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="right">&euro;{{ number_format($item->product_price, 2, ',', '.') }}</td>
                    <td class="right">&euro;{{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotaal (excl. btw)</td><td class="right">&euro;{{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
        @if((float) $invoice->discount_amount > 0)
            <tr><td>Korting</td><td class="right">-&euro;{{ number_format($invoice->discount_amount, 2, ',', '.') }}</td></tr>
        @endif
        <tr><td>Verzending</td><td class="right">@if((float) $invoice->shipping_cost > 0) &euro;{{ number_format($invoice->shipping_cost, 2, ',', '.') }} @else Gratis @endif</td></tr>
        <tr><td>BTW ({{ number_format($invoice->tax_percentage, 0) }}%)</td><td class="right">&euro;{{ number_format($invoice->tax_amount, 2, ',', '.') }}</td></tr>
        <tr><td><strong>Totaal (incl. btw)</strong></td><td class="right"><strong>&euro;{{ number_format($invoice->total, 2, ',', '.') }}</strong></td></tr>
    </table>

    <p>Betaalmethode: {{ $invoice->payment_method }} &middot; Status: betaald</p>
</body>
</html>

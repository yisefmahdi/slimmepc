<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uw apparaat is gerepareerd!</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
        .header { background-color: #2563eb; padding: 32px; text-align: center; color: white; }
        .content { padding: 32px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 24px; }
        h1 { margin: 0; font-size: 24px; }
        p { font-size: 16px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Goed nieuws!</h1>
        </div>
        <div class="content">
            <p>Beste {{ $receipt->customer_name }},</p>
            <p>We zijn blij u te kunnen informeren dat de reparatie van uw <strong>{{ $receipt->device_type }}</strong> is voltooid.</p>
            <p>Uw apparaat ligt nu klaar om te worden opgehaald bij onze vestiging.</p>
            <div style="text-align: center;">
                <a href="{{ route('tracking.index', ['t_number' => $receipt->receiptNumber()]) }}" class="btn" style="background-color: #2563eb; color: #ffffff !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Bekijk status van mijn apparaat</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SlimmePC. Alle rechten voorbehouden.
        </div>
    </div>
</body>
</html>

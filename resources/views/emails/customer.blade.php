<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectText }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; color: #333; margin: 0; padding: 0; }
        .container { background: #ffffff; max-width: 620px; margin: 40px auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .top-bar { background: #0b5394; padding: 15px; text-align: center; color: #ffffff; font-size: 20px; font-weight: bold; }
        .content { padding: 30px; font-size: 15px; line-height: 1.7; }
        .button { display: inline-block; padding: 12px 20px; background: linear-gradient(90deg, #007bff, #0056b3); color: #fff !important; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 25px; }
        .company-info { background: #f8f8f8; padding: 20px; text-align: center; font-size: 12px; color: #555; line-height: 1.6; border-top: 1px solid #eee; }
        .company-info div { margin: 5px 0; }
        .footer { padding: 10px; text-align: center; font-size: 11px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">Slimme-PC</div>
        <div class="content">
            <h4>Geachte Klant,</h4>
            {!! nl2br(e($bodyText)) !!}

            @if($type === 'maintenance')
                <div style="text-align: center;">
                    <a href="https://slimme-pc.nl/afspraak" class="button">➜ Afspraak maken</a>
                </div>
                <br>
                <p>Vragen? Wij staan voor je klaar.</p>
                <p>Met vriendelijke groet,</p>
                <p>Het Slimme-PC team</p>
            @endif
        </div>
        <div class="company-info">
            <div>📍 Vankinsbergenstraat 6E, 7311BM Apeldoorn</div>
            <div>📧 info@slimme-pc.nl</div>
            <div>☎️ 0617100945</div>
            <div>KvK: 82384878 | Btw: NL003670746B07</div>
        </div>
        <div class="footer">Dit bericht is automatisch gegenereerd vanuit het systeem. Heeft u vragen? Neem gerust contact met ons op.</div>
    </div>
</body>
</html>

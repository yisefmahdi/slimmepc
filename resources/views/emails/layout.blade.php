<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Slimme-PC</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:Arial, Helvetica, sans-serif;">
@php
    $brand = $mailBrand ?? [];
    // Live URL naar het CMS-logo (zelfde waarde als de website-header).
    // Geen CID-embed: de URL is altijd actueel bij CMS-wijzigingen.
    // Vereist een publiek bereikbare APP_URL op live (lokaal tonen
    // externe clients zoals Gmail geen localhost-afbeeldingen).
    $logoImg = $brand['header']['logo_image'] ?? 'assets/img/landing/logo.webp';
    $logoSrc = str_starts_with($logoImg, 'http') ? $logoImg : asset($logoImg);
    $logoText = $brand['header']['logo_text'] ?? 'SLIMME-PC';
    $tagline = $brand['header']['tagline'] ?? '';
    $contactRows = $brand['footer']['contact'] ?? [];
    $copyright = $brand['footer']['copyright'] ?? '© Slimme-PC. Alle rechten voorbehouden.';
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td align="center" style="background-color:#f8fafc; padding:32px 12px;">

      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="640" style="max-width:640px; width:100%;">

        {{-- SITE HEADER (donkerblauw gradient, zoals navbar) --}}
        <tr>
          <td bgcolor="#1e3a8a" style="background-color:#1e3a8a; background:linear-gradient(90deg,#172554 0%,#1e40af 50%,#2563eb 100%); border-radius:16px 16px 0 0; padding:22px 28px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
              <tr>
                <td width="56">
                  <div style="background-color:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); border-radius:12px; width:52px; height:52px; text-align:center;">
                    <a href="{{ config('app.url') }}" style="text-decoration:none;"><img src="{{ $logoSrc }}" alt="{{ $logoText }}" width="44" height="44" style="display:inline-block; border:0; width:44px; height:44px; object-fit:contain; margin-top:3px;"></a>
                  </div>
                </td>
                <td style="padding-left:12px;">
                  <div style="font-size:19px; font-weight:800; color:#ffffff; line-height:1.2;">{{ $logoText }}</div>
                  @if($tagline)
                  <div style="font-size:11px; color:#bfdbfe; margin-top:2px;">{{ $tagline }}</div>
                  @endif
                </td>
                @hasSection('badge')
                <td align="right" style="vertical-align:middle;">
                  <span style="display:inline-block; background-color:#84cc16; color:#0b1734; font-size:11px; font-weight:800; padding:6px 14px; border-radius:999px; white-space:nowrap;">@yield('badge')</span>
                </td>
                @endif
              </tr>
            </table>
          </td>
        </tr>

        {{-- BODY --}}
        <tr>
          <td style="background-color:#ffffff; padding:28px 32px 28px;">
            @hasSection('kicker')
            <div style="font-size:11px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:#2563eb; margin-bottom:10px;">@yield('kicker')</div>
            @endif
            <h1 style="margin:0 0 12px; font-size:26px; font-weight:800; color:#020617; line-height:1.25;">@yield('title')</h1>

            @yield('body')

            @hasSection('card')
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #e2e8f0; border-radius:12px; margin-top:4px;">
              <tr>
                <td style="padding:18px 20px;">@yield('card')</td>
              </tr>
            </table>
            @endif

            @hasSection('cta_url')
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
              <tr>
                <td align="center" style="padding:24px 0 4px;">
                  <a href="@yield('cta_url')" style="display:inline-block; background-color:#0757ef; background:linear-gradient(90deg,#0647ca 0%,#0757ef 50%,#2877ff 100%); color:#ffffff; font-size:14px; font-weight:700; text-decoration:none; padding:14px 36px; border-radius:12px;">@yield('cta_label')</a>
                </td>
              </tr>
            </table>
            @endif
          </td>
        </tr>

        {{-- SITE FOOTER (lichtblauw gradient, zoals website-footer) --}}
        <tr>
          <td bgcolor="#d7e8ff" style="background-color:#d7e8ff; background:linear-gradient(180deg,#edf5ff 0%,#83b9eb 60%,#d7e8ff 100%); border-radius:0 0 16px 16px; padding:24px 32px;">
            <div style="font-size:14px; font-weight:800; color:#0b1734;">{{ $logoText }}</div>
            @if(!empty($contactRows))
            <div style="font-size:12px; line-height:1.9; color:#334155; padding-top:8px;">
              @foreach($contactRows as $row)
                {{ $row['label'] ?? '' }}: {!! nl2br(e($row['value'] ?? '')) !!}<br>
              @endforeach
            </div>
            @endif
            <div style="border-top:1px solid rgba(11,23,52,0.12); margin-top:12px; padding-top:12px; font-size:11px; color:#475569;">{{ $copyright }}</div>
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>
</body>
</html>

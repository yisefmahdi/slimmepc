<x-mail::message>
# 🧾 Uw Factuur — {{ $invoice->invoice_number }}

Geachte {{ $invoice->name }},

Hierbij ontvangt u in de bijlage de factuur van **Slimme PC** voor de uitgevoerde werkzaamheden.

<x-mail::panel>
**Factuurnummer:** {{ $invoice->invoice_number }}<br>
**Datum:** {{ $invoice->created_at->format('d-m-Y') }}<br>
**Bedrag:** € {{ number_format($invoice->total, 2, ',', '.') }} (incl. {{ $invoice->tax_percentage }}% BTW)
</x-mail::panel>

We zouden het zeer op prijs stellen als u uw ervaring met onze service wilt delen. U kunt dit eenvoudig doen via onze Google pagina:

<x-mail::button :url="'https://maps.google.com/?q=Slimme-PC+Apeldoorn'">
⭐ Geef uw beoordeling
</x-mail::button>

Heeft u vragen over deze factuur? Neem gerust contact met ons op.

Met vriendelijke groet,<br>
**Het Slimme PC Team**<br>
[www.slimme-pc.nl](https://www.slimme-pc.nl)

---
📧 Slimmepc@gmail.com | 📞 0617100945 / 0557850547

<small style="color:#94a3b8">Deze factuur is automatisch gegenereerd. Bewaar deze e-mail voor uw administratie.</small>
</x-mail::message>

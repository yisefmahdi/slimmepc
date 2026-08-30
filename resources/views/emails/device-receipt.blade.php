<x-mail::message>
# 📦 Bevestiging Ontvangst Apparaat

Geachte {{ $receipt->customer_name }},

Hierbij ontvangt u van ons een bevestiging dat wij op **{{ $receipt->received_at->format('d-m-Y H:i') }}** uw apparaat hebben ontvangen.

## Details Apparaat

<x-mail::table>
| Type | {{ $receipt->device_type }} |
| :--- | :--- |
| Serienummer | {{ $receipt->serial_number ?: '—' }} |
| Opmerking | {{ $receipt->notes ?: '—' }} |
</x-mail::table>

U ontvangt morgen een update over het probleem en de reparatiekosten.

<x-mail::button :url="route('tracking.index', ['t_number' => $receipt->receiptNumber()])">
🔍 Bekijk status van mijn apparaat
</x-mail::button>

Met vriendelijke groet,<br>
**Het Slimme-PC Team**

---
📧 Slimmepc@gmail.com | 📞 0617100945 / 0557850547
</x-mail::message>

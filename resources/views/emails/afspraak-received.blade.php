<x-mail::message>
# Bedankt voor uw aanvraag aan huis

Beste {{ $submission->name }},

We hebben uw aanvraag voor een **afspraak aan huis** goed ontvangen. Hieronder vindt u een overzicht van uw gegevens.

| Gegevens |  |
| --- | --- |
| Aanvraagnummer | **{{ $submission->afspraak_number }}** |
| Apparaat | {{ $submission->device }} |
| Gewenste datum | {{ $submission->preferred_date->format('d-m-Y') }} |
| Gewenst tijdstip | {{ $submission->preferred_time }} |
| Adres | {{ $submission->street }} {{ $submission->house_number }}, {{ $submission->postcode }} {{ $submission->city }} |

**Uw omschrijving:**
{{ $submission->problem }}

We nemen zo spoedig mogelijk (binnen 24 uur) contact met u op om de afspraak te bevestigen. Heeft u ondertussen vragen? Antwoord dan gerust op deze e-mail.

Met vriendelijke groet,

**Slimme-PC**
<x-mail::button :url="config('app.url')">
Naar de website
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>

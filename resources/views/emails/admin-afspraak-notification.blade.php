<x-mail::message>
# Nieuwe afspraak-aan-huis aanvraag

Er is een nieuwe aanvraag voor een **afspraak aan huis** binnengekomen.

| Velden | Inhoud |
| --- | --- |
| Aanvraagnummer | **{{ $submission->afspraak_number }}** |
| Naam | {{ $submission->name }} |
| E-mail | {{ $submission->email }} |
| Telefoon | {{ $submission->phone }} |
| Adres | {{ $submission->street }} {{ $submission->house_number }}, {{ $submission->postcode }} {{ $submission->city }} |
| Apparaat | {{ $submission->device }} |
| Gewenste datum | {{ $submission->preferred_date->format('d-m-Y') }} |
| Gewenst tijdstip | {{ $submission->preferred_time }} |
| Status | {{ $submission->status }} |

**Omschrijving probleem:**
{{ $submission->problem }}

<x-mail::button :url="config('app.url') . '/admin/afspraak-aanvragen/' . $submission->id">
Bekijk in admin
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>

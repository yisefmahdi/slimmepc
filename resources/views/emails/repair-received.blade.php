<x-mail::message>
# Bedankt, {{ $submission->name }}!

We hebben je reparatieaanvraag ontvangen (aanmeldnummer **{{ $submission->repair_number }}**). We nemen zo snel mogelijk contact met je op, meestal binnen één werkdag.

**Samenvatting van je aanvraag:**

- **Aanmeldnummer:** {{ $submission->repair_number }}
- **Apparaat:** {{ $submission->device }}
- **Merk:** {{ $submission->brand }}
- **Model:** {{ $submission->model }}
- **Serienummer:** {{ $submission->serial ?: 'Niet opgegeven' }}
- **Probleem:** {{ implode(', ', (array) $submission->problems) }}
- **Omschrijving:** {{ $submission->description }}
- **Belangrijke gegevens:** {{ $submission->data_importance }}
- **Eerder geopend:** {{ $submission->opened_before }}
- **Naam:** {{ $submission->name }}
- **E-mail:** {{ $submission->email }}
- **Telefoon:** {{ $submission->phone }}
- **Postcode:** {{ $submission->postcode }}
- **Vervolg:** {{ $submission->delivery_method }}
- **Contactvoorkeur:** {{ $submission->contact_preference }}

Houd deze e-mail bij de hand. Je kunt direct op dit bericht reageren en je antwoord komt dan automatisch bij ons terecht.

Met vriendelijke groet,<br>
**Slimme-PC**
</x-mail::message>

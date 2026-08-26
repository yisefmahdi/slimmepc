<x-mail::message>
# Nieuwe reparatieaanvraag

Er is zojuist een nieuwe reparatieaanvraag binnengekomen via het aanmeldformulier op de website.

**Van:** {{ $submission->name }} ({{ $submission->email }}, {{ $submission->phone }})

- **Aanmeldnummer:** {{ $submission->repair_number }}
- **Apparaat:** {{ $submission->device }}
- **Merk:** {{ $submission->brand }}
- **Model:** {{ $submission->model }}
- **Serienummer:** {{ $submission->serial ?: '—' }}
- **Probleem:** {{ implode(', ', (array) $submission->problems) }}
- **Omschrijving:** {{ $submission->description }}
- **Belangrijke gegevens:** {{ $submission->data_importance }}
- **Eerder geopend:** {{ $submission->opened_before }}
- **Vervolg:** {{ $submission->delivery_method }}
- **Contactvoorkeur:** {{ $submission->contact_preference }}
- **Ontvangen op:** {{ $submission->created_at->format('d-m-Y H:i') }}

<x-mail::button :url="$inboxUrl">
Open in dashboard
</x-mail::button>

Met vriendelijke groet,<br>
**Slimme-PC**
</x-mail::message>

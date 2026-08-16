<x-mail::message>
# Nieuwe contactaanvraag

Er is zojuist een nieuw bericht binnengekomen via het contactformulier op de website.

**Van:**

- **Naam:** {{ $submission->name }}
- **E-mail:** {{ $submission->email }}
- **Telefoon:** {{ $submission->phone ?: '—' }}

**Aanvraag:**

- **Onderwerp:** {{ $submission->subject }}
- **Type:** {{ $submission->request_type }}
- **Bericht:** {{ $submission->message }}
- **Ontvangen op:** {{ $submission->created_at->format('d-m-Y H:i') }}

<x-mail::button :url="$inboxUrl">
Open in dashboard
</x-mail::button>

Met vriendelijke groet,<br>
**Slimme-PC**
</x-mail::message>
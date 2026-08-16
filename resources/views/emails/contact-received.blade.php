<x-mail::message>
# Bedankt, {{ $submission->name }}!

We hebben je bericht ontvangen en nemen zo snel mogelijk contact met je op. Dat is meestal binnen één werkdag.

**Samenvatting van je aanvraag:**

- **Onderwerp:** {{ $submission->subject }}
- **Type:** {{ $submission->request_type }}
- **Bericht:** {{ $submission->message }}

Houd deze e-mail bij de hand. Je kunt direct op dit bericht reageren en je antwoord komt dan automatisch bij ons terecht.

Met vriendelijke groet,<br>
**Slimme-PC**
</x-mail::message>
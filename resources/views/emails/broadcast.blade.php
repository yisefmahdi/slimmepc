@component('mail::message')
# Beste {{ $name }},

{!! nl2br(e($content)) !!}

<br><br>
Met vriendelijke groeten,<br>
**Slimme PC**

@endcomponent

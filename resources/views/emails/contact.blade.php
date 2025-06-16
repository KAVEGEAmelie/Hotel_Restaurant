@component('mail::message')
# Nouveau Message de Contact

Vous avez reçu un nouveau message depuis le site de l'Hôtel Le Printemps.

**Nom :** {{ $data['name'] }}
**Email :** {{ $data['email'] }}
**Sujet :** {{ $data['subject'] }}

---

**Message :**
{{ $data['message'] }}

Merci,<br>
{{ config('app.name') }}
@endcomponent
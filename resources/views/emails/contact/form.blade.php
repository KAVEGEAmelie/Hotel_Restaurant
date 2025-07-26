<x-mail::message>
# Nouveau Message depuis votre site web

Vous avez reçu une nouvelle demande de contact.

**Nom :** {{ $data['name'] }}
**Email :** <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a>
**Sujet :** {{ $data['subject'] }}

---

**Message :**<br>
{{ nl2br(e($data['message'])) }}

</x-mail::message>

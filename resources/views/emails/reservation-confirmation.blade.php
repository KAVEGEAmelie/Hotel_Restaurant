<x-mail::message>
# Confirmation de Réservation

Bonjour **{{ $reservation->client_prenom }} {{ $reservation->client_nom }}**,

Nous avons le plaisir de confirmer votre réservation à l'Hôtel Restaurant Le Printemps.

## Détails de votre réservation

**Numéro de réservation :** #{{ $reservation->id }}  
**Chambre :** {{ $reservation->chambre->nom }}  
**Date d'arrivée :** {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}  
**Date de départ :** {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}  
**Nombre de nuits :** {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }}  
@if($reservation->nombre_personnes)
**Nombre de personnes :** {{ $reservation->nombre_personnes }}  
@endif

## Montant payé
**{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA**

@if($reservation->transaction_ref)
**Référence de transaction :** {{ $reservation->transaction_ref }}
@endif

<x-mail::button :url="route('payment.receipt', $reservation)" color="success">
Télécharger votre reçu
</x-mail::button>

## Informations importantes

- L'enregistrement se fait à partir de 14h00
- Le départ doit se faire avant 12h00
- Une pièce d'identité valide sera demandée à l'arrivée

@if($reservation->notes)
**Notes de réservation :** {{ $reservation->notes }}
@endif

Nous nous réjouissons de vous accueillir à l'Hôtel Restaurant Le Printemps !

Cordialement,<br>
L'équipe de **Hôtel Restaurant Le Printemps**

---
*Si vous avez des questions, n'hésitez pas à nous contacter :*  
📧 hotelrestaurantleprintemps@yahoo.com  
📞 +228 71 34 88 88 / 96 06 88 88
</x-mail::message>

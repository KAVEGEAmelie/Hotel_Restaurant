<?php

namespace App\Http\Controllers;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show(Reservation $reservation)
    {
        // On vérifie que la réservation est bien "en attente"
        if ($reservation->statut !== 'pending') {
            // Si déjà payée ou annulée, on redirige avec un message.
            return redirect()->route('home')->with('error', 'Cette réservation n\'est plus valide pour le paiement.');
        }

        // On passe la réservation à la vue
        return view('payment.show', compact('reservation'));
    }

    public function process(Request $request, Reservation $reservation)
    {
        // 1. Logique de traitement du paiement avec l'API (Stripe, etc.)
        // ...

        // 2. Si le paiement réussit :
        $reservation->update(['statut' => 'confirmée']);

        // 3. Envoyer l'email de confirmation
        // Mail::to($reservation->user->email)->send(new ReservationConfirmedMail($reservation));

        // 4. Générer le reçu PDF
        // ...

        // 5. Rediriger vers la page de succès
        return redirect()->route('reservation.success', $reservation);
    }
}

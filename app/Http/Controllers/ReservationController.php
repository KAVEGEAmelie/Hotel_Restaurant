<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chambre;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationController extends Controller
{
public function create(Request $request)
{
    // 1. Valider TOUTES les données
    $validatedData = $request->validate([
        'chambre_id' => 'required|exists:chambres,id',
        'client_nom' => 'required|string|max:255',
        'client_prenom' => 'required|string|max:255',
        'client_email' => 'required|email|max:255',
        'client_telephone' => 'required|string|max:20',
        'checkin_date' => 'required|date|after_or_equal:today',
        'checkout_date' => 'required|date|after:checkin_date',
        'nombre_invites' => 'required|integer|min:1',
    ]);

    $chambre = Chambre::findOrFail($validatedData['chambre_id']);
    $checkin = Carbon::parse($validatedData['checkin_date']);
    $checkout = Carbon::parse($validatedData['checkout_date']);

    // 2. Vérification de sécurité : la chambre est-elle toujours libre ?
    $isBooked = Reservation::where('chambre_id', $chambre->id)
        ->where('statut', '!=', 'annulée')
        ->where(function ($query) use ($checkin, $checkout) {
            $query->where('check_out_date', '>', $checkin)
                  ->where('check_in_date', '<', $checkout);
        })->exists();

    if ($isBooked) {
        return redirect()->back()->with('error', 'Désolé, cette chambre n\'est plus disponible à ces dates.');
    }
    
    if ($validatedData['nombre_invites'] > $chambre->capacite) {
        return redirect()->back()->with('error', 'Le nombre d\'invités dépasse la capacité de cette chambre.');
    }

    // 3. On prépare TOUTES les données pour la création
    $dataToCreate = $validatedData; // On prend toutes les données validées du client et des dates
    
    // On ajoute les données que le serveur doit calculer
    $nbNuits = $checkin->diffInDays($checkout);
    $dataToCreate['user_id'] = Auth::check() ? Auth::id() : null; // Si l'utilisateur est connecté, on garde son ID
    $dataToCreate['prix_total'] = $nbNuits * $chambre->prix_par_nuit;
    $dataToCreate['statut'] = 'pending';

    // 4. On crée la réservation avec le tableau de données complet
    $reservation = Reservation::create($dataToCreate);

    // 5. Rediriger vers la page de paiement
    return redirect()->route('payment.show', ['reservation' => $reservation]);
}
}
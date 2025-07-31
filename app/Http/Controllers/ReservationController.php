<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chambre;
use App\Models\Reservation;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
class ReservationController extends Controller
{
    /**
     * Crée une réservation en attente et redirige vers le paiement.
     */
    public function create(Request $request)
    {
         // 1. Valider toutes les données du formulaire
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

         // 3. Préparer le tableau de données complet pour la création
        $dataToCreate = $validatedData; // On commence avec toutes les données validées

        // On y ajoute les informations calculées par le serveur
        $nbNuits = $checkin->diffInDays($checkout);
        $dataToCreate['user_id'] = Auth::check() ? Auth::id() : null;
        $dataToCreate['prix_total'] = $nbNuits * $chambre->prix_par_nuit;
        $dataToCreate['statut'] = 'pending';

        // Corriger les noms de champs pour la BDD
$dataToCreate['check_in_date'] = $validatedData['checkin_date'];
$dataToCreate['check_out_date'] = $validatedData['checkout_date'];
unset($dataToCreate['checkin_date'], $dataToCreate['checkout_date']);
        // 4. On crée la réservation avec le tableau de données COMPLET
        $reservation = Reservation::create($dataToCreate);

        // 5. Rediriger vers la page de paiement
        return redirect()->route('payment.show', ['reservation' => $reservation]);
    }

    public function downloadReceipt(Reservation $reservation)
{
    // Sécurité : on vérifie que l'utilisateur connecté est bien le propriétaire de la réservation
    if (Auth::id() !== $reservation->user_id) {
        // Si la réservation a été faite par un invité, il faudrait un autre moyen de vérification
        // (par exemple, un token unique envoyé par email). Pour l'instant, on se base sur l'utilisateur connecté.
        // abort(403);
    }

    // Charger les relations nécessaires
    $reservation->load('chambre');

    // Générer le PDF
    $pdf = PDF::loadView('pdf.receipt', compact('reservation'));

    // Proposer le PDF au téléchargement avec un nom de fichier dynamique
    return $pdf->download('recu-reservation-'.$reservation->id.'.pdf');
}
}

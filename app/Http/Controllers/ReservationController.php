<?php
namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    /**
     * Stocker une nouvelle réservation
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'chambre_id' => 'required|exists:chambres,id',
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_telephone' => 'required|string|max:20',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'nombre_invites' => 'required|integer|min:1',
        ]);

        $chambre = Chambre::findOrFail($validatedData['chambre_id']);
        $checkin = Carbon::parse($validatedData['check_in_date']);
        $checkout = Carbon::parse($validatedData['check_out_date']);

        // Vérification de la capacité
        if ($validatedData['nombre_invites'] > $chambre->capacite) {
            return back()->withInput()->with('error', 'Le nombre d\'invités dépasse la capacité de la chambre.');
        }

        // Vérification de disponibilité
        $conflictingReservations = Reservation::where('chambre_id', $chambre->id)
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->where(function($query) use ($checkin, $checkout) {
                $query->whereBetween('check_in_date', [$checkin, $checkout->copy()->subDay()])
                      ->orWhereBetween('check_out_date', [$checkin->copy()->addDay(), $checkout])
                      ->orWhere(function($q) use ($checkin, $checkout) {
                          $q->where('check_in_date', '<', $checkin)
                            ->where('check_out_date', '>', $checkout);
                      });
            })
            ->exists();

        if ($conflictingReservations) {
            return back()->withInput()->with('error', 'Cette chambre n\'est pas disponible pour les dates sélectionnées.');
        }

        // Calcul du prix total
        $nbNuits = $checkin->diffInDays($checkout);
        $prixTotal = $nbNuits * $chambre->prix_par_nuit;

        // Création de la réservation
        $reservation = Reservation::create([
            'chambre_id' => $chambre->id,
            'user_id' => Auth::id(),
            'client_nom' => $validatedData['client_nom'],
            'client_prenom' => $validatedData['client_prenom'],
            'client_email' => $validatedData['client_email'],
            'client_telephone' => $validatedData['client_telephone'],
            'check_in_date' => $checkin,
            'check_out_date' => $checkout,
            'nombre_invites' => $validatedData['nombre_invites'],
            'prix_total' => $prixTotal,
            'statut' => 'en_attente',
            'statut_paiement' => 'en_attente'
        ]);

        return redirect()->route('reservations.confirm', $reservation);
    }

    /**
     * Afficher la page de confirmation avant paiement
     */
    public function confirm(Reservation $reservation)
    {
        // Vérifier que l'utilisateur connecté est propriétaire de la réservation
        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réservation.');
        }

        // Vérifier que la réservation est bien en attente
        if ($reservation->statut !== 'en_attente') {
            return redirect()->route('chambres.index')->with('error', 'Cette réservation ne peut plus être modifiée.');
        }

        $reservation->load('chambre');
        
        return view('reservations.confirm', compact('reservation'));
    }

    /**
     * Télécharger le reçu de réservation
     */
    public function downloadReceipt(Reservation $reservation)
    {
        if (Auth::id() !== $reservation->user_id) {
            abort(403);
        }

        if ($reservation->statut_paiement !== 'paye') {
            return back()->with('error', 'Le reçu n\'est disponible que pour les paiements confirmés.');
        }

        $reservation->load('chambre');
        $pdf = Pdf::loadView('pdf.receipt', compact('reservation'));

        return $pdf->download('recu-reservation-'.$reservation->id.'.pdf');
    }
}
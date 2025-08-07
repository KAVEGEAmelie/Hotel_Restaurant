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
    public function create(Request $request)
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

        // Vérification de disponibilité plus robuste
        $conflictingReservations = Reservation::where('chambre_id', $chambre->id)
            ->whereIn('statut', ['pending', 'confirmée'])
            ->where(function($query) use ($checkin, $checkout) {
                $query->where(function($q) use ($checkin, $checkout) {
                    // Vérifie si la nouvelle réservation chevauche une réservation existante
                    $q->where('check_in_date', '<', $checkout)
                      ->where('check_out_date', '>', $checkin);
                });
            })
            ->get();

        if ($conflictingReservations->count() > 0) {
            $conflictDetails = $conflictingReservations->map(function($res) {
                return 'Du ' . Carbon::parse($res->check_in_date)->format('d/m/Y') .
                       ' au ' . Carbon::parse($res->check_out_date)->format('d/m/Y');
            })->join(', ');

            return back()->withInput()->with('error',
                'Désolé, cette chambre n\'est plus disponible pour ces dates. ' .
                'Conflit avec les réservations : ' . $conflictDetails);
        }

        // Calcul du prix total
        $nbNuits = $checkin->diffInDays($checkout);
        $dataToCreate = $validatedData;
        $dataToCreate['user_id'] = Auth::id();
        $dataToCreate['prix_total'] = $nbNuits * $chambre->prix_par_nuit;
        $dataToCreate['statut'] = 'pending';

        // Création de la réservation
        $reservation = Reservation::create($dataToCreate);

        return redirect()->route('payment.show', $reservation);
    }

    

    public function downloadReceipt(Reservation $reservation)
    {
        if (Auth::id() !== $reservation->user_id) {
            abort(403);
        }
        $reservation->load('chambre');
        $pdf = Pdf::loadView('pdf.receipt', compact('reservation'));
        return $pdf->download('recu-reservation-'.$reservation->id.'.pdf');
    }
}

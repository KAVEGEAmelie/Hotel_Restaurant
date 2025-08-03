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

        if ($validatedData['nombre_invites'] > $chambre->capacite) {
            return back()->withInput()->with('error', 'Le nombre d\'invités dépasse la capacité de la chambre.');
        }

        $isBooked = Reservation::where('chambre_id', $chambre->id)
            ->whereIn('statut', ['pending', 'confirmée'])
            ->where(fn($q) => $q->where('check_out_date', '>', $checkin)->where('check_in_date', '<', $checkout))
            ->exists();

        if ($isBooked) {
            return back()->withInput()->with('error', 'Désolé, cette chambre n\'est plus disponible pour ces dates.');
        }

        $nbNuits = $checkin->diffInDays($checkout);
        $dataToCreate = $validatedData;
        $dataToCreate['user_id'] = Auth::id(); // Auth::id() renvoie null si l'utilisateur n'est pas connecté
        $dataToCreate['prix_total'] = $nbNuits * $chambre->prix_par_nuit;
        $dataToCreate['statut'] = 'pending';

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

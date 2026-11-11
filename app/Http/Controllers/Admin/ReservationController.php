<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReservationController extends Controller
{
    /**
     * 📋 Affiche la liste des réservations avec filtres de recherche
     */
    public function listAll(Request $request)
    {
        try {
            // Requête de base avec relations
            $query = Reservation::with(['user', 'chambre'])
                ->orderBy('created_at', 'desc');

            // 🔍 Filtre de recherche par nom, prénom ou email
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('client_nom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_prenom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_email', 'like', "%{$searchTerm}%")
                      ->orWhere('client_telephone', 'like', "%{$searchTerm}%");
                });
            }

            // 📊 Filtre par statut
            if ($request->filled('statut') && array_key_exists($request->statut, Reservation::STATUTS)) {
                $query->where('statut', $request->statut);
            }

            // 📅 Filtre par date d'arrivée
            if ($request->filled('date')) {
                $query->whereDate('check_in_date', $request->date);
            }

            // 📄 Pagination avec conservation des paramètres de recherche
            $reservations = $query->paginate(20)->withQueryString();

            return view('admin.reservations.index', compact('reservations'));

        } catch (Exception $e) {
            Log::error('Erreur listage réservations admin: ' . $e->getMessage());

            return redirect()->route('admin.dashboard')
                ->with('error', '❌ Erreur lors du chargement des réservations');
        }
    }

    /**
     * 🔄 Met à jour le statut d'une réservation (AJAX)
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'statut' => ['required', Rule::in(array_keys(Reservation::STATUTS))],
        ]);

        // Remettre une réservation en "en attente"/"confirmée" ne doit pas créer de double réservation.
        if (in_array($validated['statut'], Reservation::STATUTS_BLOQUANTS, true)
            && ! in_array($reservation->statut, Reservation::STATUTS_BLOQUANTS, true)
            && $this->aUnConflit($reservation)) {
            return response()->json([
                'success' => false,
                'message' => 'La chambre est déjà réservée sur ces dates.',
            ], 422);
        }

        $oldStatus = $reservation->statut;
        $reservation->update(['statut' => $validated['statut']]);

        Log::info('Statut réservation modifié', [
            'reservation_id' => $reservation->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['statut'],
            'admin_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Réservation de {$reservation->client_nom} {$reservation->client_prenom} : {$reservation->statut_label}",
            'new_status' => $validated['statut'],
            'old_status' => $oldStatus,
        ]);
    }

    private function aUnConflit(Reservation $reservation): bool
    {
        return Reservation::where('chambre_id', $reservation->chambre_id)
            ->whereKeyNot($reservation->id)
            ->chevauche($reservation->check_in_date, $reservation->check_out_date)
            ->exists();
    }

    /**
     * ⚡ Actions groupées sur les réservations (AJAX)
     */
    public function bulkAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:confirm,cancel,delete',
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:reservations,id'
            ]);

            $reservations = Reservation::whereIn('id', $validated['ids']);
            $count = $reservations->count();

            if ($count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune réservation trouvée'
                ], 400);
            }

            switch ($validated['action']) {
                case 'confirm':
                    $reservations->update(['statut' => Reservation::STATUT_CONFIRMEE]);
                    $message = "✅ {$count} réservation(s) confirmée(s) avec succès";
                    break;

                case 'cancel':
                    $reservations->update(['statut' => Reservation::STATUT_ANNULEE]);
                    $message = "❌ {$count} réservation(s) annulée(s) avec succès";
                    break;

                case 'delete':
                    $reservationsList = $reservations->pluck('client_nom', 'id')->toArray();
                    $reservations->delete();
                    $message = "🗑️ {$count} réservation(s) supprimée(s) définitivement";

                    Log::warning('Suppression groupée de réservations', [
                        'count' => $count,
                        'reservations' => $reservationsList,
                        'admin_id' => Auth::id()
                    ]);
                    break;
            }

            Log::info('Action groupée réservations', [
                'action' => $validated['action'],
                'count' => $count,
                'ids' => $validated['ids'],
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'action' => $validated['action']
            ]);

        } catch (Exception $e) {
            Log::error('Erreur action groupée: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'action groupée'
            ], 500);
        }
    }

    /**
     * 🗑️ Supprimer une réservation
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        try {
            // Récupère les infos AVANT suppression
            $reservationInfo = "#{$reservation->id} - {$reservation->client_nom} {$reservation->client_prenom}";
            $clientNom = $reservation->client_nom;
            $clientPrenom = $reservation->client_prenom;

            $reservation->delete();

            Log::warning('Réservation supprimée par admin', [
                'reservation_id' => $reservation->id,
                'client' => $clientNom . ' ' . $clientPrenom,
                'admin_id' => Auth::id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "🗑️ Réservation {$reservationInfo} supprimée avec succès"
                ]);
            }

            return redirect()->route('admin.reservations.index')
                ->with('success', "🗑️ Réservation {$reservationInfo} supprimée avec succès !");
        } catch (Exception $e) {
            Log::error('Erreur suppression réservation: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression'
                ], 500);
            }

            return redirect()->route('admin.reservations.index')
                ->with('error', '❌ Erreur lors de la suppression de la réservation');
        }
    }
    /**
     * 🚔 Télécharger la fiche de police
     */
    public function downloadPoliceForm(Reservation $reservation)
    {
        try {
            $reservation->load(['chambre', 'user']);

            $data = [
                'reservation' => $reservation,
                'date_generation' => now(),
                'admin_name' => Auth::user()->name,
                'hotel_info' => [
                    'nom' => 'Hôtel Restaurant Le Printemps',
                    'adresse' => 'Votre adresse ici',
                    'telephone' => 'Votre téléphone ici',
                    'email' => 'hotelrestaurantleprintemps@yahoo.com'
                ]
            ];

            $pdf = Pdf::loadView('admin.reservations.police-form', $data)
                ->setPaper('a4', 'portrait');

            Log::info('Fiche de police générée', [
                'reservation_id' => $reservation->id,
                'admin_id' => Auth::id()
            ]);

            return $pdf->stream("fiche-police-{$reservation->id}-{$reservation->client_nom}.pdf");

        } catch (Exception $e) {
            Log::error('Erreur génération fiche police: ' . $e->getMessage());

            return redirect()->route('admin.reservations.index')
                ->with('error', '❌ Erreur lors de la génération de la fiche de police');
        }
    }

    /**
     * 📄 Télécharger le reçu d'une réservation
     */
    public function downloadReceipt(Reservation $reservation)
    {
        try {
            $reservation->load(['chambre', 'user']);

            $pdf = Pdf::loadView('payment.receipt', compact('reservation'))
                ->setPaper('a4', 'portrait');

            Log::info('Reçu téléchargé par admin', [
                'reservation_id' => $reservation->id,
                'admin_id' => Auth::id()
            ]);

            return $pdf->download("recu-reservation-{$reservation->id}.pdf");

        } catch (Exception $e) {
            Log::error('Erreur téléchargement reçu: ' . $e->getMessage());

            return redirect()->route('admin.reservations.index')
                ->with('error', '❌ Erreur lors du téléchargement du reçu');
        }
    }
}

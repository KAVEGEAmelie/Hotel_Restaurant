<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
            $query = Reservation::with(['user', 'chambre', 'adminConfirme'])
                ->orderBy('created_at', 'desc');

            // 🔍 Filtre de recherche par nom, prénom, email, téléphone ou numéro de réservation
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('client_nom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_prenom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_email', 'like', "%{$searchTerm}%")
                      ->orWhere('client_telephone', 'like', "%{$searchTerm}%");
                    
                    // Recherche par numéro de réservation (ID exact ou partiel)
                    if (is_numeric($searchTerm)) {
                        $q->orWhere('id', $searchTerm);
                    }
                    // Recherche par #ID (ex: "#35" ou "35")
                    if (preg_match('/^#?(\d+)$/', $searchTerm, $matches)) {
                        $q->orWhere('id', $matches[1]);
                    }
                });
            }

            // 📊 Filtre par statut
            if ($request->filled('statut') && $request->statut !== 'all') {
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
     * 👁️ Afficher une réservation spécifique
     */
    public function show(Reservation $reservation)
    {
        try {
            $reservation->load(['user', 'chambre']);

            return view('admin.reservations.show', compact('reservation'));

        } catch (Exception $e) {
            Log::error('Erreur affichage réservation: ' . $e->getMessage());

            return redirect()->route('admin.reservations.index')
                ->with('error', '❌ Erreur lors de l\'affichage de la réservation');
        }
    }

    /**
     * ✏️ Afficher le formulaire d'édition
     */
    public function edit(Reservation $reservation)
    {
        try {
            $reservation->load(['chambre']);
            $chambres = Chambre::where('statut', 'disponible')->get();

            return view('admin.reservations.edit', compact('reservation', 'chambres'));

        } catch (Exception $e) {
            Log::error('Erreur édition réservation: ' . $e->getMessage());

            return redirect()->route('admin.reservations.index')
                ->with('error', '❌ Erreur lors de l\'édition de la réservation');
        }
    }

    /**
     * 💾 Mettre à jour une réservation
     */
    public function update(Request $request, Reservation $reservation)
    {
        try {
            $validated = $request->validate([
                'client_nom' => 'required|string|max:255',
                'client_prenom' => 'required|string|max:255',
                'client_email' => 'required|email|max:255',
                'client_telephone' => 'required|string|max:20',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'nombre_invites' => 'required|integer|min:1',
                'statut' => 'required|string|in:pending,confirmée,annulée,payée',
                'notes' => 'nullable|string|max:1000'
            ]);

            $reservation->update($validated);

            Log::info('Réservation mise à jour par admin', [
                'reservation_id' => $reservation->id,
                'admin_id' => Auth::id(),
                'changes' => $validated
            ]);

            return redirect()->route('admin.reservations.index')
                ->with('success', "✅ Réservation #{$reservation->id} mise à jour avec succès !");

        } catch (Exception $e) {
            Log::error('Erreur mise à jour réservation: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Erreur lors de la mise à jour de la réservation');
        }
    }

    /**
     * 🔄 Met à jour le statut d'une réservation (AJAX)
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        try {
            $validated = $request->validate([
                'statut' => 'required|string|in:pending,confirmée,annulée,payée'
            ]);

            $oldStatus = $reservation->statut;
            $reservation->update([
                'statut' => $validated['statut'],
                'updated_by_admin' => Auth::id(),
                'status_updated_at' => now()
            ]);

            $statusEmojis = [
                'pending' => '⏳',
                'confirmée' => '✅',
                'annulée' => '❌',
                'payée' => '💳'
            ];

            Log::info('Statut réservation modifié via AJAX', [
                'reservation_id' => $reservation->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['statut'],
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$statusEmojis[$validated['statut']]} Réservation de {$reservation->client_nom} {$reservation->client_prenom} mise à jour",
                'new_status' => $validated['statut'],
                'old_status' => $oldStatus
            ]);

        } catch (Exception $e) {
            Log::error('Erreur AJAX mise à jour statut: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
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
                    $reservations->update([
                        'statut' => 'confirmée',
                        'admin_confirme_id' => Auth::id(),
                        'date_confirmation' => now()
                    ]);
                    $message = "✅ {$count} réservation(s) confirmée(s) avec succès";
                    break;

                case 'cancel':
                    $reservations->update([
                        'statut' => 'annulée',
                        'cancelled_at' => now(),
                        'cancelled_by' => Auth::id()
                    ]);
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
                'message' => 'Erreur lors de l\'action groupée: ' . $e->getMessage()
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

    /**
     * 📊 Exporter les réservations
     */
    public function export(Request $request, $format = 'pdf')
    {
        try {
            // Récupérer les réservations selon les filtres
            $query = Reservation::with(['user', 'chambre']);

            // Appliquer les mêmes filtres que dans listAll
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('client_nom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_prenom', 'like', "%{$searchTerm}%")
                      ->orWhere('client_email', 'like', "%{$searchTerm}%")
                      ->orWhere('client_telephone', 'like', "%{$searchTerm}%");
                    
                    // Recherche par numéro de réservation (ID exact ou partiel)
                    if (is_numeric($searchTerm)) {
                        $q->orWhere('id', $searchTerm);
                    }
                    // Recherche par #ID (ex: "#35" ou "35")
                    if (preg_match('/^#?(\d+)$/', $searchTerm, $matches)) {
                        $q->orWhere('id', $matches[1]);
                    }
                });
            }

            if ($request->filled('statut') && $request->statut !== 'all') {
                $query->where('statut', $request->statut);
            }

            if ($request->filled('date')) {
                $query->whereDate('check_in_date', $request->date);
            }

            $reservations = $query->orderBy('created_at', 'desc')->get();

            $data = [
                'reservations' => $reservations,
                'date_export' => now(),
                'admin_name' => Auth::user()->name,
                'filtres' => $request->only(['search', 'statut', 'date'])
            ];

            switch ($format) {
                case 'pdf':
                    $pdf = Pdf::loadView('admin.reservations.export-pdf', $data)
                        ->setPaper('a4', 'landscape');
                    return $pdf->download("export-reservations-" . now()->format('Y-m-d') . ".pdf");

                case 'excel':
                    // Export CSV (compatible Excel)
                    $filename = "export-reservations-" . now()->format('Y-m-d') . ".csv";
                    
                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    $callback = function() use ($reservations) {
                        $file = fopen('php://output', 'w');
                        
                        // BOM pour l'UTF-8 (pour Excel)
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        // En-têtes CSV
                        fputcsv($file, [
                            'N° Réservation',
                            'Nom',
                            'Prénom', 
                            'Email',
                            'Téléphone',
                            'Chambre',
                            'Date Arrivée',
                            'Date Départ',
                            'Nombre Invités',
                            'Prix Total (FCFA)',
                            'Statut',
                            'Date Création'
                        ], ';');

                        // Données
                        foreach ($reservations as $reservation) {
                            fputcsv($file, [
                                '#' . $reservation->id,
                                $reservation->client_nom,
                                $reservation->client_prenom,
                                $reservation->client_email,
                                $reservation->client_telephone,
                                $reservation->chambre->nom ?? 'N/A',
                                \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y'),
                                \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y'),
                                $reservation->nombre_invites,
                                number_format($reservation->prix_total, 0, ',', ' '),
                                ucfirst($reservation->statut),
                                $reservation->created_at->format('d/m/Y H:i:s')
                            ], ';');
                        }
                        
                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);

                default:
                    return redirect()->back()
                        ->with('error', '❌ Format d\'export non supporté');
            }

        } catch (Exception $e) {
            Log::error('Erreur export réservations: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', '❌ Erreur lors de l\'export');
        }
    }

    /**
     * 📈 Statistiques des réservations
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Reservation::count(),
                'en_attente' => Reservation::where('statut', 'pending')->count(),
                'confirmees' => Reservation::where('statut', 'confirmée')->count(),
                'annulees' => Reservation::where('statut', 'annulée')->count(),
                'payees' => Reservation::where('statut', 'payée')->count(),
                'ce_mois' => Reservation::whereMonth('created_at', now()->month)->count(),
                'revenus_mois' => Reservation::whereMonth('created_at', now()->month)
                    ->where('statut', 'payée')
                    ->sum('prix_total'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Erreur statistiques réservations: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des statistiques'
            ], 500);
        }
    }
}

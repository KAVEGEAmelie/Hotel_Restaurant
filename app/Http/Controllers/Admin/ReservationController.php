<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Affiche la liste des réservations avec filtres de recherche.
     */
    public function listAll(Request $request)
    {
        // On commence une requête de base
        $query = Reservation::with(['user', 'chambre'])->latest();

        // Filtre de recherche par nom ou email du client
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('client_nom', 'like', "%{$searchTerm}%")
                  ->orWhere('client_prenom', 'like', "%{$searchTerm}%")
                  ->orWhere('client_email', 'like', "%{$searchTerm}%");
            });
        }

        // Filtre par statut
        if ($request->filled('statut') && $request->statut != 'all') {
            $query->where('statut', $request->statut);
        }

        // Filtre par date d'arrivée
        if ($request->filled('date')) {
            $query->whereDate('check_in_date', $request->date);
        }

        // On exécute la requête avec pagination
        $reservations = $query->paginate(15)->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Supprime une réservation.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')->with('success', 'Réservation supprimée avec succès.');
    }

    // Vous ajouterez les autres méthodes (show, update, etc.) ici plus tard
}
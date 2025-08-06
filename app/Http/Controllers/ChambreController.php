<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChambreController extends Controller
{
    /**
     * Affiche la liste publique des chambres, en filtrant par disponibilité.
     */
    public function listPublic(Request $request)
    {
        // On commence une requête de base
        $query = Chambre::query();

        // On vérifie si des dates ont été soumises dans le formulaire
        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $checkin = Carbon::parse($request->check_in_date);
            $checkout = Carbon::parse($request->check_out_date);

            // Si le nombre d'invités est fourni, on filtre par capacité
            if ($request->filled('guests')) {
                $query->where('capacite', '>=', $request->guests);
            }

            // On exclut les chambres qui ont des réservations qui se chevauchent
            $query->whereDoesntHave('reservations', function ($q) use ($checkin, $checkout) {
                $q->whereIn('statut', ['pending', 'confirmée']) // On ignore les réservations annulées
                  ->where(function ($query) use ($checkin, $checkout) {
                      $query->where('check_in_date', '<', $checkout)
                            ->where('check_out_date', '>', $checkin);
                  });
            });
        }

        // On récupère le résultat final de la requête (filtrée ou non)
        $chambres = $query->latest()->get();

        // On retourne la vue avec la liste de chambres
        return view('chambres.index', [
            'chambres' => $chambres,
        ]);
    }

    /**
     * Affiche la page de détail pour une chambre spécifique.
     */
    public function show(Chambre $chambre)
    {
        return view('chambres.show', ['chambre' => $chambre]);
    }

    // Les autres méthodes ne sont pas nécessaires pour la partie publique.
}

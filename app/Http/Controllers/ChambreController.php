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
        // On commence une requête de base - AFFICHER TOUTES LES CHAMBRES
        $query = Chambre::query();

        // Trier par disponibilité d'abord (disponibles en premier)
        $query->orderBy('est_disponible', 'desc')->orderBy('nom');

        // On vérifie si des dates ont été soumises dans le formulaire
        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $checkin = Carbon::parse($request->check_in_date);
            $checkout = Carbon::parse($request->check_out_date);

            // Si le nombre d'invités est fourni, on filtre par capacité
            if ($request->filled('guests')) {
                $query->where('capacite', '>=', $request->guests);
            }

            // On exclut les chambres qui ont des réservations qui se chevauchent
            // MAIS ON GARDE CELLES QUI SONT MARQUÉES COMME INDISPONIBLES PAR L'ADMIN
            $query->where(function($q) use ($checkin, $checkout) {
                // Chambres disponibles ET sans conflit de dates
                $q->where('est_disponible', true)
                  ->whereDoesntHave('reservations', function ($subQuery) use ($checkin, $checkout) {
                      $subQuery->whereIn('statut', ['pending', 'confirmed'])
                               ->where(function ($dateQuery) use ($checkin, $checkout) {
                                   $dateQuery->where('check_in_date', '<', $checkout)
                                            ->where('check_out_date', '>', $checkin);
                               });
                  });
            })->orWhere('est_disponible', false); // Toujours afficher les indisponibles
        }

        // On récupère le résultat final de la requête (filtrée ou non)
        $chambres = $query->get();

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
}

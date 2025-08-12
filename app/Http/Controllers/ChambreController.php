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

        // Filtre par capacité/nombre d'invités
        if ($request->filled('guests')) {
            $query->where('capacite', '>=', $request->guests);
        }

        // Filtre par prix
        if ($request->filled('prix_min')) {
            $query->where('prix_par_nuit', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix_par_nuit', '<=', $request->prix_max);
        }

        // Filtre par catégorie/type de chambre
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par disponibilité
        if ($request->filled('disponibilite')) {
            if ($request->disponibilite === 'disponible') {
                $query->where('est_disponible', true);
            } elseif ($request->disponibilite === 'indisponible') {
                $query->where('est_disponible', false);
            }
        }

        // On vérifie si des dates ont été soumises dans le formulaire
        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $checkin = Carbon::parse($request->check_in_date);
            $checkout = Carbon::parse($request->check_out_date);

            // Validation des dates
            if ($checkout <= $checkin) {
                return redirect()->back()->with('error', 'La date de départ doit être postérieure à la date d\'arrivée.');
            }

            if ($checkin->isPast()) {
                return redirect()->back()->with('error', 'La date d\'arrivée ne peut pas être dans le passé.');
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

        // Tri des résultats
        $sort = $request->get('sort', 'nom_asc');
        switch ($sort) {
            case 'nom_desc':
                $query->orderBy('nom', 'desc');
                break;
            case 'prix_asc':
                $query->orderBy('prix_par_nuit', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('prix_par_nuit', 'desc');
                break;
            case 'capacite_asc':
                $query->orderBy('capacite', 'asc');
                break;
            case 'capacite_desc':
                $query->orderBy('capacite', 'desc');
                break;
            case 'nom_asc':
            default:
                $query->orderBy('est_disponible', 'desc')->orderBy('nom', 'asc');
                break;
        }

        // On récupère le résultat final de la requête (filtrée ou non)
        $chambres = $query->get();

        // Messages d'information
        if ($chambres->isEmpty() && $request->hasAny(['check_in_date', 'check_out_date', 'guests', 'prix_min', 'prix_max', 'categorie', 'disponibilite'])) {
            session()->flash('info', 'Aucune chambre ne correspond à vos critères. Essayez de modifier vos filtres.');
        }

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $aujourdhui = today();

        // Réservations actives (en attente ou confirmées)
        $actives = fn () => Reservation::with('chambre')
            ->whereIn('statut', Reservation::STATUTS_BLOQUANTS);

        $arrivees = $actives()->whereDate('check_in_date', $aujourdhui)->orderBy('client_nom')->get();
        $departs = Reservation::with('chambre')
            ->where('statut', Reservation::STATUT_CONFIRMEE)
            ->whereDate('check_out_date', $aujourdhui)
            ->orderBy('client_nom')
            ->get();

        // Chambres occupées cette nuit : arrivée <= aujourd'hui < départ
        $chambresOccupees = Reservation::where('statut', Reservation::STATUT_CONFIRMEE)
            ->whereDate('check_in_date', '<=', $aujourdhui)
            ->whereDate('check_out_date', '>', $aujourdhui)
            ->distinct()
            ->count('chambre_id');
        $totalChambres = Chambre::count();

        // Chiffre d'affaires encaissé ce mois-ci
        $chiffreAffairesMois = (float) Reservation::where('statut_paiement', Reservation::PAIEMENT_PAYE)
            ->whereBetween('date_paiement', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('montant_paye');

        $enAttentePaiement = Reservation::where('statut', Reservation::STATUT_EN_ATTENTE)
            ->where('statut_paiement', '!=', Reservation::PAIEMENT_PAYE)
            ->count();

        // Payées mais annulées (paiement arrivé après expiration) : à rembourser
        $aRembourser = Reservation::with('chambre')
            ->where('statut_paiement', Reservation::PAIEMENT_PAYE)
            ->where('statut', Reservation::STATUT_ANNULEE)
            ->latest('date_paiement')
            ->get();

        $dernieresReservations = Reservation::with('chambre')->latest()->take(8)->get();

        return view('admin.dashboard', [
            'stats' => [
                'chambres_occupees' => $chambresOccupees,
                'total_chambres' => $totalChambres,
                'taux_occupation' => $totalChambres > 0 ? (int) round($chambresOccupees * 100 / $totalChambres) : 0,
                'chiffre_affaires_mois' => $chiffreAffairesMois,
                'en_attente_paiement' => $enAttentePaiement,
            ],
            'arrivees' => $arrivees,
            'departs' => $departs,
            'aRembourser' => $aRembourser,
            'dernieresReservations' => $dernieresReservations,
        ]);
    }
}

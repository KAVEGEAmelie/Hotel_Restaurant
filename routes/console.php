<?php

use App\Models\Reservation;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
| Libère les chambres bloquées par des réservations jamais payées.
| Une réservation "en attente" non payée depuis plus de 2 heures est annulée.
*/
Artisan::command('reservations:expirer {--heures=2}', function () {
    $count = Reservation::where('statut', Reservation::STATUT_EN_ATTENTE)
        ->where('statut_paiement', '!=', Reservation::PAIEMENT_PAYE)
        ->where('created_at', '<', now()->subHours((int) $this->option('heures')))
        ->update([
            'statut' => Reservation::STATUT_ANNULEE,
            'notes_paiement' => 'Annulée automatiquement : paiement non reçu',
        ]);

    $this->info("{$count} réservation(s) expirée(s).");
})->purpose('Annule les réservations en attente jamais payées');

Schedule::command('reservations:expirer')->everyFifteenMinutes();

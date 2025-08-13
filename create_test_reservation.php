<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Reservation;
use App\Models\Chambre;

// Créer une chambre de test si elle n'existe pas
$chambre = Chambre::first();
if (!$chambre) {
    echo 'Aucune chambre trouvée, création d\'une chambre de test...' . PHP_EOL;
    $chambre = Chambre::create([
        'numero' => '101',
        'type' => 'Deluxe',
        'prix' => 150.00,
        'description' => 'Chambre de luxe avec vue sur mer',
        'capacite' => 2,
        'disponible' => true
    ]);
}

// Créer une réservation de test
$reservation = Reservation::create([
    'chambre_id' => $chambre->id,
    'nom' => 'Jean Dupont',
    'email' => 'jean.dupont@gmail.com',
    'telephone' => '+33123456789',
    'date_arrivee' => '2024-08-15',
    'date_depart' => '2024-08-18',
    'nombre_adultes' => 2,
    'nombre_enfants' => 0,
    'demandes_speciales' => 'Vue sur mer si possible',
    'prix_total' => 450.00,
    'statut' => 'en_attente'
]);

echo 'Réservation créée avec ID: ' . $reservation->id . PHP_EOL;
echo 'URL de test: http://localhost:8000/payment/initiate/' . $reservation->id . PHP_EOL;
echo 'Montant total: ' . $reservation->prix_total . '€' . PHP_EOL;
echo 'Client: ' . $reservation->nom . ' (' . $reservation->email . ')' . PHP_EOL;

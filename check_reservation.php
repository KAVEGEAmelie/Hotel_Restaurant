<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Vérifier la réservation
$reservation = App\Models\Reservation::with(['chambre', 'user'])->find(35);

if (!$reservation) {
    echo "ERREUR: Réservation 35 non trouvée" . PHP_EOL;
    exit;
}

echo "=== RÉSERVATION 35 ===" . PHP_EOL;
echo "ID: " . $reservation->id . PHP_EOL;
echo "Statut: " . $reservation->statut . PHP_EOL;
echo "User ID: " . $reservation->user_id . PHP_EOL;
echo "Chambre: " . ($reservation->chambre ? $reservation->chambre->nom : 'MANQUANTE') . PHP_EOL;

if ($reservation->chambre) {
    echo "Image chambre: " . $reservation->chambre->image_principale . PHP_EOL;
} else {
    echo "PROBLÈME: Pas de chambre associée!" . PHP_EOL;
}

// Vérifier l'utilisateur
if ($reservation->user) {
    echo "Propriétaire: " . $reservation->user->name . " (" . $reservation->user->email . ")" . PHP_EOL;
} else {
    echo "PROBLÈME: Pas d'utilisateur associé!" . PHP_EOL;
}

echo "Check-in: " . $reservation->check_in_date . PHP_EOL;
echo "Check-out: " . $reservation->check_out_date . PHP_EOL;
echo "Prix total: " . $reservation->prix_total . PHP_EOL;

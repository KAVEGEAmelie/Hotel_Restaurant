<?php
require_once 'vendor/autoload.php';

// Charger Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simuler une requête vers la page de confirmation
$request = Illuminate\Http\Request::create('/reservations/35/confirm', 'GET');

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
    echo "Content Length: " . strlen($response->getContent()) . PHP_EOL;
    
    if ($response->getStatusCode() === 500) {
        echo "ERREUR 500 - Contenu:" . PHP_EOL;
        echo $response->getContent() . PHP_EOL;
    } else if (strlen($response->getContent()) < 100) {
        echo "Contenu suspect (trop court):" . PHP_EOL;
        echo $response->getContent() . PHP_EOL;
    } else {
        echo "Réponse semble normale" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}

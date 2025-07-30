<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PaymentController extends Controller
{
   // Dans PaymentController.php

public function show(Reservation $reservation)
{
    // On charge la relation 'chambre' pour être sûr de pouvoir l'utiliser
    $reservation->load('chambre');

    if ($reservation->statut !== 'pending') {
        return redirect()->route('home')->with('error', 'Cette réservation n\'est plus valide.');
    }

    return view('payment.show', compact('reservation'));
}

    public function process(Request $request, Reservation $reservation)
    {
        try {
            $apiBaseUrl = config('services.cashpay.url');

            // 1. Obtenir le token d'accès
            $tokenResponse = Http::asForm()->post($apiBaseUrl . '/oauth/token', [
                'username' => config('services.cashpay.username'),
                'password' => config('services.cashpay.password'),
                'client_id' => config('services.cashpay.client_id'),
                'client_secret' => config('services.cashpay.client_secret'),
                'grant_type' => 'password',
            ]);

            if (!$tokenResponse->successful()) throw new \Exception('Auth Error');
            $accessToken = $tokenResponse->json()['access_token'];

            // 2. Créer la commande de paiement
            $paymentData = [
                'amount' => (int) $reservation->prix_total,
                'description' => 'Paiement réservation #' . $reservation->id . ' pour ' . $reservation->chambre->nom,
                'merchant_reference' => 'RESA-' . $reservation->id . '-' . time(),
                'client' => [
                    'lastname' => $reservation->client_nom,
                    'firstname' => $reservation->client_prenom,
                    'email' => $reservation->client_email,
                    'phone' => $reservation->client_telephone,
                ],
                'return_url' => route('payment.return'),
                'callback_url' => route('payment.callback'),
            ];

            $paymentResponse = Http::withToken($accessToken)
                ->withHeaders([
                    'api_key' => config('services.cashpay.api_key'),
                    'api_reference' => config('services.cashpay.api_reference'),
                ])
                ->post($apiBaseUrl . '/orders', $paymentData);

            if (!$paymentResponse->successful() || !isset($paymentResponse->json()['payment_url'])) {
                Log::error('CashPay Init Error', ['data' => $paymentData, 'response' => $paymentResponse->body()]);
                throw new \Exception('Payment Init Error');
            }

            // 3. Sauvegarder la référence et rediriger
            $reservation->transaction_ref = $paymentResponse->json()['order_reference'];
            $reservation->save();

            return redirect()->away($paymentResponse->json()['payment_url']);

        } catch (\Exception $e) {
            Log::error('CashPay Process Error: ' . $e->getMessage());
            return back()->with('error', 'Le service de paiement a rencontré un problème. Veuillez réessayer.');
        }
    }

    public function return(Request $request)
    {
        return redirect()->route('home')->with('success', 'Votre paiement est en cours de traitement. Vous recevrez une confirmation par e-mail.');
    }

    public function callback(Request $request)
    {
        $token = $request->input('data'); // La doc dit que les données sont dans un champ "data"

        if (!$token) {
            Log::warning('Callback CashPay reçu sans data.');
            return response('Token manquant', 400);
        }

        try {
            $secretKey = config('services.cashpay.api_key');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $transactionRef = $decoded->order_reference; // Récupère la référence depuis le token
            $reservation = Reservation::where('transaction_ref', $transactionRef)->first();

            if ($reservation) {
                if ($decoded->status === 'SUCCESS') { // À vérifier, le statut peut être en majuscules
                    $reservation->update(['statut' => 'confirmée']);
                    // TODO: Envoyer l'email de confirmation au client
                    Log::info('Paiement confirmé via callback pour la réservation #' . $reservation->id);
                } else {
                    $reservation->update(['statut' => 'échoué']);
                    Log::warning('Paiement échoué via callback pour la réservation #' . $reservation->id, (array)$decoded);
                }
            } else {
                 Log::warning('Callback CashPay reçu pour une transaction inconnue', ['ref' => $transactionRef]);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Erreur décodage JWT CashPay: ' . $e->getMessage());
            return response('Erreur de traitement', 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CashPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $cashPayService;

    public function __construct(CashPayService $cashPayService)
    {
        $this->cashPayService = $cashPayService;
    }

    public function show(Reservation $reservation)
    {
        // Vérifier la configuration
        if (!$this->cashPayService->isConfigured()) {
            return redirect()->route('home')->with('error', 'Service de paiement non configuré.');
        }

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
            // Validation des données
            $request->validate([
                'payment_method' => 'required|in:card,mobile_money',
            ]);

            // Vérifier la configuration
            if (!$this->cashPayService->isConfigured()) {
                throw new \Exception('Service de paiement non configuré');
            }

            // Préparer les données de paiement
            $paymentData = $this->cashPayService->preparePaymentData($reservation, $request->payment_method);

            // Initialiser le paiement
            $result = $this->cashPayService->initializePayment($paymentData);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            // Sauvegarder la référence de transaction
            $reservation->transaction_ref = $result['order_reference'];
            $reservation->save();

            Log::info('Paiement initialisé avec succès', [
                'reservation_id' => $reservation->id,
                'order_reference' => $result['order_reference'],
                'payment_method' => $request->payment_method
            ]);

            // Rediriger vers la page de paiement CashPay
            return redirect()->away($result['bill_url']);

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Le service de paiement a rencontré un problème. Veuillez réessayer.');
        }
    }

    public function return(Request $request)
    {
        // Cette méthode est appelée quand l'utilisateur revient du site CashPay
        return redirect()->route('home')->with('success', 'Votre paiement est en cours de traitement. Vous recevrez une confirmation par e-mail.');
    }

    public function callback(Request $request)
    {
        Log::info('Callback CashPay reçu', [
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $token = $request->input('data');

        if (!$token) {
            Log::warning('Callback CashPay reçu sans token data');
            return response('Token manquant', 400);
        }

        // Décoder le token JWT
        $result = $this->cashPayService->decodeCallbackToken($token);

        if (!$result['success']) {
            Log::error('Erreur décodage token: ' . $result['error']);
            return response('Erreur de traitement du token', 400);
        }

        $decodedData = $result['data'];

        // Récupérer la référence de commande
        $orderReference = $decodedData['order_reference'] ?? null;

        if (!$orderReference) {
            Log::error('Token JWT ne contient pas order_reference', [
                'decoded' => $decodedData
            ]);
            return response('Référence de commande manquante', 400);
        }

        // Trouver la réservation correspondante
        $reservation = Reservation::where('transaction_ref', $orderReference)->first();

        if (!$reservation) {
            Log::warning('Réservation non trouvée pour la référence', [
                'order_reference' => $orderReference,
                'decoded' => $decodedData
            ]);
            return response('Réservation non trouvée', 404);
        }

        // Traiter le statut du paiement
        $status = $decodedData['status'] ?? null;

        if ($status === 'SUCCESS') {
            $reservation->update(['statut' => 'confirmée']);
            Log::info('Paiement confirmé avec succès', [
                'reservation_id' => $reservation->id,
                'order_reference' => $orderReference,
                'status' => $status
            ]);

            // TODO: Envoyer un email de confirmation au client
            // Mail::to($reservation->client_email)->send(new ReservationConfirmed($reservation));

        } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
            $reservation->update(['statut' => 'échoué']);
            Log::warning('Paiement échoué', [
                'reservation_id' => $reservation->id,
                'order_reference' => $orderReference,
                'status' => $status
            ]);
        } else {
            Log::warning('Statut de paiement inconnu', [
                'reservation_id' => $reservation->id,
                'order_reference' => $orderReference,
                'status' => $status
            ]);
        }

        return response('OK', 200);
    }

        /**
     * Route de test pour vérifier la configuration
     */
    public function testConnection()
    {
        if (!\Illuminate\Support\Facades\Auth::user() || !\Illuminate\Support\Facades\Auth::user()->is_admin) {
            abort(403);
        }

        $result = $this->cashPayService->testConnection();

        return response()->json($result);
    }
}

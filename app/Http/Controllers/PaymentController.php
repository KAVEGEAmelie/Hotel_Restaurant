<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CashPayService;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    private $cashPayService;

    public function __construct(CashPayService $cashPayService)
    {
        $this->cashPayService = $cashPayService;
    }

    /**
     * Afficher la page de paiement pour une réservation
     */
    public function show(Reservation $reservation)
    {
        try {
            // Vérifier la configuration du service de paiement
            if (!$this->cashPayService->isConfigured()) {
                return redirect()->route('home')
                    ->with('error', 'Service de paiement temporairement indisponible.');
            }

            // Charger la relation chambre
            $reservation->load('chambre');

            // Vérifier le statut de la réservation
            if (!in_array($reservation->statut, ['pending', 'en_attente'])) {
                return redirect()->route('home')
                    ->with('error', 'Cette réservation n\'est plus disponible pour le paiement.');
            }

            // Vérifier si déjà payée
            if (isset($reservation->payment_status) && $reservation->payment_status === 'paid') {
                return redirect()->route('payment.success', $reservation)
                    ->with('info', 'Cette réservation a déjà été payée.');
            }

            return view('payment.show', compact('reservation'));

        } catch (Exception $e) {
            Log::error('Erreur affichage page paiement: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Impossible d\'afficher la page de paiement.');
        }
    }

    /**
     * Traiter le paiement d'une réservation
     */
    public function process(Request $request, Reservation $reservation)
    {
        Log::info('PaymentController::process démarré', [
            'reservation_id' => $reservation->id,
            'statut_reservation' => $reservation->statut,
            'payment_method' => $request->payment_method
        ]);

        try {
            // Validation des données d'entrée
            $validated = $request->validate([
                'payment_method' => 'required|in:card,mobile_money,mtn,moov,orange,visa,mastercard',
            ]);

            Log::info('Validation des données réussie');

            // Vérifier la configuration du service
            if (!$this->cashPayService->isConfigured()) {
                throw new Exception('Service de paiement non configuré');
            }

            // Vérifier que la réservation est éligible au paiement
            if (!in_array($reservation->statut, ['pending', 'en_attente'])) {
                throw new Exception('Cette réservation n\'est plus valide pour le paiement');
            }

            // Charger la relation chambre si pas déjà fait
            if (!$reservation->relationLoaded('chambre')) {
                $reservation->load('chambre');
            }

            // Préparer les données de paiement
            $paymentData = $this->cashPayService->preparePaymentData($reservation, $validated['payment_method']);

            Log::info('Données de paiement préparées', $paymentData);

            // Initialiser le paiement
            $result = $this->cashPayService->initializePayment($paymentData);

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Erreur lors de l\'initialisation du paiement');
            }

            // Sauvegarder les informations de paiement
            $updateData = [
                'payment_reference' => $result['order_reference'],
                'payment_method' => $validated['payment_method'],
            ];

            // Ajouter payment_status seulement si la colonne existe
            if ($this->hasPaymentStatusColumn()) {
                $updateData['payment_status'] = 'pending';
            }

            $reservation->update($updateData);

            Log::info('Paiement initialisé avec succès', [
                'reservation_id' => $reservation->id,
                'order_reference' => $result['order_reference'],
                'payment_method' => $validated['payment_method'],
                'bill_url' => $result['bill_url']
            ]);

            // Rediriger vers la page de paiement CashPay
            return redirect()->away($result['bill_url']);

        } catch (Exception $e) {
            Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erreur lors de l\'initialisation du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Callback webhook de CashPay
     */
    public function callback(Request $request)
    {
        Log::info('Callback CashPay reçu', [
            'method' => $request->method(),
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Vérifier la présence du token
            $token = $request->input('data') ?? $request->input('token');

            if (!$token) {
                Log::warning('Callback CashPay reçu sans token');
                return response()->json(['status' => 'error', 'message' => 'Token manquant'], 400);
            }

            // Décoder le token JWT
            $result = $this->cashPayService->decodeCallbackToken($token);

            if (!$result['success']) {
                Log::error('Erreur décodage token JWT: ' . $result['error']);
                return response()->json(['status' => 'error', 'message' => 'Token invalide'], 400);
            }

            $decodedData = $result['data'];
            Log::info('Token JWT décodé avec succès', $decodedData);

            // Traiter les données du callback
            $this->processPaymentCallback($decodedData);

            return response()->json(['status' => 'success', 'message' => 'Callback traité']);

        } catch (Exception $e) {
            Log::error('Erreur traitement callback: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Erreur interne'], 500);
        }
    }

    /**
     * Traiter les données du callback de paiement
     */
    private function processPaymentCallback(array $data)
    {
        try {
            // Récupérer la référence de commande
            $orderReference = $data['order_reference'] ?? $data['reference'] ?? $data['merchant_reference'] ?? null;
            $status = $data['status'] ?? $data['transaction_status'] ?? null;

            if (!$orderReference) {
                Log::error('Référence de commande manquante dans le callback', $data);
                return;
            }

            // Trouver la réservation correspondante
            $reservation = Reservation::where('payment_reference', $orderReference)
                                    ->orWhere('transaction_ref', $orderReference)
                                    ->first();

            if (!$reservation) {
                Log::warning('Réservation non trouvée pour la référence: ' . $orderReference);
                return;
            }

            Log::info('Réservation trouvée', [
                'reservation_id' => $reservation->id,
                'order_reference' => $orderReference,
                'status' => $status
            ]);

            // Traiter selon le statut
            $this->updateReservationStatus($reservation, $status, $data);

        } catch (Exception $e) {
            Log::error('Erreur traitement callback: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le statut de la réservation selon le résultat du paiement
     */
    private function updateReservationStatus(Reservation $reservation, $status, array $callbackData)
    {
        $updateData = [];

        switch (strtoupper($status)) {
            case 'SUCCESS':
            case 'PAID':
            case 'COMPLETED':
                $updateData['statut'] = 'confirmée';
                if ($this->hasPaymentStatusColumn()) {
                    $updateData['payment_status'] = 'paid';
                    $updateData['paid_at'] = now();
                }

                $reservation->update($updateData);

                Log::info('Paiement confirmé avec succès', [
                    'reservation_id' => $reservation->id,
                    'status' => $status
                ]);

                // Envoyer email de confirmation
                $this->sendConfirmationEmail($reservation);
                break;

            case 'FAILED':
            case 'CANCELLED':
            case 'EXPIRED':
                $updateData['statut'] = 'échoué';
                if ($this->hasPaymentStatusColumn()) {
                    $updateData['payment_status'] = 'failed';
                }

                $reservation->update($updateData);

                Log::warning('Paiement échoué', [
                    'reservation_id' => $reservation->id,
                    'status' => $status
                ]);
                break;

            case 'PENDING':
            case 'PROCESSING':
                // Ne pas changer le statut, juste logger
                Log::info('Paiement en cours de traitement', [
                    'reservation_id' => $reservation->id,
                    'status' => $status
                ]);
                break;

            default:
                Log::warning('Statut de paiement inconnu', [
                    'reservation_id' => $reservation->id,
                    'status' => $status,
                    'callback_data' => $callbackData
                ]);
                break;
        }
    }

    /**
     * Page de retour après paiement
     */
    public function return(Request $request)
    {
        Log::info('Retour de paiement', $request->all());

        $reference = $request->get('reference') ?? $request->get('order_reference');
        $status = $request->get('status');

        if ($reference) {
            $reservation = Reservation::where('payment_reference', $reference)
                                    ->orWhere('transaction_ref', $reference)
                                    ->first();

            if ($reservation) {
                if (in_array(strtolower($status), ['success', 'paid', 'completed'])) {
                    return redirect()->route('payment.success', $reservation)
                        ->with('success', 'Paiement effectué avec succès !');
                } else {
                    return redirect()->route('payment.show', $reservation)
                        ->with('error', 'Le paiement a échoué ou a été annulé.');
                }
            }
        }

        return redirect()->route('home')
            ->with('info', 'Retour de paiement traité. Vous recevrez une confirmation par email.');
    }

    /**
     * Page de succès après paiement
     */
    public function success(Reservation $reservation)
    {
        // Charger la relation chambre
        $reservation->load('chambre');

        return view('payment.success', compact('reservation'));
    }

    /**
     * Page de simulation de paiement
     */
    public function simulation(Request $request)
    {
        $amount = $request->get('amount', 0);
        $reference = $request->get('reference', 'SIM-' . uniqid());

        return view('payment.simulation', compact('amount', 'reference'));
    }

    /**
     * Finaliser la simulation de paiement
     */
    public function completeSimulation(Request $request)
    {
        $action = $request->input('action');
        $reference = $request->input('reference');

        if ($action === 'success') {
            // Trouver la réservation par référence ou la plus récente
            $reservation = null;

            if ($reference && $reference !== 'SIM-' . uniqid()) {
                $reservation = Reservation::where('payment_reference', $reference)
                                        ->orWhere('transaction_ref', $reference)
                                        ->first();
            }

            if (!$reservation) {
                $reservation = Reservation::where('statut', 'pending')->latest()->first();
            }

            if ($reservation) {
                // Marquer la réservation comme payée
                $updateData = ['statut' => 'confirmée'];
                if ($this->hasPaymentStatusColumn()) {
                    $updateData['payment_status'] = 'paid';
                    $updateData['paid_at'] = now();
                }

                $reservation->update($updateData);

                // Charger la relation chambre pour l'email
                $reservation->load('chambre');

                // Envoyer l'email de confirmation
                $this->sendConfirmationEmail($reservation);

                return redirect()->route('payment.success', $reservation)
                    ->with('success', '🎉 Paiement simulé confirmé avec succès !')
                    ->with('info', '📧 Email de confirmation envoyé');
            } else {
                return redirect()->route('home')
                    ->with('success', '✅ Simulation de paiement terminée');
            }
        } else {
            return redirect()->route('home')
                ->with('warning', '❌ Paiement simulé annulé par l\'utilisateur');
        }
    }

    /**
     * Télécharger le reçu de paiement
     */
    public function downloadReceipt(Reservation $reservation)
    {
        try {
            // Charger la relation chambre
            $reservation->load('chambre');

            // Vérifier que la réservation est payée
            if ($reservation->statut !== 'confirmée') {
                return back()->with('error', 'Reçu disponible uniquement pour les réservations confirmées.');
            }

            // Générer le PDF
            $pdf = Pdf::loadView('payment.receipt', compact('reservation'));

            // Nom du fichier
            $fileName = 'recu-reservation-' . $reservation->id . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($fileName);

        } catch (Exception $e) {
            Log::error('Erreur génération reçu: ' . $e->getMessage());
            return back()->with('error', 'Impossible de générer le reçu.');
        }
    }

    /**
     * Route de test pour vérifier la configuration (admin seulement)
     */
    public function testConnection()
    {
        // Vérification des permissions admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $result = $this->cashPayService->testConnection();
            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Erreur test connexion CashPay: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer l'email de confirmation
     */
    private function sendConfirmationEmail(Reservation $reservation)
    {
        try {
            if (!$reservation->relationLoaded('chambre')) {
                $reservation->load('chambre');
            }

            Mail::to($reservation->client_email)->send(new ReservationConfirmationMail($reservation));

            Log::info('Email de confirmation envoyé', [
                'reservation_id' => $reservation->id,
                'email' => $reservation->client_email
            ]);

        } catch (Exception $e) {
            Log::error('Erreur envoi email confirmation: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id
            ]);
        }
    }

    /**
     * Vérifier si la colonne payment_status existe
     */
   private function hasPaymentStatusColumn(): bool
    {
        try {
            return Schema::hasColumn('reservations', 'payment_status'); // ✅ Maintenant Schema est importé
        } catch (Exception $e) {
            Log::warning('Erreur vérification colonne payment_status: ' . $e->getMessage());
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CinetPayService;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PaymentController extends Controller
{
    private $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    /**
     * Initier un paiement pour une réservation
     */
    public function initiatePayment(Request $request, $reservationId)
    {
        try {
            // Récupérer la réservation avec toutes les relations nécessaires
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            // Vérifier que la réservation est en attente de paiement
            if ($reservation->statut !== 'en_attente') {
                return back()->with('error', 'Cette réservation ne peut plus être payée.');
            }

            // Initier le paiement via CinetPay avec les données complètes
            $result = $this->cinetPayService->initiatePayment($reservation);

            if ($result['success']) {
                Log::info('Paiement initié avec succès', [
                    'reservation_id' => $reservation->id,
                    'transaction_id' => $result['transaction_id']
                ]);

                // Rediriger vers le guichet de paiement CinetPay
                return redirect($result['payment_url']);
            } else {
                Log::error('Échec initiation paiement', [
                    'reservation_id' => $reservation->id,
                    'error' => $result['message']
                ]);

                return back()->with('error', $result['message']);
            }

        } catch (Exception $e) {
            Log::error('Erreur initiation paiement', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur technique s\'est produite. Veuillez réessayer.');
        }
    }

    /**
     * Page de retour après paiement CinetPay
     */
    public function returnFromPayment(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            // Récupérer les paramètres de retour CinetPay
            $transactionId = $request->input('transaction_id') ?? $reservation->transaction_id;
            $token = $request->input('token');

            if ($transactionId) {
                // Vérifier le statut du paiement auprès de CinetPay
                $status = $this->cinetPayService->checkPaymentStatus($transactionId);

                if ($status['success'] && $status['status'] === 'COMPLETED') {
                    // Paiement réussi
                    DB::transaction(function () use ($reservation, $status) {
                        $reservation->update([
                            'statut' => 'confirmee',
                            'statut_paiement' => 'paye',
                            'date_paiement' => now(),
                            'montant_paye' => $status['amount'],
                            'methode_paiement' => $status['payment_method'] ?? 'CinetPay'
                        ]);
                    });

                    // Envoyer email de confirmation
                    try {
                        if ($reservation->user && $reservation->user->email) {
                            Mail::to($reservation->user->email)->send(new ReservationConfirmationMail($reservation));
                        }
                    } catch (Exception $e) {
                        Log::warning('Erreur envoi email', ['error' => $e->getMessage()]);
                    }

                    return redirect()->route('payment.success', $reservation->id);

                } elseif ($status['status'] === 'FAILED') {
                    // Paiement échoué
                    $reservation->update(['statut_paiement' => 'echec']);
                    return view('payment.failed', compact('reservation'));

                } else {
                    // Paiement en cours
                    return view('payment.pending', compact('reservation'));
                }
            }

            // Statut indéterminé
            return view('payment.pending', compact('reservation'));

        } catch (Exception $e) {
            Log::error('Erreur page de retour', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('chambres.index')->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Webhook CinetPay pour les notifications de paiement
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('CinetPay: Webhook reçu', $request->all());

            // Traiter le webhook selon les spécifications CinetPay
            $webhookData = $this->cinetPayService->handleWebhook($request->all());

            if ($webhookData) {
                $transactionId = $webhookData['transaction_id'];
                $status = $webhookData['status'];

                // Trouver la réservation correspondante
                $reservation = Reservation::where('transaction_id', $transactionId)->first();

                if ($reservation) {
                    if ($status === 'COMPLETED') {
                        // Paiement confirmé - Mise à jour selon les données réelles de CinetPay
                        DB::transaction(function () use ($reservation, $webhookData) {
                            $reservation->update([
                                'statut' => 'confirmee',
                                'statut_paiement' => 'paye',
                                'date_paiement' => now(),
                                'montant_paye' => $webhookData['amount'],
                                'methode_paiement' => $webhookData['payment_data']['payment_method'] ?? 'CinetPay',
                                'cinetpay_payment_data' => json_encode($webhookData['payment_data'])
                            ]);

                            // Libérer la chambre si elle était bloquée
                            if ($reservation->chambre) {
                                $reservation->chambre->update(['statut' => 'disponible']);
                            }
                        });

                        // Envoyer l'email de confirmation avec les vraies données
                        try {
                            if ($reservation->user && $reservation->user->email) {
                                Mail::to($reservation->user->email)->send(new ReservationConfirmationMail($reservation));
                            }
                        } catch (Exception $e) {
                            Log::warning('Erreur envoi email confirmation', [
                                'reservation_id' => $reservation->id,
                                'error' => $e->getMessage()
                            ]);
                        }

                        Log::info('CinetPay: Paiement confirmé via webhook', [
                            'reservation_id' => $reservation->id,
                            'transaction_id' => $transactionId,
                            'amount' => $webhookData['amount']
                        ]);

                    } else {
                        // Paiement échoué ou en attente
                        $reservation->update([
                            'statut_paiement' => 'echec',
                            'notes_paiement' => 'Paiement échoué - ' . ($webhookData['message'] ?? 'Raison inconnue')
                        ]);

                        Log::info('CinetPay: Paiement échoué via webhook', [
                            'reservation_id' => $reservation->id,
                            'transaction_id' => $transactionId,
                            'status' => $status
                        ]);
                    }

                    // Réponse pour CinetPay
                    return response('OK', 200);
                } else {
                    Log::warning('CinetPay: Réservation non trouvée', [
                        'transaction_id' => $transactionId
                    ]);
                    return response('Transaction not found', 404);
                }
            } else {
                Log::warning('CinetPay: Webhook invalide');
                return response('Invalid webhook data', 400);
            }

        } catch (Exception $e) {
            Log::error('CinetPay: Erreur traitement webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response('Webhook processing error', 500);
        }
    }

    /**
     * Vérifier manuellement le statut d'un paiement
     */
    public function checkPaymentStatus(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::findOrFail($reservationId);

            if ($reservation->transaction_id) {
                $status = $this->cinetPayService->checkPaymentStatus($reservation->transaction_id);

                if ($status['success']) {
                    return response()->json([
                        'success' => true,
                        'status' => $status['status'],
                        'message' => 'Statut vérifié avec succès'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible de vérifier le statut'
            ]);

        } catch (Exception $e) {
            Log::error('Erreur vérification statut', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification'
            ]);
        }
    }

    /**
     * Page de succès après paiement
     */
    public function success(Reservation $reservation)
    {
        if ($reservation->statut_paiement !== 'paye') {
            return redirect()->route('chambres.index')->with('warning', 'Ce paiement n\'est pas encore confirmé.');
        }

        return view('payment.success', compact('reservation'));
    }

    /**
     * Télécharger le reçu de paiement
     */
    public function downloadReceipt(Reservation $reservation)
    {
        if ($reservation->statut_paiement !== 'paye') {
            return back()->with('error', 'Le reçu n\'est disponible que pour les paiements confirmés.');
        }

        $pdf = Pdf::loadView('payment.receipt', compact('reservation'));

        return $pdf->download('recu-reservation-' . $reservation->id . '.pdf');
    }
}

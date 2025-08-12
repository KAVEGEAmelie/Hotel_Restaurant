<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\CinetPayService;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
            // Validation des données
            $validator = Validator::make($request->all(), [
                'customer_phone' => 'nullable|string|max:20',
                'customer_address' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Récupérer la réservation
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            // Vérifier que la réservation est en attente de paiement
            if ($reservation->statut !== 'en_attente') {
                return back()->with('error', 'Cette réservation ne peut plus être payée.');
            }

            // Générer un ID de transaction unique
            $transactionId = $this->cinetPayService->generateTransactionId($reservation->id);

            // Préparer les données pour le paiement
            $paymentData = [
                'amount' => $this->cinetPayService->formatAmount($reservation->prix_total),
                'currency' => 'XOF', // Ajouter explicitement la devise
                'transaction_id' => $transactionId,
                'description' => "Réservation #{$reservation->id} - {$reservation->chambre->nom}",
                'return_url' => route('payment.return', ['reservation' => $reservation->id]),
                'customer_name' => $reservation->user->name,
                'customer_surname' => $reservation->user->prenom ?? '',
                'customer_email' => $reservation->user->email,
                'customer_phone' => $request->customer_phone ?? $reservation->user->telephone,
                'customer_address' => $request->customer_address ?? '',
                'customer_city' => 'Lomé',
                'customer_country' => 'TG',
                'customer_state' => 'Maritime',
                'customer_zip_code' => '00000',
            ];

            // Initier le paiement via CinetPay
            $result = $this->cinetPayService->initiatePayment($paymentData);

            if ($result['success']) {
                // Sauvegarder les détails du paiement dans la réservation
                $reservation->update([
                    'transaction_id' => $transactionId,
                    'payment_url' => $result['payment_url'],
                    'payment_token' => $result['payment_token'] ?? null,
                ]);

                Log::info('Payment initiated for reservation', [
                    'reservation_id' => $reservation->id,
                    'transaction_id' => $transactionId,
                    'amount' => $reservation->prix_total
                ]);

                // Rediriger vers CinetPay
                return redirect($result['payment_url']);

            } else {
                Log::error('Payment initiation failed', [
                    'reservation_id' => $reservation->id,
                    'error' => $result['message']
                ]);

                return back()->with('error', 'Erreur lors de l\'initiation du paiement : ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Payment controller error', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur technique est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Page de retour après paiement
     */
    public function returnFromPayment(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            // Vérifier le statut du paiement
            if ($reservation->transaction_id) {
                $paymentStatus = $this->cinetPayService->checkPaymentStatus($reservation->transaction_id);

                if ($paymentStatus['success']) {
                    $status = $paymentStatus['status'];

                    if ($status === 'ACCEPTED' || $status === 'COMPLETED') {
                        // Paiement réussi
                        $reservation->update([
                            'statut' => 'confirmee',
                            'payment_status' => 'paid',
                            'payment_date' => now(),
                            'payment_method' => $paymentStatus['payment_method'] ?? 'cinetpay',
                            'operator_id' => $paymentStatus['operator_id'] ?? null,
                        ]);

                        Log::info('Payment completed successfully', [
                            'reservation_id' => $reservation->id,
                            'transaction_id' => $reservation->transaction_id
                        ]);

                        return view('reservations.payment-success', compact('reservation'));

                    } elseif ($status === 'REFUSED' || $status === 'CANCELLED') {
                        // Paiement échoué
                        $reservation->update([
                            'payment_status' => 'failed',
                        ]);

                        return view('reservations.payment-failed', compact('reservation'));

                    } else {
                        // Paiement en cours
                        return view('reservations.payment-pending', compact('reservation'));
                    }
                }
            }

            // Statut indéterminé
            return view('reservations.payment-pending', compact('reservation'));

        } catch (\Exception $e) {
            Log::error('Payment return error', [
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
            Log::info('CinetPay webhook received', $request->all());

            // Traiter le webhook
            $webhookData = $this->cinetPayService->handleWebhook($request->all());

            if ($webhookData) {
                $transactionId = $webhookData['transaction_id'];
                $status = $webhookData['status'];

                // Trouver la réservation correspondante
                $reservation = Reservation::where('transaction_id', $transactionId)->first();

                if ($reservation) {
                    if ($status === 'ACCEPTED' || $status === 'COMPLETED') {
                        // Paiement réussi
                        $reservation->update([
                            'statut' => 'confirmee',
                            'payment_status' => 'paid',
                            'payment_date' => now(),
                            'payment_method' => $webhookData['payment_method'] ?? 'cinetpay',
                            'operator_id' => $webhookData['operator_id'] ?? null,
                        ]);

                        // Envoyer l'email de confirmation (optionnel)
                        Mail::to($reservation->user->email)->send(new ReservationConfirmationMail($reservation));

                        Log::info('Payment confirmed via webhook', [
                            'reservation_id' => $reservation->id,
                            'transaction_id' => $transactionId
                        ]);

                    } elseif ($status === 'REFUSED' || $status === 'CANCELLED') {
                        // Paiement échoué
                        $reservation->update([
                            'payment_status' => 'failed',
                        ]);

                        Log::info('Payment failed via webhook', [
                            'reservation_id' => $reservation->id,
                            'transaction_id' => $transactionId
                        ]);
                    }

                    return response('OK', 200);
                } else {
                    Log::warning('Reservation not found for webhook', ['transaction_id' => $transactionId]);
                }
            }

            return response('Invalid webhook data', 400);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response('Webhook processing failed', 500);
        }
    }

    /**
     * Vérifier manuellement le statut d'un paiement
     */
    public function checkPaymentStatus(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::findOrFail($reservationId);

            if (!$reservation->transaction_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune transaction trouvée pour cette réservation'
                ]);
            }

            $paymentStatus = $this->cinetPayService->checkPaymentStatus($reservation->transaction_id);

            if ($paymentStatus['success']) {
                $status = $paymentStatus['status'];

                // Mettre à jour le statut si nécessaire
                if ($status === 'ACCEPTED' || $status === 'COMPLETED') {
                    $reservation->update([
                        'statut' => 'confirmee',
                        'payment_status' => 'paid',
                        'payment_date' => now(),
                    ]);
                } elseif ($status === 'REFUSED' || $status === 'CANCELLED') {
                    $reservation->update([
                        'payment_status' => 'failed',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'status' => $status,
                    'reservation_status' => $reservation->fresh()->statut
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $paymentStatus['message']
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check error', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ]);
        }
    }

    /**
     * Page de succès après paiement
     */
    public function success(Reservation $reservation)
    {
        // Charger la relation chambre
        $reservation->load('chambre', 'user');

        return view('payment.success', compact('reservation'));
    }

    /**
     * Télécharger le reçu de paiement
     */
    public function downloadReceipt(Reservation $reservation)
    {
        try {
            // Charger la relation chambre
            $reservation->load('chambre', 'user');

            // Vérifier que la réservation est payée
            if ($reservation->statut !== 'confirmee') {
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Mail\ReservationConfirmationMail;
use App\Services\CashPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PaymentController extends Controller
{
    protected $cashPayService;

    public function __construct(CashPayService $cashPayService)
    {
        $this->cashPayService = $cashPayService;
    }

    /**
     * Initier un paiement pour une réservation avec CashPay V2.0
     */
    public function initiatePayment(Request $request, $reservationId)
    {
        try {
            Log::info('CashPay: Début initiation paiement', [
                'reservation_id' => $reservationId,
                'request_data' => $request->all()
            ]);

            // Récupérer la réservation
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            Log::info('CashPay: Réservation trouvée', [
                'reservation_id' => $reservation->id,
                'statut' => $reservation->statut,
                'prix_total' => $reservation->prix_total
            ]);

            // Vérifier que la réservation est en attente de paiement
            if ($reservation->statut !== 'en_attente') {
                Log::warning('CashPay: Réservation pas en attente', [
                    'reservation_id' => $reservationId,
                    'statut_actuel' => $reservation->statut
                ]);
                return back()->with('error', 'Cette réservation ne peut plus être payée.');
            }

            Log::info('CashPay: Appel createBill...');
            
            // Créer la facture avec CashPay V2.0
            $billResult = $this->cashPayService->createBill($reservation);

            Log::info('CashPay: Résultat createBill', [
                'success' => $billResult['success'] ?? false,
                'bill_url' => $billResult['bill_url'] ?? 'NON DÉFINI',
                'message' => $billResult['message'] ?? 'Pas de message'
            ]);

            if (!$billResult['success']) {
                Log::error('CashPay: Échec création facture', [
                    'reservation_id' => $reservationId,
                    'error' => $billResult['message']
                ]);
                return back()->with('error', 'Erreur lors de la création du paiement: ' . $billResult['message']);
            }

            // Vérifier que l'URL de la facture est valide
            if (empty($billResult['bill_url'])) {
                Log::error('CashPay: URL de facture vide', [
                    'reservation_id' => $reservationId,
                    'billResult' => $billResult
                ]);
                return back()->with('error', 'Erreur lors de la génération du lien de paiement. Veuillez réessayer.');
            }

            Log::info('CashPay: Redirection vers le paiement', [
                'reservation_id' => $reservationId,
                'bill_url' => $billResult['bill_url']
            ]);

            // Rediriger vers l'URL de la facture CashPay
            return redirect($billResult['bill_url']);

        } catch (Exception $e) {
            Log::error('Erreur initiation paiement CashPay', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur technique s\'est produite. Veuillez réessayer.');
        }
    }

    /**
     * Page de retour après paiement CashPay
     */
    public function paymentReturn(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::with(['chambre', 'user'])->findOrFail($reservationId);

            Log::info('CashPay: Retour de paiement', [
                'reservation_id' => $reservationId,
                'params' => $request->all()
            ]);

            // Afficher la page en attente pendant la vérification
            return view('payment.pending', compact('reservation'));

        } catch (Exception $e) {
            Log::error('Erreur page de retour CashPay', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('chambres.index')->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Webhook CashPay pour les notifications de paiement
     */
    public function handleWebhook(Request $request)
    {
        try {
            $webhookData = $request->all();
            
            Log::info('CashPay: Webhook reçu', ['data' => $webhookData]);

            // Traiter le webhook selon la documentation CashPay V2.0
            $webhookResult = $this->cashPayService->handleWebhook($webhookData);

            if (!$webhookResult) {
                return response()->json(['error' => 'Invalid webhook data'], 400);
            }

            // Trouver la réservation correspondante
            $reservation = Reservation::where('cashpay_merchant_reference', $webhookResult['merchant_reference'])
                ->orWhere('cashpay_order_reference', $webhookResult['order_reference'])
                ->first();

            if (!$reservation) {
                Log::warning('CashPay: Réservation non trouvée pour webhook', [
                    'merchant_reference' => $webhookResult['merchant_reference'],
                    'order_reference' => $webhookResult['order_reference']
                ]);
                return response()->json(['error' => 'Reservation not found'], 404);
            }

            // Mettre à jour la réservation selon l'état du paiement
            $this->updateReservationFromWebhook($reservation, $webhookResult);

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            Log::error('CashPay: Erreur traitement webhook', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement (AJAX)
     */
    public function checkPaymentStatus(Reservation $reservation)
    {
        try {
            // Recharger la réservation depuis la base de données
            $reservation->refresh();

            return response()->json([
                'success' => true,
                'reservation_status' => $reservation->statut_paiement,
                'status' => $reservation->statut,
                'amount_paid' => $reservation->montant_paye,
                'payment_method' => $reservation->methode_paiement,
                'payment_date' => $reservation->date_paiement,
            ]);

        } catch (Exception $e) {
            Log::error('Erreur vérification statut paiement', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ], 500);
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
     * Page d'échec de paiement
     */
    public function failed(Reservation $reservation)
    {
        return view('payment.failed', compact('reservation'));
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

    /**
     * Test de l'intégration CashPay
     */
    public function testCashPay()
    {
        try {
            $result = $this->cashPayService->testAuthentication();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour la réservation depuis un webhook
     */
    private function updateReservationFromWebhook($reservation, $webhookResult)
    {
        DB::transaction(function () use ($reservation, $webhookResult) {
            $updateData = [
                'cashpay_webhook_data' => json_encode($webhookResult),
                'cashpay_status' => $webhookResult['state'],
                'updated_at' => now()
            ];

            // Selon la documentation, l'état peut être : Pending, Paid, Error, Cancelled, Partial, Excess
            switch ($webhookResult['state']) {
                case 'Paid':
                    $updateData = array_merge($updateData, [
                        'statut' => 'confirmee',
                        'statut_paiement' => 'paye',
                        'date_paiement' => now(),
                        'montant_paye' => $webhookResult['received_amount'] ?? $reservation->prix_total,
                        'methode_paiement' => 'CashPay'
                    ]);

                    // Envoyer email de confirmation
                    try {
                        if ($reservation->user && $reservation->user->email) {
                            Mail::to($reservation->user->email)->send(new ReservationConfirmationMail($reservation));
                        }
                    } catch (Exception $e) {
                        Log::warning('Erreur envoi email confirmation', ['error' => $e->getMessage()]);
                    }
                    break;

                case 'Error':
                case 'Cancelled':
                    $updateData['statut_paiement'] = 'echec';
                    break;

                case 'Partial':
                    $updateData['statut_paiement'] = 'partiel';
                    break;

                case 'Pending':
                default:
                    $updateData['statut_paiement'] = 'en_attente';
                    break;
            }

            $reservation->update($updateData);

            Log::info('CashPay: Réservation mise à jour', [
                'reservation_id' => $reservation->id,
                'new_status' => $updateData['statut_paiement'],
                'cashpay_state' => $webhookResult['state']
            ]);
        });
    }
}

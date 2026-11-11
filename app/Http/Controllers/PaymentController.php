<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmationMail;
use App\Models\Reservation;
use App\Services\CinetPayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(private CinetPayService $cinetPayService)
    {
    }

    /**
     * Initier un paiement pour une réservation
     */
    public function initiatePayment(Reservation $reservation)
    {
        if ($reservation->statut !== Reservation::STATUT_EN_ATTENTE || $reservation->estPayee()) {
            return back()->with('error', 'Cette réservation ne peut plus être payée.');
        }

        try {
            $reservation->load(['chambre', 'user']);
            $result = $this->cinetPayService->initiatePayment($reservation);

            if ($result['success']) {
                return redirect()->away($result['payment_url']);
            }

            return back()->with('error', $result['message']);
        } catch (Exception $e) {
            Log::error('Erreur initiation paiement', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur technique s\'est produite. Veuillez réessayer.');
        }
    }

    /**
     * Page de retour après paiement CinetPay (GET ou POST selon CinetPay)
     */
    public function returnFromPayment(Reservation $reservation)
    {
        try {
            // On ne fait jamais confiance aux paramètres reçus : on interroge l'API avec NOTRE transaction_id.
            if ($reservation->transaction_id && ! $reservation->estPayee()) {
                $status = $this->cinetPayService->checkPaymentStatus($reservation->transaction_id);
                $this->appliquerStatut($reservation, $status);
                $reservation->refresh();
            }

            if ($reservation->estPayee()) {
                return redirect()->route('payment.success', $reservation);
            }

            if ($reservation->statut_paiement === Reservation::PAIEMENT_ECHEC) {
                return view('payment.failed', compact('reservation'));
            }

            return view('payment.pending', compact('reservation'));
        } catch (Exception $e) {
            Log::error('Erreur page de retour', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('chambres.index')->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Webhook CinetPay (notification serveur à serveur)
     */
    public function webhook(Request $request)
    {
        // CinetPay teste l'URL en GET avant d'envoyer les notifications.
        if ($request->isMethod('get')) {
            return response('OK', 200);
        }

        try {
            $transactionId = $this->cinetPayService->handleWebhook($request->all());

            if (! $transactionId) {
                return response('Invalid webhook data', 400);
            }

            $reservation = Reservation::where('transaction_id', $transactionId)->first();

            if (! $reservation) {
                Log::warning('CinetPay: Réservation non trouvée', ['transaction_id' => $transactionId]);

                return response('Transaction not found', 404);
            }

            if (! $reservation->estPayee()) {
                $status = $this->cinetPayService->checkPaymentStatus($transactionId);
                $this->appliquerStatut($reservation, $status);
            }

            return response('OK', 200);
        } catch (Exception $e) {
            Log::error('CinetPay: Erreur traitement webhook', ['error' => $e->getMessage()]);

            return response('Webhook processing error', 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement (appel AJAX depuis la page "en attente")
     */
    public function checkPaymentStatus(Reservation $reservation)
    {
        if ($reservation->transaction_id && ! $reservation->estPayee()) {
            $status = $this->cinetPayService->checkPaymentStatus($reservation->transaction_id);
            $this->appliquerStatut($reservation, $status);
            $reservation->refresh();
        }

        return response()->json([
            'success' => true,
            'statut_paiement' => $reservation->statut_paiement,
            'redirect' => $reservation->estPayee() ? route('payment.success', $reservation) : null,
        ]);
    }

    /**
     * Page de succès après paiement
     */
    public function success(Reservation $reservation)
    {
        if (! $reservation->estPayee()) {
            return redirect()->route('chambres.index')->with('warning', 'Ce paiement n\'est pas encore confirmé.');
        }

        $reservation->load('chambre');

        return view('payment.success', compact('reservation'));
    }

    /**
     * Télécharger le reçu de paiement
     */
    public function downloadReceipt(Reservation $reservation)
    {
        if (! $reservation->estPayee()) {
            return back()->with('error', 'Le reçu n\'est disponible que pour les paiements confirmés.');
        }

        $reservation->load('chambre');
        $pdf = Pdf::loadView('payment.receipt', compact('reservation'));

        return $pdf->download('recu-reservation-'.$reservation->id.'.pdf');
    }

    /**
     * Applique le statut renvoyé par l'API CinetPay à la réservation.
     * Idempotent : un paiement déjà confirmé n'est jamais retraité (pas de double email).
     */
    private function appliquerStatut(Reservation $reservation, array $status): void
    {
        if ($status['status'] === 'COMPLETED') {
            // Le montant payé doit couvrir le montant attendu.
            $attendu = $this->cinetPayService->formatAmount($reservation->prix_total);
            if ((int) $status['amount'] < $attendu) {
                Log::error('CinetPay: Montant payé insuffisant', [
                    'reservation_id' => $reservation->id,
                    'attendu' => $attendu,
                    'recu' => $status['amount'],
                ]);
                $reservation->update([
                    'statut_paiement' => Reservation::PAIEMENT_ECHEC,
                    'notes_paiement' => "Montant reçu ({$status['amount']}) inférieur au montant attendu ({$attendu})",
                ]);

                return;
            }

            $confirmee = DB::transaction(function () use ($reservation, $status) {
                $fresh = Reservation::whereKey($reservation->id)->lockForUpdate()->first();
                if ($fresh->estPayee()) {
                    return false;
                }

                // Paiement arrivé après l'expiration automatique et chambre reprise entre-temps :
                // on enregistre le paiement mais on signale qu'un remboursement est nécessaire.
                $chambreReprise = $fresh->statut === Reservation::STATUT_ANNULEE
                    && Reservation::where('chambre_id', $fresh->chambre_id)
                        ->whereKeyNot($fresh->id)
                        ->chevauche($fresh->check_in_date, $fresh->check_out_date)
                        ->exists();

                $fresh->update([
                    'statut' => $chambreReprise ? Reservation::STATUT_ANNULEE : Reservation::STATUT_CONFIRMEE,
                    'notes_paiement' => $chambreReprise ? 'Payée après expiration, chambre déjà reprise : REMBOURSEMENT À EFFECTUER' : null,
                    'statut_paiement' => Reservation::PAIEMENT_PAYE,
                    'date_paiement' => now(),
                    'montant_paye' => $status['amount'],
                    'methode_paiement' => $status['payment_method'] ?? 'CinetPay',
                    'operator_id' => $status['operator_id'] ?? null,
                    'cinetpay_payment_data' => json_encode($status['raw_data'] ?? []),
                ]);

                return ! $chambreReprise;
            });

            if ($confirmee) {
                Log::info('CinetPay: Paiement confirmé', ['reservation_id' => $reservation->id]);
                $this->envoyerConfirmation($reservation->fresh('chambre'));
            }

            return;
        }

        if ($status['status'] === 'FAILED') {
            $reservation->update([
                'statut_paiement' => Reservation::PAIEMENT_ECHEC,
                'notes_paiement' => 'Paiement échoué - '.($status['message'] ?? 'Raison inconnue'),
            ]);
        }

        // PENDING / ERROR : on ne change rien, on revérifiera plus tard.
    }

    private function envoyerConfirmation(Reservation $reservation): void
    {
        $email = $reservation->client_email ?: $reservation->user?->email;

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new ReservationConfirmationMail($reservation));
        } catch (Exception $e) {
            Log::warning('Erreur envoi email confirmation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

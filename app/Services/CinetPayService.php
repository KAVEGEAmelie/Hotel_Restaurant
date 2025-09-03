<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CinetPayService
{
    private $apiKey;
    private $siteId;
    private $secretKey;
    private $currency;
    private $environment;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key');
        $this->siteId = config('services.cinetpay.site_id');
        $this->secretKey = config('services.cinetpay.secret_key');
        $this->currency = config('services.cinetpay.currency', 'XOF');
        $this->environment = config('services.cinetpay.environment', 'sandbox');

        // URL de l'API CinetPay (même URL pour sandbox et production)
        $this->apiUrl = 'https://api-checkout.cinetpay.com/v2';
    }

    /**
     * Initier un paiement selon les spécifications CinetPay
     */
    public function initiatePayment($reservation, $paymentMethod = 'ALL')
    {
        try {
            // Générer un ID de transaction unique
            $transactionId = $this->generateTransactionId($reservation->id);

            // Données du formulaire de réservation
            $customerData = $this->extractCustomerData($reservation);

            // Données de paiement selon les spécifications CinetPay
            $paymentData = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId,
                'amount' => $this->formatAmount($reservation->prix_total),
                'currency' => $this->currency,
                'description' => "Réservation Hôtel Le Printemps - Chambre {$reservation->chambre->nom}",
                'notify_url' => route('payment.webhook'),
                'return_url' => route('payment.return', $reservation->id),
                'channels' => $paymentMethod, // Méthode de paiement choisie
                'lang' => 'fr',

                // Informations client du formulaire de réservation
                'customer_id' => $reservation->user_id ?? null,
                'customer_name' => $customerData['name'],
                'customer_surname' => $customerData['surname'],
                'customer_email' => $customerData['email'],
                'customer_phone_number' => $customerData['phone'],
                'customer_address' => $customerData['address'],
                'customer_city' => $customerData['city'],
                'customer_country' => $customerData['country'],
                'customer_state' => $customerData['state'],
                'customer_zip_code' => $customerData['zip_code'],

                // Données de facture personnalisées
                'invoice_data' => [
                    'Chambre' => "Chambre {$reservation->chambre->nom}",
                    'Période' => "Du {$reservation->check_in_date->format('d/m/Y')} au {$reservation->check_out_date->format('d/m/Y')}",
                    'Durée' => $reservation->check_in_date->diffInDays($reservation->check_out_date) . " nuit(s)"
                ],

                // Métadonnées pour identifier la réservation
                'metadata' => json_encode([
                    'reservation_id' => $reservation->id,
                    'chambre_id' => $reservation->chambre_id,
                    'user_id' => $reservation->user_id,
                    'source' => 'hotel_reservation',
                    'payment_method' => $paymentMethod
                ])
            ];

            Log::info('CinetPay: Initiation du paiement', [
                'reservation_id' => $reservation->id,
                'transaction_id' => $transactionId,
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentMethod
            ]);

            // Appel à l'API CinetPay
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Hotel-Le-Printemps/1.0'
                ])
                ->post($this->apiUrl . '/payment', $paymentData);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] == '201') {
                // Mise à jour de la réservation avec les données CinetPay
                $reservation->update([
                    'transaction_id' => $transactionId,
                    'payment_url' => $result['data']['payment_url'],
                    'payment_token' => $result['data']['payment_token'],
                    'cinetpay_data' => json_encode($paymentData),
                    'statut_paiement' => 'en_attente'
                ]);

                Log::info('CinetPay: Paiement initié avec succès', [
                    'reservation_id' => $reservation->id,
                    'transaction_id' => $transactionId,
                    'payment_url' => $result['data']['payment_url']
                ]);

                return [
                    'success' => true,
                    'payment_url' => $result['data']['payment_url'],
                    'payment_token' => $result['data']['payment_token'],
                    'transaction_id' => $transactionId,
                    'message' => 'Lien de paiement généré avec succès'
                ];
            } else {
                Log::error('CinetPay: Échec initiation paiement', [
                    'reservation_id' => $reservation->id,
                    'response' => $result,
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Erreur lors de la génération du lien de paiement',
                    'error_code' => $result['code'] ?? 'unknown'
                ];
            }

        } catch (Exception $e) {
            Log::error('CinetPay: Erreur technique', [
                'reservation_id' => $reservation->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'initiation du paiement'
            ];
        }
    }

    /**
     * Vérifier le statut d'une transaction
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            $data = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Hotel-Le-Printemps/1.0'
                ])
                ->post($this->apiUrl . '/payment/check', $data);

            $result = $response->json();

            Log::info('CinetPay: Vérification statut', [
                'transaction_id' => $transactionId,
                'response_code' => $result['code'] ?? 'unknown'
            ]);

            if ($response->successful() && isset($result['code']) && $result['code'] == '00') {
                return [
                    'success' => true,
                    'status' => 'COMPLETED',
                    'amount' => $result['data']['amount'],
                    'currency' => $result['data']['currency'],
                    'operator_id' => $result['data']['operator_id'] ?? null,
                    'payment_method' => $result['data']['payment_method'] ?? null,
                    'payment_date' => $result['data']['payment_date'] ?? null,
                    'raw_data' => $result['data']
                ];
            } else {
                return [
                    'success' => false,
                    'status' => 'FAILED',
                    'message' => $result['message'] ?? 'Transaction non trouvée ou échouée',
                    'error_code' => $result['code'] ?? 'unknown'
                ];
            }

        } catch (Exception $e) {
            Log::error('CinetPay: Erreur vérification statut', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Erreur lors de la vérification du statut'
            ];
        }
    }

    /**
     * Traiter les notifications webhook de CinetPay
     */
    public function handleWebhook($requestData)
    {
        try {
            Log::info('CinetPay: Webhook reçu', ['data' => $requestData]);

            // Vérifier la présence des données essentielles
            if (!isset($requestData['cpm_trans_id'])) {
                Log::warning('CinetPay: Webhook sans transaction_id');
                return false;
            }

            $transactionId = $requestData['cpm_trans_id'];
            $siteId = $requestData['cpm_site_id'] ?? null;

            // Vérifier que le site_id correspond
            if ($siteId && $siteId != $this->siteId) {
                Log::warning('CinetPay: Site ID mismatch', [
                    'received' => $siteId,
                    'expected' => $this->siteId
                ]);
                return false;
            }

            // Vérifier le statut de la transaction directement avec l'API
            $status = $this->checkPaymentStatus($transactionId);

            if ($status['success'] && $status['status'] === 'COMPLETED') {
                Log::info('CinetPay: Paiement confirmé via webhook', [
                    'transaction_id' => $transactionId,
                    'amount' => $status['amount']
                ]);

                return [
                    'transaction_id' => $transactionId,
                    'status' => 'COMPLETED',
                    'amount' => $status['amount'],
                    'currency' => $status['currency'],
                    'payment_data' => $status['raw_data']
                ];
            }

            return false;

        } catch (Exception $e) {
            Log::error('CinetPay: Erreur traitement webhook', [
                'error' => $e->getMessage(),
                'data' => $requestData
            ]);

            return false;
        }
    }

    /**
     * Extraire les données client du formulaire de réservation
     */
    private function extractCustomerData($reservation)
    {
        // Récupérer les données du user ou des données de réservation
        $user = $reservation->user;

        return [
            'name' => $reservation->client_nom ?? $user->name ?? 'Client',
            'surname' => $reservation->client_prenom ?? $user->prenom ?? '',
            'email' => $reservation->client_email ?? $user->email ?? 'client@hotel.com',
            'phone' => $reservation->client_telephone ?? $user->telephone ?? '',
            'address' => $user->address ?? 'Adresse non fournie',
            'city' => 'Kpalimé',
            'country' => 'TG', // Togo
            'state' => 'Plateaux',
            'zip_code' => '00000'
        ];
    }

    /**
     * Formater le montant selon CinetPay (multiple de 5)
     */
    public function formatAmount($amount)
    {
        $amount = (int) $amount;

        // CinetPay exige des multiples de 5 pour XOF
        if ($this->currency === 'XOF') {
            $remainder = $amount % 5;
            if ($remainder !== 0) {
                $amount = $amount + (5 - $remainder);
            }
        }

        return $amount;
    }

    /**
     * Générer un ID de transaction unique selon CinetPay
     */
    public function generateTransactionId($reservationId)
    {
        // Format: PREFIX_RESERVATION_TIMESTAMP_RANDOM
        return 'HP_' . str_pad($reservationId, 4, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '_' . rand(100, 999);
    }

    /**
     * Obtenir les moyens de paiement disponibles
     */
    public function getAvailableChannels()
    {
        return [
            'ALL' => 'Tous les moyens de paiement',
            'MOBILE_MONEY' => 'Mobile Money uniquement',
            'CREDIT_CARD' => 'Carte bancaire uniquement',
            'WALLET' => 'Portefeuille électronique'
        ];
    }

    /**
     * Valider la configuration CinetPay
     */
    public function validateConfiguration()
    {
        $errors = [];

        if (empty($this->apiKey)) {
            $errors[] = 'API Key manquante';
        }

        if (empty($this->siteId)) {
            $errors[] = 'Site ID manquant';
        }

        if (empty($this->secretKey)) {
            $errors[] = 'Secret Key manquante';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

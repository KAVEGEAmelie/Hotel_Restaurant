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
        $this->currency = config('services.cinetpay.currency');
        $this->environment = config('services.cinetpay.environment');
        
        // URL de l'API CinetPay
        $this->apiUrl = $this->environment === 'live' 
            ? 'https://api-checkout.cinetpay.com/v2'
            : 'https://api-checkout.cinetpay.com/v2';
    }

    /**
     * Initier un paiement via l'API REST CinetPay
     */
    public function initiatePayment($data)
    {
        try {
            $paymentData = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $data['transaction_id'],
                'amount' => $data['amount'],
                'currency' => $this->currency,
                'description' => $data['description'],
                'return_url' => $data['return_url'],
                'notify_url' => config('services.cinetpay.webhook_url'),
                'customer_name' => $data['customer_name'],
                'customer_surname' => $data['customer_surname'] ?? '',
                'customer_email' => $data['customer_email'],
                'customer_phone_number' => $data['customer_phone'] ?? '',
                'customer_address' => $data['customer_address'] ?? '',
                'customer_city' => $data['customer_city'] ?? 'Lomé',
                'customer_country' => $data['customer_country'] ?? 'TG',
                'customer_state' => $data['customer_state'] ?? 'Maritime',
                'customer_zip_code' => $data['customer_zip_code'] ?? '00000',
            ];

            Log::info('CinetPay payment initiation request', $paymentData);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->apiUrl . '/payment', $paymentData);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] == '201') {
                Log::info('CinetPay payment initiated successfully', [
                    'transaction_id' => $data['transaction_id'],
                    'payment_url' => $result['data']['payment_url']
                ]);

                return [
                    'success' => true,
                    'payment_url' => $result['data']['payment_url'],
                    'payment_token' => $result['data']['payment_token'] ?? null,
                    'message' => 'Paiement initié avec succès'
                ];
            } else {
                Log::error('CinetPay payment initiation failed', [
                    'transaction_id' => $data['transaction_id'],
                    'response' => $result,
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Erreur lors de l\'initiation du paiement'
                ];
            }

        } catch (Exception $e) {
            Log::error('CinetPay service error', [
                'error' => $e->getMessage(),
                'transaction_id' => $data['transaction_id'] ?? 'unknown'
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'initiation du paiement'
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
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
                    'Accept' => 'application/json'
                ])
                ->post($this->apiUrl . '/payment/check', $data);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] == '00') {
                return [
                    'success' => true,
                    'status' => $result['data']['status'],
                    'amount' => $result['data']['amount'],
                    'currency' => $result['data']['currency'],
                    'operator_id' => $result['data']['operator_id'] ?? null,
                    'payment_method' => $result['data']['payment_method'] ?? null,
                    'payment_date' => $result['data']['payment_date'] ?? null,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Transaction non trouvée'
                ];
            }

        } catch (Exception $e) {
            Log::error('CinetPay status check error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ];
        }
    }

    /**
     * Traiter les notifications webhook
     */
    public function handleWebhook($data)
    {
        try {
            // Vérifier la signature du webhook
            if (!$this->verifyWebhookSignature($data)) {
                Log::warning('CinetPay webhook signature verification failed');
                return false;
            }

            $transactionId = $data['cpm_trans_id'] ?? null;
            $status = $data['cpm_result'] ?? null;
            $amount = $data['cpm_amount'] ?? null;

            if ($transactionId && $status) {
                Log::info('CinetPay webhook received', [
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'amount' => $amount
                ]);

                return [
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'amount' => $amount,
                    'currency' => $data['cpm_currency'] ?? $this->currency,
                    'operator_id' => $data['operator_id'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                ];
            }

            return false;

        } catch (Exception $e) {
            Log::error('CinetPay webhook processing error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return false;
        }
    }

    /**
     * Vérifier la signature du webhook
     */
    private function verifyWebhookSignature($data)
    {
        // Implémentation de la vérification de signature selon la documentation CinetPay
        // Pour l'instant, nous retournons true (à sécuriser en production)
        return true;
    }

    /**
     * Obtenir les moyens de paiement disponibles
     */
    public function getPaymentMethods()
    {
        return [
            'ORANGE_MONEY_BF' => 'Orange Money Burkina Faso',
            'ORANGE_MONEY_CI' => 'Orange Money Côte d\'Ivoire',
            'ORANGE_MONEY_SN' => 'Orange Money Sénégal',
            'ORANGE_MONEY_CM' => 'Orange Money Cameroun',
            'ORANGE_MONEY_ML' => 'Orange Money Mali',
            'MTN_MONEY_BF' => 'MTN Money Burkina Faso',
            'MTN_MONEY_CI' => 'MTN Money Côte d\'Ivoire',
            'MTN_MONEY_CM' => 'MTN Money Cameroun',
            'MOOV_MONEY_BF' => 'Moov Money Burkina Faso',
            'MOOV_MONEY_CI' => 'Moov Money Côte d\'Ivoire',
            'MOOV_MONEY_TG' => 'Moov Money Togo',
            'TMONEY_TG' => 'T-Money Togo',
            'CARD' => 'Carte bancaire',
        ];
    }

    /**
     * Formater le montant pour CinetPay
     */
    public function formatAmount($amount)
    {
        // CinetPay accepte les montants en centimes pour XOF
        if ($this->currency === 'XOF') {
            return (int) $amount; // Montant déjà en FCFA
        }
        
        return $amount;
    }

    /**
     * Générer un ID de transaction unique
     */
    public function generateTransactionId($reservationId)
    {
        return 'RES_' . $reservationId . '_' . time() . '_' . rand(1000, 9999);
    }
}

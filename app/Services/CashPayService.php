<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CashPayService
{
    private $baseUrl;
    private $username;
    private $password;
    private $clientId;
    private $clientSecret;
    private $apiReference;
    private $apiKey;
    private $currency;
    private $terminalCode;

    public function __construct()
    {
        $this->baseUrl = config('services.cashpay.base_url', 'https://sandbox.semoa-payments.com/api');
        $this->username = config('services.cashpay.username');
        $this->password = config('services.cashpay.password');
        $this->clientId = config('services.cashpay.client_id');
        $this->clientSecret = config('services.cashpay.client_secret');
        $this->apiReference = config('services.cashpay.api_reference');
        $this->apiKey = config('services.cashpay.api_key');
        $this->currency = config('services.cashpay.currency', 'XOF');
        $this->terminalCode = config('services.cashpay.terminal_code', 'TERMINAL_LEPRINTEMPS');
    }

    /**
     * Obtenir un token d'accès OAuth 2.0
     */
    public function getAccessToken()
    {
        try {
            $cacheKey = 'cashpay_access_token';
            
            // Vérifier si on a déjà un token en cache
            if ($token = Cache::get($cacheKey)) {
                return $token;
            }

            Log::info('CashPay: Demande de token OAuth 2.0');

            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'roles offline_access profile internal_access email client_access'
            ]);

            if (!$response->successful()) {
                Log::error('CashPay: Échec obtention token OAuth', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new Exception('Échec authentification OAuth 2.0');
            }

            $data = $response->json();
            $token = $data['access_token'];
            $expiresIn = $data['expires_in'] ?? 3600;

            // Mettre en cache le token (moins 5 minutes pour la sécurité)
            Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 300));

            Log::info('CashPay: Token OAuth obtenu avec succès');
            return $token;

        } catch (Exception $e) {
            Log::error('CashPay: Erreur obtention token', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Générer les en-têtes d'authentification OAuth 2.0
     */
    private function getAuthHeaders()
    {
        $token = $this->getAccessToken();
        
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
    }

    /**
     * Générer les en-têtes d'authentification CashPay (méthode legacy)
     */
    private function generateCashPayAuthHeaders()
    {
        $salt = time();
        $apisecure = hash('sha256', $this->username . $this->apiKey . $salt);

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'login' => $this->username,
            'apireference' => $this->apiReference,
            'salt' => (string) $salt,
            'apisecure' => $apisecure
        ];
    }

    /**
     * Tester l'authentification OAuth 2.0
     */
    public function testAuthentication()
    {
        try {
            $headers = $this->getAuthHeaders();

            Log::info('CashPay: Test d\'authentification OAuth 2.0');

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->baseUrl . '/ping');

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CashPay: Authentification OAuth réussie', ['response' => $result]);
                
                return [
                    'success' => true,
                    'message' => 'Authentification OAuth 2.0 réussie',
                    'data' => $result
                ];
            }

            Log::warning('CashPay: Échec test authentification', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Échec authentification OAuth 2.0',
                'status' => $response->status()
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur test authentification OAuth', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Créer un ledger pour le terminal
     */
    public function createLedger()
    {
        try {
            $headers = $this->getAuthHeaders();

            $data = [
                'terminal' => [
                    'code' => $this->terminalCode
                ]
            ];

            Log::info('CashPay: Création de ledger', [
                'terminal_code' => $this->terminalCode
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . '/ledger/create', $data);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CashPay: Ledger créé avec succès', ['response' => $result]);
                
                return [
                    'success' => true,
                    'ledger_reference' => $result['reference'],
                    'data' => $result
                ];
            }

            Log::error('CashPay: Échec création ledger', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Échec création ledger'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur création ledger', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtenir les passerelles de paiement disponibles
     */
    public function getGateways()
    {
        try {
            $headers = $this->getAuthHeaders();

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->baseUrl . '/gateways');

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'gateways' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des passerelles'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur récupération passerelles', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }

    /**
     * Créer une facture selon la documentation CashPay V2.0
     */
    public function createBill($reservation, $gatewayReference = null, $ledgerReference = null)
    {
        try {
            $headers = $this->getAuthHeaders();

            // Si pas de ledger fourni, en créer un
            if (!$ledgerReference) {
                $ledgerResult = $this->createLedger();
                if ($ledgerResult['success']) {
                    $ledgerReference = $ledgerResult['ledger_reference'];
                } else {
                    Log::warning('CashPay: Impossible de créer un ledger, création facture sans ledger');
                }
            }

            // Données de la facture selon la documentation
            $billData = [
                'amount' => (int) $reservation->prix_total,
                'merchant_reference' => 'RES_' . $reservation->id . '_' . time(),
                'description' => "Réservation Hôtel Le Printemps - Chambre {$reservation->chambre->nom}",
                'currency' => $this->currency,
                'callback_url' => route('payment.webhook'),
                'return_url' => route('payment.return', $reservation->id),
                'client' => [
                    'phone' => $this->formatPhoneNumber($reservation->telephone ?? $reservation->user->telephone ?? '+22800000000'),
                    'lastname' => $reservation->nom ?? $reservation->user->name ?? 'Client',
                    'firstname' => $reservation->prenom ?? $reservation->user->prenom ?? 'Hotel',
                    'email' => $reservation->email ?? $reservation->user->email ?? 'client@hotel.com'
                ]
            ];

            // Ajouter gateway et ledger si fournis
            if ($gatewayReference) {
                $billData['gateway'] = ['reference' => $gatewayReference];
            }
            if ($ledgerReference) {
                $billData['ledger'] = ['reference' => $ledgerReference];
            }

            Log::info('CashPay: Création de facture', [
                'url' => $this->baseUrl . '/tpos/orders',
                'data' => $billData
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . '/tpos/orders', $billData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Mettre à jour la réservation avec les informations CashPay
                $reservation->update([
                    'cashpay_order_reference' => $result['order_reference'],
                    'cashpay_merchant_reference' => $result['merchant_reference'],
                    'cashpay_bill_url' => $result['bill_url'],
                    'cashpay_code' => $result['code'],
                    'cashpay_qrcode_url' => $result['qrcode_url'] ?? null,
                    'cashpay_status' => $result['state'],
                    'cashpay_data' => json_encode($result),
                    'statut_paiement' => 'en_attente',
                    'cashpay_ledger_reference' => $ledgerReference
                ]);

                Log::info('CashPay: Facture créée avec succès', [
                    'order_reference' => $result['order_reference'],
                    'bill_url' => $result['bill_url']
                ]);

                return [
                    'success' => true,
                    'order_reference' => $result['order_reference'],
                    'bill_url' => $result['bill_url'],
                    'qrcode_url' => $result['qrcode_url'] ?? null,
                    'code' => $result['code'],
                    'payments_method' => $result['payments_method'] ?? [],
                    'ledger_reference' => $ledgerReference,
                    'data' => $result
                ];
            }

            Log::error('CashPay: Échec création facture', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la création de la facture',
                'error_details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur création facture', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id ?? 'unknown'
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique lors de la création de la facture'
            ];
        }
    }

    /**
     * Obtenir les détails d'une facture
     */
    public function getBillDetails($orderReference)
    {
        try {
            $headers = $this->getAuthHeaders();

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->baseUrl . '/orders/' . $orderReference);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'bill' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Facture non trouvée'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur récupération facture', [
                'error' => $e->getMessage(),
                'order_reference' => $orderReference
            ]);
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }

    /**
     * Traiter le webhook CashPay selon la documentation
     */
    public function handleWebhook($webhookData)
    {
        try {
            Log::info('CashPay: Webhook reçu', ['data' => $webhookData]);

            // Selon la documentation, les données peuvent être directement en JSON ou en JWT
            if (isset($webhookData['token'])) {
                // Format JWT
                $token = $webhookData['token'];
                $decodedData = $this->decodeJWT($token, $this->apiKey);
                
                if (!$decodedData) {
                    Log::warning('CashPay: Token JWT invalide');
                    return false;
                }
            } else {
                // Format JSON direct
                $decodedData = $webhookData;
            }

            Log::info('CashPay: Données webhook décodées', ['decoded_data' => $decodedData]);

            return [
                'order_reference' => $decodedData['order_reference'] ?? null,
                'merchant_reference' => $decodedData['merchant_reference'] ?? null,
                'amount' => $decodedData['amount'] ?? null,
                'state' => $decodedData['state'] ?? null,
                'received_amount' => $decodedData['received_amount'] ?? null,
                'payments' => $decodedData['payments'] ?? [],
                'client' => $decodedData['client'] ?? [],
                'payment_data' => $decodedData
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur traitement webhook', [
                'error' => $e->getMessage(),
                'data' => $webhookData
            ]);

            return false;
        }
    }

    /**
     * Décoder un token JWT avec la librairie Firebase JWT
     */
    private function decodeJWT($token, $secret)
    {
        try {
            // Si la librairie Firebase JWT est disponible
            if (class_exists('Firebase\JWT\JWT')) {
                $decoded = JWT::decode($token, new Key($secret, 'HS256'));
                return (array) $decoded;
            }

            // Fallback : décodage simple sans validation de signature
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }

            $payload = base64_decode($parts[1]);
            return json_decode($payload, true);

        } catch (Exception $e) {
            Log::error('CashPay: Erreur décodage JWT', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Formater le numéro de téléphone pour CashPay
     */
    private function formatPhoneNumber($phone)
    {
        // Nettoyer le numéro
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Si le numéro commence par 0, le remplacer par +228 (Togo)
        if (substr($phone, 0, 1) === '0') {
            $phone = '+228' . substr($phone, 1);
        }
        
        // Si le numéro ne commence pas par +, ajouter +228
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+228' . $phone;
        }
        
        return $phone;
    }

    /**
     * Valider la configuration CashPay
     */
    public function validateConfiguration()
    {
        $errors = [];

        if (empty($this->username)) {
            $errors[] = 'Username CashPay manquant';
        }

        if (empty($this->password)) {
            $errors[] = 'Password CashPay manquant';
        }

        if (empty($this->clientId)) {
            $errors[] = 'Client ID manquant';
        }

        if (empty($this->clientSecret)) {
            $errors[] = 'Client Secret manquant';
        }

        if (empty($this->apiReference)) {
            $errors[] = 'API Reference manquante';
        }

        if (empty($this->apiKey)) {
            $errors[] = 'API Key manquante';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Obtenir les détails d'un ledger
     */
    public function getLedgerDetails($ledgerReference)
    {
        try {
            $headers = $this->getAuthHeaders();

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->baseUrl . '/ledger/' . $ledgerReference);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'ledger' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Ledger non trouvé'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur récupération ledger', [
                'error' => $e->getMessage(),
                'ledger_reference' => $ledgerReference
            ]);
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }

    /**
     * Lister les ledgers du terminal
     */
    public function listLedgers()
    {
        try {
            $headers = $this->getAuthHeaders();

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->baseUrl . '/ledger/terminal/' . $this->terminalCode);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'ledgers' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur récupération des ledgers'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur liste ledgers', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }

    /**
     * Fermer un ledger
     */
    public function closeLedger($ledgerReference)
    {
        try {
            $headers = $this->generateCashPayAuthHeaders(); // Utiliser l'auth legacy pour cette opération

            $data = ['state' => 1]; // 1 = fermé

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->put($this->baseUrl . '/ledger/' . $ledgerReference . '/update', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Ledger fermé avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur fermeture ledger'
            ];

        } catch (Exception $e) {
            Log::error('CashPay: Erreur fermeture ledger', [
                'error' => $e->getMessage(),
                'ledger_reference' => $ledgerReference
            ]);
            return [
                'success' => false,
                'message' => 'Erreur technique'
            ];
        }
    }
}

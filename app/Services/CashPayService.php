<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class CashPayService
{
    private $apiBaseUrl;
    private $username;
    private $password;
    private $clientId;
    private $clientSecret;
    private $apiKey;
    private $apiReference;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.cashpay.url', 'https://api.semoa-payments.com/api');
        $this->username = config('services.cashpay.username');
        $this->password = config('services.cashpay.password');
        $this->clientId = config('services.cashpay.client_id');
        $this->clientSecret = config('services.cashpay.client_secret');
        $this->apiKey = config('services.cashpay.api_key');
        $this->apiReference = config('services.cashpay.api_reference');
    }

    /**
     * 🔐 Authentification OAuth 2.0 (méthode recommandée)
     */
    private function getOAuthToken(): ?string
    {
        $cacheKey = 'cashpay_oauth_token';
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken) {
            Log::info('✅ Token OAuth trouvé en cache');
            return $cachedToken;
        }

        try {
            Log::info('🔑 Obtention du token OAuth CashPay PRODUCTION');

            $response = Http::timeout(30)
                ->asForm()
                ->post($this->apiBaseUrl . '/oauth/token', [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

            Log::info('📡 Réponse authentification OAuth', [
                'status' => $response->status(),
                'success' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;

                if ($token) {
                    $expiresIn = $data['expires_in'] ?? 3600;
                    Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 300));

                    Log::info('✅ Token OAuth obtenu et mis en cache');
                    return $token;
                }
            }

            $errorMessage = 'Échec authentification OAuth: ' . $response->status();
            if ($response->body()) {
                $errorData = $response->json();
                $errorMessage .= ' - ' . ($errorData['error_description'] ?? $errorData['message'] ?? 'Erreur inconnue');
            }

            Log::error('❌ ' . $errorMessage);
            throw new Exception($errorMessage);

        } catch (Exception $e) {
            Log::error('❌ Erreur authentification OAuth: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔧 Génère les en-têtes avec token OAuth
     */
    private function generateOAuthHeaders(): array
    {
        $token = $this->getOAuthToken();

        if (!$token) {
            throw new Exception('Impossible d\'obtenir le token OAuth');
        }

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'User-Agent' => 'Hotel-Le-Printemps-Production/1.0',
        ];
    }

    /**
     * 🔧 Génère les en-têtes d'authentification standards
     */
    private function generateAuthHeaders(): array
    {
        $salt = random_int(100000, 999999);
        $stringToHash = $this->username . $this->apiKey . $salt;
        $apiSecure = hash('sha256', $stringToHash);

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'login' => $this->username,
            'apireference' => $this->apiReference,
            'salt' => (string) $salt,
            'apisecure' => $apiSecure,
            'Date' => gmdate('D, d M Y H:i:s T'),
            'User-Agent' => 'Hotel-Le-Printemps-Production/1.0',
        ];
    }

    /**
     * 🔧 Génère les en-têtes avec authentification Basic
     */
    private function generateBasicAuthHeaders(): array
    {
        $credentials = base64_encode($this->username . ':' . $this->password);

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $credentials,
            'User-Agent' => 'Hotel-Le-Printemps-Production/1.0',
        ];
    }

    /**
     * 💳 Initialise un paiement RÉEL
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            Log::info('💳 Initialisation paiement CashPay PRODUCTION', [
                'amount' => $paymentData['quantite'],
                'merchant_reference' => $paymentData['merchant_reference'],
                'payment_method' => $paymentData['payment_method'] ?? 'non spécifié'
            ]);

            // Vérifier la configuration avant de continuer
            if (!$this->isConfigured()) {
                throw new Exception('Configuration CashPay incomplète');
            }

            // Tentative 1: OAuth 2.0 (recommandée)
            $result = $this->tryOAuthPayment($paymentData);
            if ($result) {
                Log::info('✅ Paiement initialisé avec succès via OAuth');
                return $result;
            }

            // Tentative 2: Headers d'authentification standards
            $result = $this->tryStandardAuthPayment($paymentData);
            if ($result) {
                Log::info('✅ Paiement initialisé avec succès via Auth standard');
                return $result;
            }

            // Tentative 3: Basic Auth
            $result = $this->tryBasicAuthPayment($paymentData);
            if ($result) {
                Log::info('✅ Paiement initialisé avec succès via Basic Auth');
                return $result;
            }

            Log::error('❌ Toutes les méthodes d\'authentification ont échoué');
            throw new Exception('Toutes les méthodes d\'authentification ont échoué');

        } catch (Exception $e) {
            Log::error('❌ Erreur initialisation paiement CashPay', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 🎯 Tentative avec OAuth 2.0
     */
    private function tryOAuthPayment(array $paymentData): ?array
    {
        try {
            Log::info('🔐 Tentative paiement OAuth');

            $response = Http::withHeaders($this->generateOAuthHeaders())
                ->timeout(30)
                ->post($this->apiBaseUrl . '/bills', $paymentData);

            Log::info('📊 Réponse OAuth', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'body_size' => strlen($response->body())
            ]);

            if ($response->successful()) {
                return $this->processSuccessfulResponse($response->json(), 'OAuth');
            }

            Log::warning('⚠️ OAuth échoué: ' . $response->status());

        } catch (Exception $e) {
            Log::warning('⚠️ Exception OAuth: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * 🎯 Tentative avec authentification standard
     */
    private function tryStandardAuthPayment(array $paymentData): ?array
    {
        try {
            Log::info('🔑 Tentative paiement auth standard');

            $response = Http::withHeaders($this->generateAuthHeaders())
                ->timeout(30)
                ->post($this->apiBaseUrl . '/commandes', $paymentData);

            Log::info('📊 Réponse Auth Standard', [
                'status' => $response->status(),
                'success' => $response->successful()
            ]);

            if ($response->successful()) {
                return $this->processSuccessfulResponse($response->json(), 'Standard Auth');
            }

            Log::warning('⚠️ Auth standard échoué: ' . $response->status());

        } catch (Exception $e) {
            Log::warning('⚠️ Exception Auth standard: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * 🎯 Tentative avec Basic Auth
     */
    private function tryBasicAuthPayment(array $paymentData): ?array
    {
        try {
            Log::info('🔒 Tentative paiement Basic Auth');

            $response = Http::withHeaders($this->generateBasicAuthHeaders())
                ->timeout(30)
                ->post($this->apiBaseUrl . '/bills', $paymentData);

            Log::info('📊 Réponse Basic Auth', [
                'status' => $response->status(),
                'success' => $response->successful()
            ]);

            if ($response->successful()) {
                return $this->processSuccessfulResponse($response->json(), 'Basic Auth');
            }

            Log::warning('⚠️ Basic Auth échoué: ' . $response->status());

        } catch (Exception $e) {
            Log::warning('⚠️ Exception Basic Auth: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * 📋 Traite une réponse API réussie
     */
    private function processSuccessfulResponse(array $responseData, string $method): array
    {
        Log::info("📄 Traitement réponse réussie ({$method})", [
            'response_keys' => array_keys($responseData)
        ]);

        // Rechercher l'URL de paiement dans différents champs possibles
        $billUrl = $responseData['bill_url'] ??
                   $responseData['payment_url'] ??
                   $responseData['redirect_url'] ??
                   $responseData['checkout_url'] ??
                   $responseData['url'] ?? null;

        // Rechercher la référence de commande
        $orderRef = $responseData['order_reference'] ??
                    $responseData['reference'] ??
                    $responseData['transaction_id'] ??
                    $responseData['merchant_reference'] ??
                    $responseData['id'] ?? null;

        if (!$billUrl) {
            Log::error('❌ URL de paiement manquante dans la réponse', [
                'available_fields' => array_keys($responseData)
            ]);
            throw new Exception('URL de paiement manquante dans la réponse API');
        }

        if (!$orderRef) {
            Log::error('❌ Référence de commande manquante dans la réponse', [
                'available_fields' => array_keys($responseData)
            ]);
            throw new Exception('Référence de commande manquante dans la réponse API');
        }

        Log::info('✅ Paiement initialisé avec succès', [
            'order_reference' => $orderRef,
            'bill_url' => $billUrl,
            'method' => $method
        ]);

        return [
            'success' => true,
            'order_reference' => $orderRef,
            'bill_url' => $billUrl,
            'method' => $method,
            'data' => $responseData
        ];
    }

    /**
     * 📝 Prépare les données de paiement pour l'API
     */
    public function preparePaymentData($reservation, string $paymentMethod): array
    {
        $merchantReference = 'HOTEL-' . $reservation->id . '-' . time();

        $data = [
            'quantite' => (int) $reservation->prix_total,
            'merchant_reference' => $merchantReference,
            'description' => "Réservation Hôtel Le Printemps - Chambre {$reservation->chambre->nom} du {$reservation->check_in_date} au {$reservation->check_out_date}",
            'currency' => 'XOF',
            'return_url' => route('payment.return'),
            'callback_url' => route('payment.callback'),
            'payment_method' => $paymentMethod,
        ];

        // Ajouter les informations client
        if ($reservation->client_email) {
            $data['client'] = [
                'lastname' => $reservation->client_nom ?? 'Client',
                'firstname' => $reservation->client_prenom ?? 'Hôtel',
                'email' => $reservation->client_email,
                'phone' => $reservation->client_telephone ?? '',
            ];
        }

        Log::info('📋 Données de paiement préparées', [
            'amount' => $data['quantite'] . ' XOF',
            'reference' => $merchantReference,
            'payment_method' => $paymentMethod,
            'client_email' => $reservation->client_email
        ]);

        return $data;
    }

    /**
     * 🔍 Décode et valide un token JWT de callback
     */
    public function decodeCallbackToken(string $token): array
    {
        try {
            $token = trim($token);

            Log::info('🔍 Décodage token JWT callback', [
                'token_length' => strlen($token)
            ]);

            $decoded = JWT::decode($token, new Key($this->apiKey, 'HS256'));
            $decodedArray = (array) $decoded;

            Log::info('✅ Token JWT décodé avec succès', [
                'decoded_keys' => array_keys($decodedArray)
            ]);

            return [
                'success' => true,
                'data' => $decodedArray
            ];

        } catch (Exception $e) {
            Log::error('❌ Erreur décodage JWT: ' . $e->getMessage(), [
                'token_provided' => !empty($token),
                'api_key_configured' => !empty($this->apiKey)
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 📊 Vérifier le statut d'un paiement
     */
    public function getPaymentStatus(string $orderReference): array
    {
        try {
            Log::info('🔍 Vérification statut paiement: ' . $orderReference);

            // Essayer avec OAuth d'abord
            try {
                $response = Http::withHeaders($this->generateOAuthHeaders())
                    ->timeout(15)
                    ->get($this->apiBaseUrl . "/bills/{$orderReference}");

                if ($response->successful()) {
                    Log::info('✅ Statut paiement récupéré via OAuth');
                    return [
                        'success' => true,
                        'data' => $response->json(),
                        'method' => 'OAuth'
                    ];
                }
            } catch (Exception $e) {
                Log::warning('⚠️ Échec récupération statut OAuth: ' . $e->getMessage());
            }

            // Fallback avec auth standard
            $response = Http::withHeaders($this->generateAuthHeaders())
                ->timeout(15)
                ->get($this->apiBaseUrl . "/commandes/{$orderReference}");

            if ($response->successful()) {
                Log::info('✅ Statut paiement récupéré via Auth standard');
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'method' => 'Standard Auth'
                ];
            }

            Log::warning('⚠️ Impossible de récupérer le statut du paiement');
            return [
                'success' => false,
                'error' => 'Impossible de récupérer le statut du paiement'
            ];

        } catch (Exception $e) {
            Log::error('❌ Erreur récupération statut paiement: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Vérifie si la configuration est complète
     */
    public function isConfigured(): bool
    {
        $required = [
            'apiBaseUrl' => $this->apiBaseUrl,
            'username' => $this->username,
            'apiKey' => $this->apiKey,
            'apiReference' => $this->apiReference,
        ];

        foreach ($required as $key => $value) {
            if (empty($value)) {
                Log::warning("❌ Configuration CashPay incomplète: {$key} manquant");
                return false;
            }
        }

        Log::info('✅ Configuration CashPay complète');
        return true;
    }

    /**
     * 🧪 Teste la connexion à l'API
     */
    public function testConnection(): array
    {
        try {
            Log::info('🧪 Test connexion CashPay PRODUCTION', [
                'url' => $this->apiBaseUrl,
                'username' => $this->username
            ]);

            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'error' => 'Configuration incomplète'
                ];
            }

            // Test OAuth
            $result = $this->testOAuthConnection();
            if ($result['success']) {
                Log::info('✅ Test connexion réussi via OAuth');
                return $result;
            }

            // Test auth standard
            $result = $this->testStandardConnection();
            if ($result['success']) {
                Log::info('✅ Test connexion réussi via Auth standard');
                return $result;
            }

            // Test Basic Auth
            $result = $this->testBasicConnection();
            if ($result['success']) {
                Log::info('✅ Test connexion réussi via Basic Auth');
                return $result;
            }

            return [
                'success' => false,
                'error' => 'Toutes les méthodes de connexion ont échoué'
            ];

        } catch (Exception $e) {
            Log::error('❌ Erreur test connexion CashPay: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test méthodes individuelles
     */
    private function testOAuthConnection(): array
    {
        try {
            $response = Http::withHeaders($this->generateOAuthHeaders())
                ->timeout(15)
                ->get($this->apiBaseUrl . '/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Connexion OAuth réussie',
                    'method' => 'OAuth'
                ];
            }
        } catch (Exception $e) {
            // Continuer
        }

        return ['success' => false];
    }

    private function testStandardConnection(): array
    {
        try {
            $response = Http::withHeaders($this->generateAuthHeaders())
                ->timeout(15)
                ->get($this->apiBaseUrl . '/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Connexion standard réussie',
                    'method' => 'Standard Auth'
                ];
            }
        } catch (Exception $e) {
            // Continuer
        }

        return ['success' => false];
    }

    private function testBasicConnection(): array
    {
        try {
            $response = Http::withHeaders($this->generateBasicAuthHeaders())
                ->timeout(15)
                ->get($this->apiBaseUrl . '/');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Connexion Basic Auth réussie',
                    'method' => 'Basic Auth'
                ];
            }
        } catch (Exception $e) {
            // Fin
        }

        return ['success' => false];
    }

    /**
     * 🗑️ Invalider le cache du token OAuth
     */
    public function clearTokenCache(): void
    {
        Cache::forget('cashpay_oauth_token');
        Log::info('🗑️ Cache token OAuth invalidé');
    }

    /**
     * 📊 Obtenir les statistiques de l'API
     */
    public function getApiStats(): array
    {
        return [
            'api_url' => $this->apiBaseUrl,
            'configured' => $this->isConfigured(),
            'token_cached' => Cache::has('cashpay_oauth_token'),
            'username' => $this->username ? '✅' : '❌',
            'api_key' => $this->apiKey ? '✅' : '❌',
            'api_reference' => $this->apiReference ? '✅' : '❌',
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CashPayService
{
    private $apiBaseUrl;
    private $username;
    private $apiKey;
    private $apiReference;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.cashpay.url');
        $this->username = config('services.cashpay.username');
        $this->apiKey = config('services.cashpay.api_key');
        $this->apiReference = config('services.cashpay.api_reference');
    }

    /**
     * Génère les en-têtes d'authentification pour l'API CashPay
     * Essai avec headers originaux + Date header pour AWS SigV4
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
            'salt' => $salt,
            'apisecure' => $apiSecure,
            'Date' => gmdate('D, d M Y H:i:s T'),
        ];
    }

    /**
     * Initialise un paiement
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            Log::info('Initialisation paiement CashPay', $paymentData);

            $response = Http::withHeaders($this->generateAuthHeaders())
                ->post($this->apiBaseUrl . '/commandes', $paymentData);

            Log::info('Réponse CashPay', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                $errorMessage = 'Erreur HTTP: ' . $response->status();
                if ($response->body()) {
                    $errorData = $response->json();
                    $errorMessage .= ' - ' . ($errorData['message'] ?? $response->body());
                }
                throw new \Exception($errorMessage);
            }

            $responseData = $response->json();

            if (!isset($responseData['bill_url']) || !isset($responseData['order_reference'])) {
                throw new \Exception('Réponse invalide: bill_url ou order_reference manquant');
            }

            return [
                'success' => true,
                'order_reference' => $responseData['order_reference'],
                'bill_url' => $responseData['bill_url'],
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Erreur initialisation paiement CashPay: ' . $e->getMessage(), [
                'payment_data' => $paymentData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Décode et valide un token JWT de callback
     */
    public function decodeCallbackToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->apiKey, 'HS256'));

            Log::info('Token JWT décodé avec succès', [
                'decoded_data' => (array) $decoded
            ]);

            return [
                'success' => true,
                'data' => (array) $decoded
            ];

        } catch (\Exception $e) {
            Log::error('Erreur décodage JWT: ' . $e->getMessage(), [
                'token' => $token,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prépare les données de paiement pour l'API
     */
    public function preparePaymentData($reservation, string $paymentMethod): array
    {
        return [
            'quantite' => (int) $reservation->prix_total,
            'merchant_reference' => 'RESA-' . $reservation->id . '-' . time(),
            'description' => 'Paiement réservation #' . $reservation->id . ' - ' . $reservation->chambre->nom,
            'client' => [
                'lastname' => $reservation->client_nom,
                'firstname' => $reservation->client_prenom,
                'email' => $reservation->client_email,
                'phone' => $reservation->client_telephone,
            ],
            'return_url' => route('payment.return'),
            'callback_url' => route('payment.callback'),
            'payment_method' => $paymentMethod,
        ];
    }

    /**
     * Vérifie si la configuration est complète
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiBaseUrl)
            && !empty($this->username)
            && !empty($this->apiKey)
            && !empty($this->apiReference);
    }

    /**
     * Teste la connexion à l'API
     */
    public function testConnection(): array
    {
        try {
            $headers = $this->generateAuthHeaders();

            Log::info('Test de connexion CashPay', [
                'url' => $this->apiBaseUrl,
                'username' => $this->username,
                'api_reference' => $this->apiReference,
                'headers_sent' => $headers
            ]);

            // Test avec une requête GET simple sur la racine ou un endpoint de healthcheck
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->get($this->apiBaseUrl . '/');

            Log::info('Réponse test connexion', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Connexion réussie',
                    'data' => $response->json()
                ];
            } else {
                // Si l'endpoint racine ne fonctionne pas, essayons avec /commandes
                $response2 = Http::withHeaders($headers)
                    ->timeout(30)
                    ->get($this->apiBaseUrl . '/commandes');

                Log::info('Réponse test connexion (second essai)', [
                    'status' => $response2->status(),
                    'body' => $response2->body(),
                    'headers' => $response2->headers()
                ]);

                if ($response2->successful()) {
                    return [
                        'success' => true,
                        'status' => $response2->status(),
                        'message' => 'Connexion réussie (endpoint /commandes)',
                        'data' => $response2->json()
                    ];
                } else {
                    $errorMessage = 'Erreur HTTP: ' . $response->status();
                    if ($response->body()) {
                        $errorData = $response->json();
                        $errorMessage .= ' - ' . ($errorData['message'] ?? $response->body());
                    }
                    return [
                        'success' => false,
                        'status' => $response->status(),
                        'error' => $errorMessage
                    ];
                }
            }

        } catch (\Exception $e) {
            Log::error('Erreur test connexion CashPay: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

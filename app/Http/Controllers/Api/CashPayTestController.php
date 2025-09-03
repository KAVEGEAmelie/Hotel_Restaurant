<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CashPayService;
use App\Models\Reservation;
use App\Models\Chambre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CashPayTestController extends Controller
{
    protected $cashPayService;

    public function __construct(CashPayService $cashPayService)
    {
        $this->cashPayService = $cashPayService;
    }

    /**
     * Test complet de l'intégration CashPay - ENDPOINT POUR POSTMAN
     * 
     * @group CashPay Test API
     * 
     * Test complet de toutes les fonctionnalités CashPay
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "Test CashPay complet réussi",
     *   "tests": {
     *     "authentication": {"success": true, "message": "..."},
     *     "gateways": {"success": true, "data": []},
     *     "ledger_creation": {"success": true, "ledger_reference": "..."},
     *     "bill_creation": {"success": true, "bill_url": "..."}
     *   }
     * }
     */
    public function testComplete()
    {
        $results = [
            'authentication' => null,
            'configuration' => null,
            'gateways' => null,
            'ledger_creation' => null,
            'bill_creation' => null,
            'ledger_details' => null
        ];

        try {
            // 1. Test de configuration
            Log::info('CashPay Test: Vérification configuration');
            $configTest = $this->cashPayService->validateConfiguration();
            $results['configuration'] = $configTest;

            if (!$configTest['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration CashPay invalide',
                    'errors' => $configTest['errors'],
                    'results' => $results
                ], 400);
            }

            // 2. Test d'authentification OAuth 2.0
            Log::info('CashPay Test: Test authentification OAuth 2.0');
            $authResult = $this->cashPayService->testAuthentication();
            $results['authentication'] = $authResult;

            if (!$authResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec authentification OAuth 2.0',
                    'results' => $results
                ], 400);
            }

            // 3. Test récupération des passerelles
            Log::info('CashPay Test: Récupération passerelles');
            $gatewaysResult = $this->cashPayService->getGateways();
            $results['gateways'] = $gatewaysResult;

            // 4. Test création de ledger
            Log::info('CashPay Test: Création ledger');
            $ledgerResult = $this->cashPayService->createLedger();
            $results['ledger_creation'] = $ledgerResult;

            $ledgerReference = null;
            if ($ledgerResult['success']) {
                $ledgerReference = $ledgerResult['ledger_reference'];
                
                // 5. Test récupération détails ledger
                Log::info('CashPay Test: Récupération détails ledger');
                $ledgerDetailsResult = $this->cashPayService->getLedgerDetails($ledgerReference);
                $results['ledger_details'] = $ledgerDetailsResult;
            }

            // 6. Test création de facture avec une réservation factice
            Log::info('CashPay Test: Création facture test');
            $billResult = $this->createTestBillWithLedger($ledgerReference);
            $results['bill_creation'] = $billResult;

            $overallSuccess = $authResult['success'] && 
                            $gatewaysResult['success'] && 
                            $ledgerResult['success'] && 
                            $billResult['success'];

            return response()->json([
                'success' => $overallSuccess,
                'message' => $overallSuccess ? 'Test CashPay complet réussi' : 'Certains tests ont échoué',
                'timestamp' => now()->toISOString(),
                'environment' => config('services.cashpay.base_url'),
                'results' => $results
            ]);

        } catch (Exception $e) {
            Log::error('CashPay Test: Erreur test complet', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur durant le test : ' . $e->getMessage(),
                'results' => $results
            ], 500);
        }
    }

    /**
     * Test d'authentification uniquement
     * 
     * @group CashPay Test API
     */
    public function testAuth()
    {
        try {
            $result = $this->cashPayService->testAuthentication();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur test authentification : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les passerelles de paiement
     * 
     * @group CashPay Test API
     */
    public function getGateways()
    {
        try {
            $result = $this->cashPayService->getGateways();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Passerelles récupérées',
                'gateways' => $result['gateways'] ?? [],
                'count' => count($result['gateways'] ?? []),
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur récupération passerelles : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un ledger de test
     * 
     * @group CashPay Test API
     */
    public function createLedger()
    {
        try {
            $result = $this->cashPayService->createLedger();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Ledger créé',
                'ledger_reference' => $result['ledger_reference'] ?? null,
                'data' => $result['data'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur création ledger : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les ledgers du terminal
     * 
     * @group CashPay Test API
     */
    public function listLedgers()
    {
        try {
            $result = $this->cashPayService->listLedgers();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Ledgers récupérés',
                'ledgers' => $result['ledgers'] ?? [],
                'count' => count($result['ledgers'] ?? []),
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur liste ledgers : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une facture de test
     * 
     * @group CashPay Test API
     * 
     * @bodyParam amount int required Montant en francs CFA. Example: 1000
     * @bodyParam phone string Numéro de téléphone du client. Example: +22890112783
     * @bodyParam email string Email du client. Example: test@example.com
     * @bodyParam description string Description de la facture. Example: Test de paiement
     */
    public function createTestBill(Request $request)
    {
        try {
            $amount = $request->input('amount', 1000);
            $phone = $request->input('phone', '+22890112783');
            $email = $request->input('email', 'test@leprintemps.tg');
            $description = $request->input('description', 'Test de paiement CashPay');

            // Créer une réservation factice pour le test
            $fakeReservation = $this->createFakeReservation($amount, $phone, $email, $description);
            
            $result = $this->cashPayService->createBill($fakeReservation);
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Facture créée',
                'order_reference' => $result['order_reference'] ?? null,
                'bill_url' => $result['bill_url'] ?? null,
                'qrcode_url' => $result['qrcode_url'] ?? null,
                'code' => $result['code'] ?? null,
                'data' => $result['data'] ?? null,
                'test_reservation_id' => $fakeReservation->id,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur création facture test : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simuler un webhook de test
     * 
     * @group CashPay Test API
     * 
     * @bodyParam order_reference string required Référence de la commande
     * @bodyParam state string required État du paiement (Paid, Pending, Error, etc.)
     */
    public function simulateWebhook(Request $request)
    {
        try {
            $webhookData = [
                'order_reference' => $request->input('order_reference'),
                'merchant_reference' => $request->input('merchant_reference'),
                'amount' => $request->input('amount', 1000),
                'state' => $request->input('state', 'Paid'),
                'received_amount' => $request->input('received_amount'),
                'client' => [
                    'phone' => $request->input('client_phone', '+22890112783'),
                    'email' => $request->input('client_email', 'test@example.com')
                ],
                'payments' => $request->input('payments', [])
            ];

            $result = $this->cashPayService->handleWebhook($webhookData);
            
            return response()->json([
                'success' => $result !== false,
                'message' => $result ? 'Webhook traité avec succès' : 'Erreur traitement webhook',
                'webhook_data' => $webhookData,
                'processed_data' => $result,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur simulation webhook : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une facture de test avec ledger spécifique
     */
    private function createTestBillWithLedger($ledgerReference = null)
    {
        try {
            // Créer une réservation factice
            $fakeReservation = $this->createFakeReservation(2500, '+22890112783', 'test@leprintemps.tg', 'Test facture CashPay API');
            
            return $this->cashPayService->createBill($fakeReservation, null, $ledgerReference);

        } catch (Exception $e) {
            Log::error('Erreur création facture test', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur création facture test : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Créer une réservation factice pour les tests
     */
    private function createFakeReservation($amount, $phone, $email, $description)
    {
        // Créer un utilisateur de test s'il n'existe pas
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Client Test',
                'password' => bcrypt('password'),
                'role' => 'client'
            ]
        );

        // Récupérer une chambre pour le test
        $chambre = Chambre::first();
        if (!$chambre) {
            throw new Exception('Aucune chambre disponible pour le test');
        }

        // Créer une réservation de test
        $reservation = new Reservation();
        $reservation->user_id = $user->id;
        $reservation->chambre_id = $chambre->id;
        $reservation->nom = 'Test';
        $reservation->prenom = 'Client';
        $reservation->email = $email;
        $reservation->telephone = $phone;
        $reservation->date_arrivee = now()->addDays(1);
        $reservation->date_depart = now()->addDays(3);
        $reservation->nombre_personnes = 1;
        $reservation->prix_total = $amount;
        $reservation->statut = 'en_attente';
        $reservation->statut_paiement = 'en_attente';
        $reservation->save();

        // Établir la relation avec la chambre
        $reservation->load('chambre', 'user');

        return $reservation;
    }
}

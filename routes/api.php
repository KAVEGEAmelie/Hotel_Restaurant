<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CashPayTestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| CashPay Test API Routes - Pour tests Postman
|--------------------------------------------------------------------------
*/

Route::prefix('cashpay-test')->name('api.cashpay.')->group(function () {
    
    // Test complet de l'intégration CashPay
    Route::get('/complete', [CashPayTestController::class, 'testComplete'])
        ->name('test.complete');
    
    // Test d'authentification uniquement
    Route::get('/auth', [CashPayTestController::class, 'testAuth'])
        ->name('test.auth');
    
    // Récupérer les passerelles de paiement
    Route::get('/gateways', [CashPayTestController::class, 'getGateways'])
        ->name('gateways');
    
    // Créer un ledger de test
    Route::post('/ledger/create', [CashPayTestController::class, 'createLedger'])
        ->name('ledger.create');
    
    // Lister les ledgers
    Route::get('/ledgers', [CashPayTestController::class, 'listLedgers'])
        ->name('ledgers');
    
    // Créer une facture de test
    Route::post('/bill/create', [CashPayTestController::class, 'createTestBill'])
        ->name('bill.create');
    
    // Simuler un webhook
    Route::post('/webhook/simulate', [CashPayTestController::class, 'simulateWebhook'])
        ->name('webhook.simulate');
});

/*
|--------------------------------------------------------------------------
| Routes publiques pour webhook CashPay
|--------------------------------------------------------------------------
*/

// Webhook CashPay - doit être accessible sans authentification
Route::post('/cashpay/webhook', [App\Http\Controllers\PaymentController::class, 'handleWebhook'])
    ->name('api.cashpay.webhook');

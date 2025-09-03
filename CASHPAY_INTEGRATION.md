# CashPay Integration - Documentation

## 📋 Vue d'ensemble

Cette intégration CashPay implémente les deux méthodes d'authentification selon la documentation officielle :
- **CashPay Auth** : Headers `login`, `apireference`, `salt`, `apisecure`
- **OAuth 2.0** : Authentification par token Bearer

## 🔧 Configuration

### 1. Variables d'environnement (.env)

```env
# CashPay Configuration
CASHPAY_BASE_URL=https://sandbox.semoa-payments.com/api
CASHPAY_USERNAME=api_cashpay.leprintemps
CASHPAY_CLIENT_ID=api_cashpay.leprintemps
CASHPAY_CLIENT_SECRET=NZLJv4V4A9R5zAN6oeQxFq985F9E2RxC
CASHPAY_API_REFERENCE=104
CASHPAY_CURRENCY=XOF
CASHPAY_TEST_MODE=false
```

### 2. Configuration dans `config/services.php`

```php
'cashpay' => [
    'base_url' => env('CASHPAY_BASE_URL', 'https://sandbox.semoa-payments.com/api'),
    'username' => env('CASHPAY_USERNAME'),
    'client_id' => env('CASHPAY_CLIENT_ID'),
    'client_secret' => env('CASHPAY_CLIENT_SECRET'),
    'api_reference' => env('CASHPAY_API_REFERENCE'),
    'currency' => env('CASHPAY_CURRENCY', 'XOF'),
    'notification_url' => env('CASHPAY_NOTIFICATION_URL', env('APP_URL') . '/cashpay/webhook'),
    'return_url' => env('CASHPAY_RETURN_URL', env('APP_URL') . '/payment/success'),
    'cancel_url' => env('CASHPAY_CANCEL_URL', env('APP_URL') . '/payment/failed'),
    'test_mode' => env('CASHPAY_TEST_MODE', false),
],
```

## 🚀 Utilisation du Service

### 1. Test d'authentification

```php
$cashPayService = app(\App\Services\CashPayService::class);

// Test CashPay Auth
$authResult = $cashPayService->testAuthentication();
if ($authResult['success']) {
    echo "CashPay Auth fonctionne !";
}

// Test OAuth 2.0
$oauthResult = $cashPayService->getOAuthToken();
if ($oauthResult['success']) {
    echo "OAuth 2.0 fonctionne !";
}
```

### 2. Création de facture

```php
$customerInfo = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+22512345678'
];

$invoiceResult = $cashPayService->createInvoice(
    amount: 5000,
    description: 'Réservation Hôtel',
    customerInfo: $customerInfo
);

if ($invoiceResult['success']) {
    $invoiceId = $invoiceResult['invoice_id'];
    $paymentLink = $invoiceResult['payment_link'];
    // Rediriger vers $paymentLink
}
```

### 3. Vérification de statut

```php
$statusResult = $cashPayService->checkInvoiceStatus($invoiceId);
if ($statusResult['success']) {
    $status = $statusResult['status']; // 'pending', 'paid', 'failed', etc.
}
```

### 4. Traitement de webhook

```php
// Dans le PaymentController
public function handleWebhook(Request $request)
{
    $webhookData = $request->all();
    $webhookResult = $this->cashPayService->handleWebhook($webhookData);
    
    if ($webhookResult['success']) {
        // Mettre à jour la réservation
        $reservation = Reservation::where('cashpay_invoice_id', $webhookResult['invoice_id'])->first();
        if ($reservation) {
            $reservation->update([
                'cashpay_status' => $webhookResult['status'],
                'statut_paiement' => $webhookResult['status'] === 'paid' ? 'paye' : 'echec'
            ]);
        }
    }
    
    return response()->json(['status' => 'ok']);
}
```

## 🔍 Authentification CashPay Auth

Le service génère automatiquement les headers requis :

```php
private function generateCashPayAuthHeaders(): array
{
    $salt = time(); // Identifiant unique
    $apisecure = hash('sha256', $login . $apiKey . $salt);
    
    return [
        'login' => $this->login,
        'apireference' => $this->apiReference,
        'salt' => $salt,
        'apisecure' => $apisecure
    ];
}
```

## 🔑 Authentification OAuth 2.0

```php
// Le service gère automatiquement :
// 1. Obtention du token
// 2. Cache du token (expire dans 50 minutes)
// 3. Renouvellement automatique

$tokenResult = $cashPayService->getOAuthToken();
```

## 📊 Structure de la base de données

Migration ajoutée pour la table `reservations` :

```php
Schema::table('reservations', function (Blueprint $table) {
    $table->string('cashpay_invoice_id')->nullable();
    $table->text('cashpay_payment_link')->nullable();
    $table->string('cashpay_status')->nullable();
    $table->json('cashpay_response_data')->nullable();
    $table->json('cashpay_webhook_data')->nullable();
    $table->timestamp('cashpay_created_at')->nullable();
    $table->timestamp('cashpay_updated_at')->nullable();
    
    $table->index('cashpay_invoice_id');
    $table->index('cashpay_status');
});
```

## 🛠 Débogage

### 1. Test complet

```bash
php test_cashpay_complete.php
```

### 2. Vérification des logs

```bash
tail -f storage/logs/laravel.log | grep CashPay
```

### 3. Erreurs courantes

| Erreur | Cause | Solution |
|--------|-------|----------|
| `503 Service Temporarily Unavailable` | Serveur CashPay indisponible | Attendre ou contacter CashPay |
| `403 Forbidden` | Credentials incorrects | Vérifier username, client_secret, api_reference |
| `401 Unauthorized` | Token expiré | Le service renouvelle automatiquement |

## 🔄 Flow de paiement complet

1. **Initiation** : `PaymentController@initiatePayment`
   - Création de facture CashPay
   - Sauvegarde des infos en base
   - Redirection vers le lien de paiement

2. **Paiement** : L'utilisateur paye via l'interface CashPay

3. **Callback** : CashPay envoie un webhook à `/cashpay/webhook`
   - Mise à jour du statut en base
   - Notification par email

4. **Vérification** : Polling automatique via AJAX
   - Vérification périodique du statut
   - Redirection automatique en cas de succès

## 📧 Support

En cas de problème :

1. **Vérifier la configuration** avec `php test_cashpay_complete.php`
2. **Consulter les logs** Laravel pour les détails des erreurs
3. **Contacter CashPay** pour valider :
   - Les credentials du compte sandbox
   - L'activation du compte
   - L'autorisation de l'IP
   - La configuration de l'API reference

## 🎯 Points de contrôle

- [ ] Endpoint correct : `https://sandbox.semoa-payments.com/api`
- [ ] Credentials CashPay valides
- [ ] Compte sandbox activé
- [ ] IP autorisée chez CashPay
- [ ] Migration de base de données exécutée
- [ ] Routes de webhook configurées
- [ ] Variables d'environnement définies
- [ ] Tests d'authentification passants

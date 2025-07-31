# Intégration API CashPay - Hôtel Le Printemps

## 🎯 Vue d'ensemble

Cette intégration permet de traiter les paiements de réservations via l'API CashPay, supportant les cartes bancaires et le Mobile Money.

## 📋 Fonctionnalités

- ✅ Initialisation de paiements via CashPay
- ✅ Support carte bancaire et Mobile Money
- ✅ Gestion des callbacks JWT
- ✅ Validation des tokens de sécurité
- ✅ Logs détaillés pour le debugging
- ✅ Tests d'intégration complets
- ✅ Middleware de vérification de configuration
- ✅ Commande Artisan pour tester la connexion

## 🚀 Installation et Configuration

### 1. Variables d'environnement

Ajoutez ces variables dans votre fichier `.env` :

```env
# Configuration CashPay API
CASHPAY_API_URL=https://api.cashpay.ci
CASHPAY_USERNAME=votre_username
CASHPAY_PASSWORD=votre_password
CASHPAY_CLIENT_ID=votre_client_id
CASHPAY_CLIENT_SECRET=votre_client_secret
CASHPAY_API_KEY=votre_api_key
CASHPAY_API_REFERENCE=votre_api_reference
```

### 2. Obtention des credentials

Contactez CashPay pour obtenir :
- Compte développeur
- Credentials d'authentification
- Clés API pour la signature
- Référence API pour identifier votre compte

### 3. Test de la configuration

```bash
# Tester la connexion
php artisan cashpay:test

# Lancer les tests
php artisan test --filter=CashPayIntegrationTest
```

## 🔧 Architecture

### Services

- **`CashPayService`** : Service principal pour l'intégration API
- **`PaymentController`** : Contrôleur pour gérer les paiements
- **`CheckCashPayConfig`** : Middleware de vérification

### Routes

```php
// Pages de paiement (avec vérification de config)
Route::middleware(['cashpay.config'])->group(function () {
    Route::get('/paiement/{reservation}', [PaymentController::class, 'show']);
    Route::post('/paiement/{reservation}', [PaymentController::class, 'process']);
    Route::get('/payment/test-connection', [PaymentController::class, 'testConnection']);
});

// Callbacks (sans vérification de config)
Route::get('/payment/callback', [PaymentController::class, 'callback']);
Route::get('/payment/return', [PaymentController::class, 'return']);
Route::post('/payment/callback', [PaymentController::class, 'callback']);
```

## 💳 Processus de paiement

### 1. Affichage de la page de paiement

```php
// PaymentController@show
public function show(Reservation $reservation)
{
    // Vérification de la configuration
    if (!$this->cashPayService->isConfigured()) {
        return redirect()->route('home')->with('error', 'Service de paiement non configuré.');
    }

    // Validation du statut de réservation
    if ($reservation->statut !== 'pending') {
        return redirect()->route('home')->with('error', 'Cette réservation n\'est plus valide.');
    }

    return view('payment.show', compact('reservation'));
}
```

### 2. Initialisation du paiement

```php
// PaymentController@process
public function process(Request $request, Reservation $reservation)
{
    // Validation des données
    $request->validate([
        'payment_method' => 'required|in:card,mobile_money',
    ]);

    // Préparation des données
    $paymentData = $this->cashPayService->preparePaymentData($reservation, $request->payment_method);

    // Initialisation via l'API
    $result = $this->cashPayService->initializePayment($paymentData);

    if (!$result['success']) {
        throw new \Exception($result['error']);
    }

    // Sauvegarde de la référence
    $reservation->transaction_ref = $result['order_reference'];
    $reservation->save();

    // Redirection vers CashPay
    return redirect()->away($result['bill_url']);
}
```

### 3. Gestion des callbacks

```php
// PaymentController@callback
public function callback(Request $request)
{
    $token = $request->input('data');
    
    // Décodage du token JWT
    $result = $this->cashPayService->decodeCallbackToken($token);
    
    if (!$result['success']) {
        return response('Erreur de traitement du token', 400);
    }

    $decodedData = $result['data'];
    $orderReference = $decodedData['order_reference'];
    $status = $decodedData['status'];

    // Mise à jour du statut
    $reservation = Reservation::where('transaction_ref', $orderReference)->first();
    
    if ($status === 'SUCCESS') {
        $reservation->update(['statut' => 'confirmée']);
        // TODO: Envoyer email de confirmation
    } elseif ($status === 'FAILED' || $status === 'CANCELLED') {
        $reservation->update(['statut' => 'échoué']);
    }

    return response('OK', 200);
}
```

## 🔐 Sécurité

### Authentification API

```php
private function generateAuthHeaders(): array
{
    $salt = random_int(100000, 999999);
    $stringToHash = $this->username . $this->apiKey . $salt;
    $apiSecure = hash('sha256', $stringToHash);

    return [
        'login' => $this->username,
        'apireference' => $this->apiReference,
        'salt' => $salt,
        'apisecure' => $apiSecure,
    ];
}
```

### Validation JWT

```php
public function decodeCallbackToken(string $token): array
{
    try {
        $decoded = JWT::decode($token, new Key($this->apiKey, 'HS256'));
        return [
            'success' => true,
            'data' => (array) $decoded
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

## 📊 Logs et Monitoring

### Logs automatiques

- Tentatives de paiement
- Réponses de l'API
- Callbacks reçus
- Erreurs de décodage JWT

### Exemple de log

```php
Log::info('Paiement initialisé avec succès', [
    'reservation_id' => $reservation->id,
    'order_reference' => $result['order_reference'],
    'payment_method' => $request->payment_method
]);
```

## 🧪 Tests

### Tests d'intégration

```bash
# Lancer tous les tests CashPay
php artisan test --filter=CashPayIntegrationTest

# Test spécifique
php artisan test --filter=it_can_show_payment_page_for_valid_reservation
```

### Tests disponibles

- ✅ Affichage de la page de paiement
- ✅ Validation du statut de réservation
- ✅ Validation de la méthode de paiement
- ✅ Gestion des erreurs API
- ✅ Traitement des callbacks JWT
- ✅ Gestion des tokens invalides
- ✅ Gestion des callbacks sans données
- ✅ Gestion de l'URL de retour

## 🐛 Debugging

### Commande de test

```bash
php artisan cashpay:test
```

### Vérification des logs

```bash
tail -f storage/logs/laravel.log | grep -i cashpay
```

### Test de connexion via route

```
GET /payment/test-connection
```

## 📝 Structure des données

### Requête de paiement

```json
{
  "quantite": 50000,
  "merchant_reference": "RESA-123-1234567890",
  "description": "Paiement réservation #123 - Chambre Deluxe",
  "client": {
    "lastname": "Doe",
    "firstname": "John",
    "email": "john@example.com",
    "phone": "+22501234567"
  },
  "return_url": "https://votre-site.com/payment/return",
  "callback_url": "https://votre-site.com/payment/callback",
  "payment_method": "card"
}
```

### Réponse API

```json
{
  "order_reference": "CP-123456789",
  "bill_url": "https://cashpay.ci/pay/CP-123456789"
}
```

### Callback JWT

```json
{
  "order_reference": "CP-123456789",
  "status": "SUCCESS",
  "amount": 50000,
  "transaction_id": "TXN-123456"
}
```

## 🔄 Workflow complet

1. **Réservation créée** → Statut "pending"
2. **Utilisateur accède à la page de paiement**
3. **Choix de la méthode de paiement** (carte/mobile money)
4. **Soumission du formulaire** → Appel à l'API CashPay
5. **Redirection vers CashPay** → Page de paiement sécurisée
6. **Paiement effectué** → Callback JWT reçu
7. **Traitement du callback** → Mise à jour du statut
8. **Email de confirmation** → Envoi au client

## 🚨 Gestion d'erreurs

### Erreurs courantes

- **401 Unauthorized** : Vérifiez vos credentials
- **400 Bad Request** : Vérifiez le format des données
- **500 Internal Server Error** : Contactez CashPay

### Logs d'erreur

```php
Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage(), [
    'reservation_id' => $reservation->id,
    'trace' => $e->getTraceAsString()
]);
```

## 📞 Support

En cas de problème :

1. Vérifiez les logs Laravel
2. Testez avec `php artisan cashpay:test`
3. Contactez le support CashPay
4. Consultez la documentation officielle

## 🔄 Mise à jour

Pour mettre à jour l'intégration :

1. Vérifiez la compatibilité avec la nouvelle version de l'API
2. Mettez à jour les tests
3. Testez en environnement de développement
4. Déployez en production

---

**Note** : Cette intégration suit les meilleures pratiques de sécurité et de logging pour garantir un traitement fiable des paiements. 
# 🏨 Intégration CashPay V2.0 - Hôtel Restaurant Le Printemps

## 📋 Vue d'ensemble

Cette intégration implémente l'API CashPay V2.0 avec authentification OAuth 2.0 pour traiter les paiements des réservations d'hôtel.

### 🔑 Credentials de Production

```env
CASHPAY_BASE_URL=https://sandbox.semoa-payments.com/api
CASHPAY_USERNAME=api_cashpay.leprintemps
CASHPAY_PASSWORD=nwDT7XCQzU
CASHPAY_CLIENT_ID=api_cashpay.leprintemps
CASHPAY_CLIENT_SECRET=NZLJv4V4A9R5zAN6oeQxFq985F9E2RxC
CASHPAY_API_REFERENCE=104
CASHPAY_API_KEY=2xiUFAWYSriHvSFYcv9YRl816uzgbC2Vth5g
CASHPAY_CURRENCY=XOF
CASHPAY_TERMINAL_CODE=TERMINAL_LEPRINTEMPS
```

## 🚀 Configuration et Tests

### 1. Interface d'Administration

Accédez à l'interface de test CashPay dans l'administration :
```
http://localhost:8000/admin/cashpay
```

Cette interface permet de :
- ✅ Tester l'authentification OAuth 2.0
- 🔍 Vérifier la configuration
- 🌐 Récupérer les passerelles de paiement
- 📊 Créer et gérer les ledgers
- 📋 Voir les résultats des tests en temps réel

### 2. Tests avec Postman

Importez la collection Postman fournie : `CashPay_Integration_Tests.postman_collection.json`

#### Configuration des variables :
- `base_url` : `http://localhost:8000`

#### Tests disponibles :
1. **Test Complet** : `/api/cashpay-test/complete`
2. **Authentification** : `/api/cashpay-test/auth`
3. **Passerelles** : `/api/cashpay-test/gateways`
4. **Ledgers** : `/api/cashpay-test/ledger/create`
5. **Factures** : `/api/cashpay-test/bill/create`
6. **Webhooks** : `/api/cashpay-test/webhook/simulate`

## 🔄 Flux de Paiement

### 1. Création d'une Réservation
```php
// Le client fait une réservation
$reservation = Reservation::create([
    'user_id' => $user->id,
    'chambre_id' => $chambre->id,
    'prix_total' => 15000,
    'statut' => 'en_attente',
    'statut_paiement' => 'en_attente'
]);
```

### 2. Initiation du Paiement
```php
// Redirection vers CashPay
$paymentController = new PaymentController();
$result = $paymentController->initiatePayment($request, $reservation->id);
// L'utilisateur est redirigé vers l'interface de paiement CashPay
```

### 3. Traitement du Webhook
```php
// CashPay notifie automatiquement via webhook
POST /payment/webhook
{
    "token": "jwt_token_from_cashpay"
}
```

### 4. Mise à jour de la Réservation
```php
// La réservation est automatiquement mise à jour selon l'état du paiement
$reservation->update([
    'statut' => 'confirmee',
    'statut_paiement' => 'paye',
    'date_paiement' => now(),
    'methode_paiement' => 'CashPay'
]);
```

## 🏗️ Architecture Technique

### Services
- **`CashPayService`** : Service principal pour l'API CashPay
  - Authentification OAuth 2.0
  - Gestion des ledgers
  - Création de factures
  - Traitement des webhooks

### Contrôleurs
- **`PaymentController`** : Gestion des paiements côté utilisateur
- **`CashPayTestController`** : Endpoints de test pour Postman

### Modèles
- **`Reservation`** : Enrichi avec les champs CashPay
  ```php
  // Nouveaux champs
  cashpay_order_reference
  cashpay_merchant_reference
  cashpay_bill_url
  cashpay_code
  cashpay_status
  cashpay_ledger_reference
  cashpay_webhook_data
  montant_paye
  methode_paiement
  date_paiement
  ```

## 📊 Gestion des Ledgers

### Création Automatique
```php
$service = new CashPayService();
$result = $service->createLedger();
// Retourne : ledger_reference
```

### Utilisation avec Factures
```php
$billResult = $service->createBill($reservation, null, $ledgerReference);
```

### Fermeture des Ledgers
```php
$result = $service->closeLedger($ledgerReference);
```

## 🔔 Webhooks CashPay

### Configuration
L'URL de webhook est automatiquement configurée lors de la création de facture :
```php
'callback_url' => route('payment.webhook')
```

### Traitement
```php
public function handleWebhook(Request $request)
{
    $webhookData = $request->all();
    $result = $this->cashPayService->handleWebhook($webhookData);
    
    // Mise à jour automatique de la réservation
    $this->updateReservationFromWebhook($reservation, $result);
}
```

### États Supportés
- `Paid` : Paiement réussi ✅
- `Pending` : En attente ⏳
- `Error` : Erreur ❌
- `Cancelled` : Annulé 🚫
- `Partial` : Paiement partiel ⚠️

## 🧪 Tests et Validation

### Test Rapide via Admin
```
GET /admin/cashpay
```

### Test API Complet
```bash
curl -X GET "http://localhost:8000/api/cashpay-test/complete" \
  -H "Accept: application/json"
```

### Créer une Facture Test
```bash
curl -X POST "http://localhost:8000/api/cashpay-test/bill/create" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1500,
    "phone": "+22890112783",
    "email": "test@leprintemps.tg",
    "description": "Test facture"
  }'
```

## 🔍 Debugging et Logs

### Logs CashPay
Tous les appels API sont loggés dans `storage/logs/laravel.log` :
```
[2025-09-02 10:30:00] local.INFO: CashPay: Test d'authentification OAuth 2.0
[2025-09-02 10:30:01] local.INFO: CashPay: Token OAuth obtenu avec succès
[2025-09-02 10:30:02] local.INFO: CashPay: Facture créée avec succès {"order_reference":"SANDBOX-..."}
```

### Vérification de Configuration
```php
$service = new CashPayService();
$validation = $service->validateConfiguration();
if (!$validation['valid']) {
    dd($validation['errors']);
}
```

## 🚨 Gestion d'Erreurs

### Authentification
- Vérifier les credentials OAuth 2.0
- S'assurer que l'environnement sandbox est accessible

### Création de Factures
- Vérifier le format du numéro de téléphone (+228...)
- S'assurer que le montant est > 0
- Valider la structure des données client

### Webhooks
- Vérifier la signature JWT si disponible
- Logger tous les webhooks reçus
- Gérer les duplicatas d'événements

## 📚 Documentation API CashPay

Lien officiel : https://documenter.getpostman.com/view/4377470/UUxukWRR

### Endpoints Principaux
- **OAuth Token** : `POST /oauth/token`
- **Créer Facture** : `POST /tpos/orders`
- **Passerelles** : `GET /gateways`
- **Ledgers** : `POST /ledger/create`

## 🤝 Support

Pour toute question ou problème :
- **Contact CashPay** : Julien AFAWOUBO - julien@semoa-group.com - +228 93251116
- **Logs** : Consulter `storage/logs/laravel.log`
- **Tests** : Utiliser l'interface admin ou Postman

---

✅ **Intégration CashPay V2.0 Complète et Fonctionnelle** ✅

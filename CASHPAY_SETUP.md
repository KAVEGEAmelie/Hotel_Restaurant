# Configuration de l'API CashPay

## Variables d'environnement requises

Ajoutez les variables suivantes dans votre fichier `.env` :

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

## Obtention des credentials

1. Contactez CashPay pour obtenir un compte développeur
2. Ils vous fourniront :
   - Username et Password pour l'authentification
   - Client ID et Client Secret pour l'API
   - API Key pour la signature des requêtes
   - API Reference pour identifier votre compte

## Test de l'intégration

### 1. Test en mode développement

L'API CashPay propose un environnement de test. Utilisez :
- URL de test : `https://test-api.cashpay.ci`
- Credentials de test fournis par CashPay

### 2. Vérification des logs

Les logs de paiement sont disponibles dans :
- `storage/logs/laravel.log`

### 3. Test des callbacks

Pour tester les callbacks en local, vous pouvez utiliser ngrok :
```bash
ngrok http 8000
```

Puis utilisez l'URL ngrok dans votre configuration de callback.

## Structure des données

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

### Réponse de l'API
```json
{
  "order_reference": "CP-123456789",
  "bill_url": "https://cashpay.ci/pay/CP-123456789"
}
```

### Callback JWT
Le callback contient un token JWT avec :
- `order_reference` : Référence de la commande
- `status` : SUCCESS, FAILED, ou CANCELLED
- `amount` : Montant payé
- `transaction_id` : ID de la transaction

## Gestion des erreurs

### Erreurs courantes

1. **401 Unauthorized** : Vérifiez vos credentials
2. **400 Bad Request** : Vérifiez le format des données
3. **500 Internal Server Error** : Contactez CashPay

### Logs à surveiller

- Tentatives de paiement
- Réponses de l'API
- Callbacks reçus
- Erreurs de décodage JWT

## Sécurité

- Ne partagez jamais vos credentials
- Utilisez HTTPS en production
- Validez toujours les tokens JWT
- Loggez les tentatives de paiement
- Surveillez les callbacks suspects

## Support

En cas de problème :
1. Vérifiez les logs Laravel
2. Testez avec l'environnement de test
3. Contactez le support CashPay
4. Vérifiez la documentation officielle 
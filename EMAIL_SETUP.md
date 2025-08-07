# Configuration Email pour Production

## Services d'email recommandés pour l'Afrique

### 1. **Yahoo Mail** (Configuration actuelle recommandée)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=hotelrestaurantleprintemps@yahoo.com
MAIL_PASSWORD=votre-mot-de-passe-app-yahoo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hotelrestaurantleprintemps@yahoo.com"
MAIL_FROM_NAME="Hôtel Restaurant Le Printemps"
```

### 2. **SMTP Gmail** (Alternative)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hotelrestaurantleprintemps@yahoo.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hotelrestaurantleprintemps@yahoo.com"
MAIL_FROM_NAME="Hôtel Restaurant Le Printemps"
```

### 2. **Mailgun** (Professionnel)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=votre-domaine.mailgun.org
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS="hotelrestaurantleprintemps@yahoo.com"
MAIL_FROM_NAME="Hôtel Restaurant Le Printemps"
```

### 3. **SendGrid** (Fiable en Afrique)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=votre-clé-api-sendgrid
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hotelrestaurantleprintemps@yahoo.com"
MAIL_FROM_NAME="Hôtel Restaurant Le Printemps"
```

## Instructions de configuration

### Pour Yahoo Mail (configuration actuelle) :
1. Connectez-vous à votre compte Yahoo
2. Allez dans "Paramètres de compte" > "Sécurité du compte"
3. Activez l'authentification à 2 facteurs
4. Générez un "Mot de passe d'application" pour votre application Laravel
5. Utilisez ce mot de passe dans MAIL_PASSWORD

### Pour Gmail (le plus simple) :
1. Activez l'authentification à 2 facteurs sur votre compte Gmail
2. Générez un "Mot de passe d'application" dans les paramètres de sécurité
3. Utilisez ce mot de passe dans MAIL_PASSWORD

### Pour les autres services :
1. Créez un compte sur le service choisi
2. Obtenez vos clés API
3. Configurez votre domaine (optionnel mais recommandé)

## Test en production

Après avoir configuré votre service d'email, testez avec :
```bash
php artisan tinker
```

Puis :
```php
use App\Models\Reservation;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;

$reservation = Reservation::latest()->first();
Mail::to('votre-email-test@gmail.com')->send(new ReservationConfirmationMail($reservation));
```

## Fonctionnalités actuelles

✅ Email de confirmation automatique après paiement
✅ Template professionnel avec détails de réservation
✅ Bouton de téléchargement du reçu dans l'email
✅ Informations importantes pour le client
✅ Design responsive pour mobile
✅ Gestion d'erreurs avec logs
✅ Coordonnées réelles de l'hôtel intégrées
✅ Branding complet "Hôtel Restaurant Le Printemps"

## Informations de contact intégrées

📧 **Email :** hotelrestaurantleprintemps@yahoo.com  
📞 **Téléphones :** +228 71 34 88 88 / 96 06 88 88  
🏨 **Nom complet :** Hôtel Restaurant Le Printemps

## Prochaines améliorations possibles

- Email de rappel 24h avant l'arrivée
- Email de demande d'avis après le départ
- Email d'annulation
- Notifications SMS (Twilio)

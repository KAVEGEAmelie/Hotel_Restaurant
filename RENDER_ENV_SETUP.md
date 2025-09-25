# Configuration Variables d'Environnement Render

## Variables obligatoires à configurer sur Render.com

### 1. Base de données
```
DATABASE_URL=postgresql://hotel_le_printemps_pg_user:MO1ZU9JHBr93Y5ivbvhz4E9ntt7oYS1u@dpg-d3akif2dbo4c738d1i-a.frankfurt-postgres.render.com:5432/hotel_le_printemps_pg
```
⚠️ **Cette URL sera automatiquement définie par Render si vous utilisez leur service PostgreSQL**

### 2. Application Laravel
```
APP_KEY=base64:biNLtRftMrheEP4oy25gKTrFFDD+N2EuNc+APzExiV8=
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hotel-restaurant-leprintemps.onrender.com
```

### 3. Email (optionnel)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=hotelrestaurantleprintemps@yahoo.com
MAIL_PASSWORD=votre_mot_de_passe_application_yahoo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hotelrestaurantleprintemps@yahoo.com
MAIL_FROM_NAME="Hotel Restaurant Le Printemps"
```

## Comment configurer sur Render

1. Allez sur votre service Render
2. Onglet "Environment"
3. Ajoutez ces variables une par une
4. Cliquez sur "Save Changes"
5. Render redéploiera automatiquement

## Problèmes courants

### SSL/TLS Requis
- Render PostgreSQL exige SSL
- Notre configuration `database.php` inclut `sslmode=require`

### APP_KEY manquante
- Générez une nouvelle clé avec : `php artisan key:generate --show`
- Ajoutez-la aux variables d'environnement Render

### Cache et optimisation
- Les scripts de déploiement nettoient automatiquement le cache
- Si problème persiste, essayez un redéploiement manuel
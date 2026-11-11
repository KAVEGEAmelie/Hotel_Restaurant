# Hôtel Restaurant Le Printemps

Site web et back-office de l'Hôtel Restaurant Le Printemps (Kpalimé, Togo) : réservation de chambres en ligne avec paiement **CinetPay** (Mobile Money, carte), présentation du restaurant et administration.

## Fonctionnalités

**Côté public**
- Liste des chambres avec filtres (dates, nombre de personnes, prix, catégorie) et page de détail
- Réservation sans compte, avec vérification de la disponibilité (pas de double réservation)
- Paiement en ligne CinetPay, email de confirmation et reçu PDF
- Pages restaurant (menus PDF, galerie des plats), à propos et contact

**Back-office (`/admin`)**
- Tableau de bord
- Chambres : création, modification, activation/désactivation
- Réservations : recherche, filtres, confirmation/annulation, actions groupées, fiche de police et reçu en PDF
- Restaurant : menus PDF et galerie des plats
- Utilisateurs et rôles (administrateur uniquement)

## Rôles

| Rôle | Accès |
|---|---|
| `client` | Site public, profil, reçus de ses réservations |
| `gerant` | Back-office : chambres, réservations, restaurant |
| `admin` | Tout, y compris la gestion des utilisateurs et des rôles |

Le rôle est stocké dans la colonne `users.role` et se change depuis **Admin › Utilisateurs**.

## Stack

- PHP 8.2+, Laravel 12
- PostgreSQL (ou MySQL)
- Blade, Bootstrap, Tailwind, Vite
- barryvdh/laravel-dompdf (PDF), API CinetPay v2

## Installation

```bash
git clone https://github.com/KAVEGEAmelie/Hotel_Restaurant.git
cd Hotel_Restaurant

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Renseignez dans `.env` la base de données, l'email, `ADMIN_PASSWORD` et les clés CinetPay, puis :

```bash
php artisan migrate --seed     # crée les tables et le compte administrateur
php artisan storage:link       # images des chambres et menus
npm run build
php artisan serve
```

Si `ADMIN_PASSWORD` est vide, un mot de passe aléatoire est généré et affiché une seule fois dans la console.

## Paiement CinetPay

Variables `.env` : `CINETPAY_API_KEY`, `CINETPAY_SITE_ID`, `CINETPAY_SECRET_KEY`, `CINETPAY_CURRENCY` (XOF).

- URL de notification : `https://votre-domaine/payment/webhook` (définie automatiquement à chaque paiement)
- `APP_URL` doit être l'URL publique en HTTPS, sinon CinetPay ne peut pas joindre le webhook
- Le statut d'un paiement est toujours revérifié auprès de l'API CinetPay ; les données reçues ne sont jamais prises telles quelles

## Tâche planifiée

Les réservations restées « en attente » sans paiement pendant plus de 2 heures sont annulées automatiquement pour libérer la chambre (`php artisan reservations:expirer`). En production, ajoutez cette tâche cron (sur Hostinger : *Avancé › Tâches Cron*) :

```
* * * * * cd /chemin/vers/le/projet && php artisan schedule:run >> /dev/null 2>&1
```

## Tests

```bash
php artisan test
```

Les tests utilisent SQLite en mémoire et ne touchent pas à votre base.

## Auteur

**KAVEGEAmelie** — [github.com/KAVEGEAmelie](https://github.com/KAVEGEAmelie)

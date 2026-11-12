# Déploiement sur alwaysdata

Dans ce guide, remplacez `MONCOMPTE` par le nom de votre compte alwaysdata.
Le site sera accessible sur `https://MONCOMPTE.alwaysdata.net`.

> ⚠️ L'offre gratuite d'alwaysdata **interdit l'usage commercial**. Elle convient pour tester et
> montrer le site avec CinetPay en mode **sandbox**. Pour encaisser de vrais clients, passez à
> l'offre payante (à partir de 5 €/mois) ou à un autre hébergeur payant.

## 1. Créer le compte et la base de données (interface web)

1. Créez un compte sur https://www.alwaysdata.com (offre **Free**).
2. **Bases de données › PostgreSQL** : créez un utilisateur, puis une base (par exemple `MONCOMPTE_hotel`)
   avec cet utilisateur en **tous les droits**. Notez le nom de la base, l'utilisateur et le mot de passe.
3. **Accès distant › SSH** : modifiez l'utilisateur SSH par défaut, définissez un mot de passe
   et cochez **activer la connexion par mot de passe**.
4. **Environnement** (ou **Langages**) : choisissez **PHP 8.3** (8.2 minimum).

## 2. Installer le code (en SSH)

Depuis votre ordinateur :

```bash
ssh MONCOMPTE@ssh-MONCOMPTE.alwaysdata.net
```

Puis, sur le serveur :

```bash
git clone https://github.com/KAVEGEAmelie/Hotel_Restaurant.git hotel
cd hotel
cp .env.example .env
nano .env        # remplir comme à l'étape 3 (Ctrl+O pour enregistrer, Ctrl+X pour quitter)
bash deploy.sh
php artisan db:seed --force    # crée le compte administrateur (une seule fois)
```

Si `composer install` s'arrête faute de mémoire (offre gratuite = 256 Mo de RAM), lancez sur
**votre ordinateur** `composer install --no-dev --optimize-autoloader`, puis envoyez le dossier
`vendor/` dans `~/hotel/vendor/` avec FileZilla (SFTP, hôte `ssh-MONCOMPTE.alwaysdata.net`, port 22).
Relancez ensuite `bash deploy.sh`.

## 3. Le fichier `.env` de production

Les lignes à modifier (laissez les autres telles quelles) :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://MONCOMPTE.alwaysdata.net
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=postgresql-MONCOMPTE.alwaysdata.net
DB_PORT=5432
DB_DATABASE=MONCOMPTE_hotel
DB_USERNAME=l_utilisateur_cree_a_l_etape_1
DB_PASSWORD=son_mot_de_passe

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Email : créez d'abord une adresse dans « Emails » de l'interface alwaysdata
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp-MONCOMPTE.alwaysdata.net
MAIL_PORT=465
MAIL_USERNAME=contact@MONCOMPTE.alwaysdata.net
MAIL_PASSWORD=mot_de_passe_de_cette_adresse
MAIL_FROM_ADDRESS="contact@MONCOMPTE.alwaysdata.net"

ADMIN_EMAIL=votre.email@exemple.com
ADMIN_PASSWORD=un_mot_de_passe_solide

CINETPAY_API_KEY=...
CINETPAY_SITE_ID=...
CINETPAY_SECRET_KEY=...
CINETPAY_ENVIRONMENT=sandbox
```

`APP_KEY` est générée automatiquement par `deploy.sh`. **Ne mettez jamais `APP_DEBUG=true` en ligne** :
les erreurs afficheraient vos mots de passe.

## 4. Brancher le site (interface web)

**Web › Sites** : modifiez le site `MONCOMPTE.alwaysdata.net` :

- Type : **PHP**
- Répertoire racine : **`/hotel/public/`** (le dossier `public`, jamais la racine du projet)
- Cochez **Forcer le HTTPS** s'il est proposé

Ouvrez `https://MONCOMPTE.alwaysdata.net` : la page d'accueil doit s'afficher.

## 5. Tâche planifiée (expiration des réservations non payées)

**Avancé › Tâches planifiées** › Ajouter :

- Type : **Commande**
- Commande : `cd /home/MONCOMPTE/hotel && php artisan schedule:run`
- Fréquence : toutes les minutes (`* * * * *`)

## 6. CinetPay

Dans votre espace CinetPay, rien à configurer pour les URL : le site envoie lui-même l'URL de
notification (`https://MONCOMPTE.alwaysdata.net/payment/webhook`) à chaque paiement.
Faites une réservation test et payez avec un numéro de test sandbox.

## Vérifications

- [ ] La page d'accueil et la liste des chambres s'affichent
- [ ] Connexion à `/login` avec `ADMIN_EMAIL` / `ADMIN_PASSWORD`, puis accès à `/admin/dashboard`
- [ ] Ajout d'une chambre **avec photo** dans l'admin, et photo visible sur le site
- [ ] Réservation + paiement sandbox → statut « Confirmée / Payé » dans l'admin et email reçu
- [ ] `https://MONCOMPTE.alwaysdata.net/payment/webhook` affiche `OK`

En cas de page blanche ou d'erreur 500 : `tail -50 ~/hotel/storage/logs/laravel.log` en SSH.

## Mettre à jour le site plus tard

Poussez vos modifications sur GitHub (avec `npm run build` avant si vous avez changé le CSS/JS), puis :

```bash
ssh MONCOMPTE@ssh-MONCOMPTE.alwaysdata.net
cd hotel && bash deploy.sh
```

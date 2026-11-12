#!/usr/bin/env bash
# Déploiement / mise à jour du site sur le serveur.
# Usage (depuis le dossier du projet sur le serveur) : bash deploy.sh
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f .env ]; then
    echo "ERREUR : fichier .env absent. Copiez .env.example vers .env et remplissez-le d'abord." >&2
    exit 1
fi

echo "==> Récupération du code"
git pull --ff-only

echo "==> Dépendances PHP (sans les outils de développement)"
composer install --no-dev --optimize-autoloader --no-interaction

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> Génération de la clé d'application"
    php artisan key:generate --force
fi

echo "==> Base de données"
php artisan migrate --force

echo "==> Lien vers les fichiers envoyés (photos, menus)"
[ -L public/storage ] || php artisan storage:link

echo "==> Mise en cache de la configuration"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Terminé."

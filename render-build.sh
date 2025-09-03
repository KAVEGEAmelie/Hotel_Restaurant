#!/bin/bash
echo "🚀 Démarrage du build sur Render..."

# Installer les dépendances Composer
composer install --no-dev --optimize-autoloader --no-interaction

# Installer les dépendances Node.js
npm install

# Compiler les assets
npm run build

# Générer la clé application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Optimiser l'application
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le stockage
php artisan storage:link

echo "✅ Build terminé !"

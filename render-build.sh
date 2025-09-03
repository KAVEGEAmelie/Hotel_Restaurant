#!/bin/bash
echo "🚀 Démarrage du build optimisé pour Render..."

# Installer Composer rapidement
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances sans interaction
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Générer la clé application
php artisan key:generate --force

# Nettoyer le cache
php artisan optimize:clear

echo "✅ Build terminé !"

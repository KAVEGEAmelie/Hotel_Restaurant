#!/bin/bash

# Copier la configuration de production
cp .env.production .env

# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Installer les dépendances Node.js
npm install

# Construire les assets
npm run build

# Générer la clé d'application
php artisan key:generate --force

# Exécuter les migrations
php artisan migrate --force

# Optimiser Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

#!/bin/bash

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Exécuter les migrations
php artisan migrate --force

# Optimiser l'application pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le stockage
php artisan storage:link

# Configurer Apache pour utiliser le port fourni par Render
if [ ! -z "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
fi

# Démarrer Apache
apache2-foreground

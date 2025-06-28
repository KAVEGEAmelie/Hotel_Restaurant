#!/bin/sh

# Attendre que la base de données soit prête (optionnel mais recommandé)
# sleep 5 

# Lancer les migrations
php artisan migrate --force

# Optimisation de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrage de Supervisor
/usr/bin/supervisord -c /etc/supervisord.conf
#!/bin/sh

# Optimisation de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrage de Supervisor
/usr/bin/supervisord -c /etc/supervisord.conf
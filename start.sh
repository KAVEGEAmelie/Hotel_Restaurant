#!/bin/sh

# Rendre le script exécutable
chmod +x /var/www/html/start.sh

# Lancer les migrations
php artisan migrate --force

# Démarrer le serveur Apache
apache2-foreground

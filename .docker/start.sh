#!/bin/sh

# Génère la clé d'application si elle n'est pas définie
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Lance les migrations et les seeders
php artisan migrate --force --seed

# Crée le lien de stockage
php artisan storage:link

# Démarre le serveur PHP-FPM et Nginx
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

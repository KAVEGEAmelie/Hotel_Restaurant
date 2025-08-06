# Utilise une image de base optimisée pour Laravel
FROM thecodingmachine/php:8.2-v4-fpm-node18

# Copie le code de l'application
COPY . /app

# Installe les dépendances Composer et NPM
RUN composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build

# Gère les permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copie le script de démarrage
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Commande de démarrage
CMD ["start.sh"]

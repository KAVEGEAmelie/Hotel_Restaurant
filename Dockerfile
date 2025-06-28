# Étape 1: Utiliser une image PHP de base avec Composer
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Étape 2: Utiliser une image PHP-FPM pour l'application finale
FROM php:8.2-fpm-alpine

# Variables d'environnement pour Laravel
ENV APP_PORT=8000
ENV DOCKER=true

# Installer les dépendances PHP nécessaires
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libzip-dev \
    nginx \
    supervisor \
    unzip \
    && docker-php-ext-install \
    gd \
    pdo \
    pdo_mysql \
    zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers de l'application
WORKDIR /app
COPY . .

# Copier les dépendances installées à l'étape 1
COPY --from=vendor /app/vendor/ vendor/

# Configurer Nginx
COPY .docker/nginx.conf /etc/nginx/nginx.conf

# Configurer Supervisor pour lancer Nginx et PHP-FPM
COPY .docker/supervisord.conf /etc/supervisord.conf

# Lancer le script de démarrage
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Exposer le port et lancer le script de démarrage
EXPOSE 8000
CMD ["start.sh"]
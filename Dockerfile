# Étape 1: Vendor
FROM composer:2 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

# Étape 2: Application
FROM php:8.2-fpm-alpine

ENV APP_PORT=8080 # On utilise 8080 qui est un port standard
ENV DOCKER=true

RUN apk add --no-cache curl libpng-dev libzip-dev nginx supervisor unzip \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .
COPY --from=vendor /app/vendor/ vendor/

# === NOUVELLE CONFIGURATION NGINX ===
# Copier la configuration principale de Nginx
COPY .docker/nginx.conf /etc/nginx/nginx.conf
# Copier la configuration spécifique à notre site
COPY .docker/default.conf /etc/nginx/http.d/default.conf

COPY .docker/supervisord.conf /etc/supervisord.conf
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Définir les permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8080
CMD ["start.sh"]
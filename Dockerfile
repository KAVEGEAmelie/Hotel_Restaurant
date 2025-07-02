# Stage 1: Installer les dépendances avec Composer
FROM composer:lts as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Stage 2: Construire l'image finale avec Apache et PHP
FROM php:8.2-apache

# Installer les extensions PHP nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && pecl install zip \
    && docker-php-ext-enable zip \
    && docker-php-ext-install pdo pdo_pgsql

# Configurer Apache pour pointer vers le dossier public de Laravel
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copier les fichiers de l'application
COPY . .

# Copier les dépendances installées à l'étape 1
COPY --from=vendor /app/vendor/ vendor/

# Définir les permissions correctes pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 80
EXPOSE 80

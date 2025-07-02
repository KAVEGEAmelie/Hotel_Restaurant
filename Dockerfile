FROM php:8.2-apache

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances PHP Laravel
RUN composer install --no-dev --optimize-autoloader \
    && php artisan key:generate \
    && php artisan storage:link

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html/storage

# Exposer le port Apache
EXPOSE 80

FROM php:8.2-cli

# Installer dépendances Laravel
RUN apt-get update && apt-get install -y \
    unzip zip git curl libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier tout le projet
COPY . .

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader \
    && php artisan key:generate

EXPOSE 8080

# Lancer Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]

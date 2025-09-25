# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Installer Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Nettoyer le cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Apache
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock d'abord
COPY composer.json composer.lock ./

# Installer les dépendances PHP
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Copier package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances Node.js
RUN npm ci --only=production

# Copier le reste des fichiers de l'application
COPY . .

# Compiler les assets
RUN npm run build

# Exécuter les scripts Composer
RUN composer dump-autoload --optimize

# Configuration Apache pour Render
RUN echo '<VirtualHost *:${PORT}>\n\
    DocumentRoot /var/www/html/public\n\
    ServerName localhost\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php\n\
        RewriteEngine On\n\
        RewriteCond %{REQUEST_FILENAME} !-f\n\
        RewriteCond %{REQUEST_FILENAME} !-d\n\
        RewriteRule ^(.*)$ index.php [QSA,L]\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Script de démarrage simple
RUN echo '#!/bin/bash\n\
echo "🚀 Démarrage de l'\''application Laravel..."\n\
\n\
# Copier le fichier d'\''environnement de production\n\
if [ -f .env.production ]; then\n\
    cp .env.production .env\n\
    echo "✅ Fichier .env.production copié vers .env"\n\
fi\n\
\n\
# Configurer Apache pour le port dynamique\n\
echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf\n\
sed -i "s/\${PORT}/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Générer la clé si nécessaire\n\
php artisan key:generate --force\n\
\n\
# Optimiser l'\''application\n\
php artisan optimize:clear\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Créer le lien de stockage\n\
php artisan storage:link || true\n\
\n\
# Démarrer Apache\n\
echo "🌐 Démarrage d'\''Apache sur le port ${PORT:-80}..."\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Exposer le port
EXPOSE ${PORT}

CMD ["/usr/local/bin/start.sh"]

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

# Installer les dépendances Node.js (toutes, y compris dev pour build)
RUN npm ci

# Copier le reste des fichiers de l'application
COPY . .

# Compiler les assets
RUN npm run build

# Nettoyer les node_modules dev après build
RUN npm ci --only=production && rm -rf node_modules/.cache

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

# Définir les permissions et créer les répertoires nécessaires
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/app/public/chambres \
    && mkdir -p /var/www/html/storage/app/public/plats-galerie \
    && mkdir -p /var/www/html/storage/app/public/menus \
    && mkdir -p /var/www/html/public/storage \
    && chmod -R 775 /var/www/html/storage/app/public \
    && chown -R www-data:www-data /var/www/html/storage/app/public

# Script de démarrage avec configuration dynamique
RUN echo '#!/bin/bash\n\
set -e\n\
cd /var/www/html\n\
echo "🚀 Démarrage de l'\''application Laravel..."\n\
echo "📁 Répertoire actuel: $(pwd)"\n\
\n\
# Copier le fichier d'\''environnement de production\n\
if [ -f .env.production ]; then\n\
    cp .env.production .env\n\
    echo "✅ Fichier .env.production copié vers .env"\n\
else\n\
    echo "❌ Fichier .env.production MANQUANT"\n\
    # Créer un .env minimal\n\
    echo "APP_NAME=\"Hotel Restaurant Le Printemps\"" > .env\n\
    echo "APP_ENV=production" >> .env\n\
    echo "APP_KEY=base64:biNLtRftMrheEP4oy25gKTrFFDD+N2EuNc+APzExiV8=" >> .env\n\
    echo "APP_DEBUG=false" >> .env\n\
    echo "APP_URL=https://hotel-restaurant-leprintemps.onrender.com" >> .env\n\
fi\n\
\n\
# Configurer les variables d'\''environnement depuis Render\n\
if [ -n "$DATABASE_URL" ]; then\n\
    echo "✅ DATABASE_URL détecté: $DATABASE_URL"\n\
    \n\
    # Parser pour les composants individuels\n\
    DB_USERNAME=$(echo "$DATABASE_URL" | sed "s|postgresql://\\([^:]*\\):.*|\\1|")\n\
    DB_PASSWORD=$(echo "$DATABASE_URL" | sed "s|postgresql://[^:]*:\\([^@]*\\)@.*|\\1|")\n\
    DB_HOST=$(echo "$DATABASE_URL" | sed "s|.*@\\([^:]*\\):.*|\\1|")\n\
    DB_PORT=$(echo "$DATABASE_URL" | sed "s|.*:\\([0-9]*\\)/.*|\\1|")\n\
    DB_DATABASE=$(echo "$DATABASE_URL" | sed "s|.*/\\([^?]*\\).*|\\1|")\n\
    \n\
    # Utiliser uniquement les variables individuelles (pas DATABASE_URL)\n\
    echo "DB_CONNECTION=pgsql" >> .env\n\
    echo "DB_HOST=$DB_HOST" >> .env\n\
    echo "DB_PORT=$DB_PORT" >> .env\n\
    echo "DB_DATABASE=$DB_DATABASE" >> .env\n\
    echo "DB_USERNAME=$DB_USERNAME" >> .env\n\
    echo "DB_PASSWORD=$DB_PASSWORD" >> .env\n\
    echo "DB_SSLMODE=prefer" >> .env\n\
    \n\
    echo "✅ Configuration BDD extraite: $DB_HOST:$DB_PORT/$DB_DATABASE"\n\
else\n\
    echo "❌ DATABASE_URL non trouvée"\n\
fi\n\
\n\
# Vérifier que .env existe\n\
if [ -f .env ]; then\n\
    echo "✅ Fichier .env créé avec succès"\n\
    echo "📋 Contenu .env:"\n\
    head -10 .env\n\
else\n\
    echo "❌ ERREUR: Fichier .env introuvable après création"\n\
fi\n\
\n\
# Ajouter les autres variables d'\''environnement\n\
[ -n "$MAIL_USERNAME" ] && echo "MAIL_USERNAME=$MAIL_USERNAME" >> .env\n\
[ -n "$MAIL_PASSWORD" ] && echo "MAIL_PASSWORD=$MAIL_PASSWORD" >> .env\n\
[ -n "$CASHPAY_API_KEY" ] && echo "CASHPAY_API_KEY=$CASHPAY_API_KEY" >> .env\n\
[ -n "$CASHPAY_SITE_ID" ] && echo "CASHPAY_SITE_ID=$CASHPAY_SITE_ID" >> .env\n\
[ -n "$CASHPAY_SECRET_KEY" ] && echo "CASHPAY_SECRET_KEY=$CASHPAY_SECRET_KEY" >> .env\n\
\n\
# Configurer Apache pour le port dynamique\n\
echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf\n\
sed -i "s/\${PORT}/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Générer la clé si nécessaire\n\
php artisan key:generate --force || echo "⚠️ Key generation failed"\n\
\n\
# Exécuter les migrations\n\
php artisan migrate --force || echo "⚠️  Migrations échouées"\n\
\n\
# Optimiser l'\''application\n\
php artisan optimize:clear || echo "⚠️ Optimize clear failed"\n\
php artisan config:cache || echo "⚠️ Config cache failed"\n\
php artisan route:cache || echo "⚠️ Route cache failed"\n\
php artisan view:cache || echo "⚠️ View cache failed"\n\
\n\
# Créer le lien de stockage et vérifier\n\
php artisan storage:link --force || echo "⚠️ Storage link failed"\n\
\n\
# Vérifier que les répertoires existent\n\
mkdir -p storage/app/public/chambres\n\
mkdir -p storage/app/public/plats-galerie\n\
mkdir -p storage/app/public/menus\n\
\n\
# S'\''assurer des bonnes permissions\n\
chown -R www-data:www-data storage/ || echo "⚠️ Permission change failed"\n\
chmod -R 775 storage/app/public/ || echo "⚠️ Chmod failed"\n\
\n\
echo "🎯 Script de démarrage terminé avec succès"\n\
\n\
# Démarrer Apache\n\
echo "🌐 Démarrage d'\''Apache sur le port ${PORT:-80}..."\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Exposer le port
EXPOSE ${PORT}

CMD ["/usr/local/bin/start.sh"]

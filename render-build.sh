#!/bin/bash
echo "🚀 Installation et configuration pour Render..."

# Mettre à jour les paquets
sudo apt-get update

# Installer PHP 8.2 et extensions nécessaires
echo "📦 Installation de PHP 8.2..."
sudo apt-get install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-pgsql \
    php8.2-xml \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-gd \
    php8.2-bcmath \
    php8.2-soap \
    php8.2-intl \
    php8.2-readline \
    unzip

# Installer Composer
echo "🎼 Installation de Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier .env.render vers .env pour la production
if [ -f .env.render ]; then
    cp .env.render .env
    echo "✅ Configuration d'environnement copiée (.env.render -> .env)"
else
    echo "⚠️ Fichier .env.render non trouvé, création d'un .env basique"
    echo "APP_ENV=production" > .env
fi

# Installer les dépendances PHP
echo "📚 Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Installer les dépendances Node.js
echo "📦 Installation des dépendances Node.js..."
npm ci

# Compiler les assets
echo "🔨 Compilation des assets..."
npm run build

# Générer la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate --force

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien de stockage
echo "📁 Création du lien de stockage..."
php artisan storage:link

echo "✅ Build terminé avec succès !"

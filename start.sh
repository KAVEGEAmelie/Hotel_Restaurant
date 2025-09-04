#!/bin/bash
echo "🚀 Démarrage de l'application Laravel..."

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        cp .env.production .env
        echo "✅ Fichier .env.production copié"
    else
        echo "APP_ENV=production" > .env
        echo "APP_DEBUG=false" >> .env
        echo "APP_KEY=base64:Xmj2ol9FNXacWfCgPDNCNacKvWngXgbmrL2j6EUWc+0=" >> .env
        echo "✅ Fichier .env créé"
    fi
fi

# Créer les dossiers nécessaires
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Définir les permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Générer la clé si nécessaire
php artisan key:generate --force

# Créer le lien de stockage
php artisan storage:link || true

# Optimiser pour la production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Application prête !"

# Démarrer le serveur
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

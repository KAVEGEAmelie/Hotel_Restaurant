#!/bin/bash
set -e

echo "🚀 Démarrage de l'application Laravel..."

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    if [ -f .env.render ]; then
        cp .env.render .env
        echo "✅ Fichier .env.render copié"
    else
        echo "APP_ENV=production" > .env
        echo "APP_DEBUG=false" >> .env
        echo "APP_KEY=base64:Xmj2ol9FNXacWfCgPDNCNacKvWngXgbmrL2j6EUWc+0=" >> .env
        echo "✅ Fichier .env créé avec clé par défaut"
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

# Fonction pour vérifier la connexion à la base de données
check_db_connection() {
    echo "🔍 Vérification de la connexion à la base de données..."
    for i in {1..5}; do
        if php artisan db:show > /dev/null 2>&1; then
            echo "✅ Connexion à la base de données établie"
            return 0
        else
            echo "⏳ Tentative $i/5 - Attente de la connexion à la base de données..."
            sleep 5
        fi
    done
    echo "❌ Impossible de se connecter à la base de données après 5 tentatives"
    return 1
}

# Vérifier la connexion avant d'exécuter les commandes qui nécessitent la DB
if check_db_connection; then
    # Exécuter les migrations
    echo "🔄 Exécution des migrations..."
    php artisan migrate --force || echo "⚠️ Les migrations ont échoué ou sont déjà appliquées"

    # Optimisation conditionnelle
    echo "⚡ Optimisation de l'application..."
    php artisan config:cache || echo "⚠️ La mise en cache de la configuration a échoué"
    php artisan route:cache || echo "⚠️ La mise en cache des routes a échoué"
    php artisan view:cache || echo "⚠️ La mise en cache des vues a échoué"
    
    # Tenter de vider le cache uniquement si la DB est accessible
    php artisan cache:clear 2>/dev/null || echo "⚠️ Impossible de vider le cache (base de données inaccessible)"
else
    echo "⚠️ Poursuite du démarrage sans optimisation complète"
fi

# Gérer le lien de stockage
if [ -L public/storage ]; then
    echo "🔗 Le lien symbolique existe déjà, suppression..."
    rm -f public/storage
fi
php artisan storage:link || echo "⚠️ Impossible de créer le lien de stockage"

# Optimiser pour la production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Application prête !"

# Démarrer le serveur
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

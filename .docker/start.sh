#!/bin/bash
echo "🚀 Démarrage de l'application Laravel..."

# Attendre que la base de données soit prête (max 30 secondes)
echo "⏳ Attente de la base de données..."
for i in {1..30}; do
    if php artisan db:monitor >/dev/null 2>&1; then
        echo "✅ Base de données accessible"
        break
    fi
    echo "⏱️ Tentative $i/30 - Base de données non accessible"
    sleep 1
done

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
php artisan migrate --force

# Lier le storage
echo "📁 Liaison du storage..."
php artisan storage:link

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan optimize:clear
php artisan optimize

# Générer la clé si elle n'existe pas
if [ -z "$(grep 'APP_KEY=base64:' .env)" ]; then
    echo "🔑 Génération de la clé application..."
    php artisan key:generate --force
fi

# Démarrer Apache en premier plan
echo "🌐 Démarrage d'Apache..."
exec apache2-foreground

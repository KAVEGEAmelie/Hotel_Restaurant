#!/bin/bash
echo "🚀 Démarrage du déploiement..."

# Attendre que la base de données soit prête
sleep 10

# Exécuter les migrations
php artisan migrate --force

# Lier le storage
php artisan storage:link

# Optimiser l'application
php artisan optimize:clear
php artisan optimize

echo "✅ Déploiement terminé !"

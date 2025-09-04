#!/bin/bash
echo "🚀 Post-deployment script..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
sleep 10

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
php artisan migrate --force

# Créer le lien de storage
echo "📁 Création du lien de storage..."
php artisan storage:link

echo "✅ Post-deployment terminé !"

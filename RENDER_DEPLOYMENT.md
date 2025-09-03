# Déploiement sur Render - Hôtel Restaurant Le Printemps

## 📋 Prérequis

- Compte Render.com
- Dépôt Git connecté à Render
- Base de données PostgreSQL configurée

## 🚀 Instructions de Déploiement

### 1. Configuration de la Base de Données

1. Créer une base de données PostgreSQL sur Render
2. Noter les informations de connexion

### 2. Configuration du Service Web

1. Connecter votre dépôt GitHub/GitLab à Render
2. Créer un nouveau service Web
3. Utiliser les paramètres suivants :
   - **Runtime**: Docker
   - **Build Command**: Automatique (Dockerfile)
   - **Start Command**: Automatique (CMD dans Dockerfile)

### 3. Variables d'Environnement

Configurer les variables d'environnement suivantes dans Render :

```env
APP_NAME=Hotel Restaurant Le Printemps
APP_ENV=production
APP_DEBUG=false
APP_KEY=[Généré automatiquement]
DB_CONNECTION=pgsql
DB_HOST=[Host de votre DB PostgreSQL]
DB_PORT=5432
DB_DATABASE=[Nom de votre DB]
DB_USERNAME=[Username de votre DB]
DB_PASSWORD=[Password de votre DB]
LOG_CHANNEL=stderr
```

### 4. Configuration des Domaines

1. Aller dans les paramètres de votre service
2. Configurer votre domaine personnalisé si nécessaire
3. Activer HTTPS automatique

### 5. Déploiement

Le déploiement se fera automatiquement à chaque push sur la branche principale.

## 🔧 Structure du Projet

- `Dockerfile` : Configuration Docker pour l'environnement de production
- `render.yaml` : Configuration Blueprint pour Render
- `.docker/` : Scripts et configurations Docker
- `render-build.sh` : Script de build personnalisé (si nécessaire)

## 📁 Fichiers Importants

- **Dockerfile** : Image Docker optimisée avec PHP 8.2 + Apache
- **render.yaml** : Configuration complète du service et de la DB
- **.docker/start.sh** : Script de démarrage avec migrations et optimisations
- **.docker/apache/000-default.conf** : Configuration Apache pour Laravel

## 🔍 Dépannage

### Erreurs Communes

1. **Erreur de migration** : Vérifier les variables de DB
2. **Erreur 500** : Vérifier les logs dans Render Dashboard
3. **Assets manquants** : Vérifier que `npm run build` s'exécute correctement

### Logs

Accéder aux logs via le Dashboard Render :
- Service → Logs
- Filtrer par type : Build, Deploy, Runtime

## 📞 Support

En cas de problème, vérifier :
1. Les logs de build
2. Les logs de runtime  
3. La configuration des variables d'environnement
4. La connexion à la base de données

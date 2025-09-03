# 🚀 Guide de Déploiement sur Render

## Étapes pour déployer votre Hôtel Restaurant Le Printemps

### 1. 📋 Prérequis
- [x] Compte GitHub configuré
- [x] Code poussé sur GitHub
- [x] Compte Render.com créé

### 2. 🛠️ Fichiers de Configuration (✅ Déjà prêts)
- [x] `render.yaml` - Configuration principale
- [x] `Dockerfile` - Image Docker optimisée
- [x] `render-build.sh` - Script de build
- [x] `.docker/start.sh` - Script de démarrage
- [x] `.docker/apache/000-default.conf` - Configuration Apache
- [x] `.env.production` - Variables d'environnement de production

### 3. 🎯 Étapes de Déploiement

#### A. Pusher le code sur GitHub
```bash
git add .
git commit -m "Configuration complète pour déploiement Render"
git push origin main
```

#### B. Créer le service sur Render.com
1. Aller sur [render.com](https://render.com)
2. Cliquer sur "New +" → "Blueprint"
3. Connecter votre repository GitHub
4. Sélectionner `Hotel_Restaurant`
5. Render détectera automatiquement le `render.yaml`

#### C. Configuration automatique
Render va automatiquement :
- ✅ Créer la base de données PostgreSQL
- ✅ Configurer les variables d'environnement
- ✅ Builder l'application avec Docker
- ✅ Déployer sur le domaine : `hotel-restaurant-le-printemps.onrender.com`

### 4. 🔧 Variables d'Environnement (Auto-configurées)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:Xmj2ol9FNXacWfCgPDNCNacKvWngXgbmrL2j6EUWc+0=`
- `APP_URL=https://hotel-restaurant-le-printemps.onrender.com`
- `DATABASE_URL` (fournie automatiquement par Render)

### 5. 📊 Après le Déploiement
1. **Créer un admin** : Accéder à `/register` pour créer le premier utilisateur admin
2. **Tester les fonctionnalités** :
   - ✅ Création de chambres
   - ✅ Système de réservation
   - ✅ Génération de PDF (bons, fiches de police)
   - ✅ Upload de menus PDF
   - ✅ QR codes

### 6. 🆘 En cas de problème
- **Logs** : Voir les logs dans le dashboard Render
- **Base de données** : Vérifier la connexion PostgreSQL
- **Migrations** : Se font automatiquement au démarrage

### 7. ✨ URL de Production
Votre site sera accessible à :
**https://hotel-restaurant-le-printemps.onrender.com**

---

## 🎉 Félicitations !
Votre Hôtel Restaurant Le Printemps sera en ligne avec :
- 🏨 Système de réservation complet
- 💳 Gestion des paiements (CashPay + CinetPay)
- 📄 Génération automatique de PDF
- 🍽️ Gestion de restaurant avec menus
- 👥 Interface d'administration complète
- 📱 Design responsive et moderne

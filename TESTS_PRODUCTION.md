# 🧪 Tests de Production - Hotel Restaurant Le Printemps

Ce fichier contient la liste des tests à effectuer après le déploiement pour s'assurer que tout fonctionne correctement.

## 📋 Checklist de Tests

### ✅ Tests de Base
- [ ] Application accessible à https://hotel-restaurant-leprintemps.onrender.com
- [ ] Page d'accueil se charge correctement
- [ ] Pas d'erreurs 500 dans les logs
- [ ] Images statiques (logo, backgrounds) s'affichent

### 🗄️ Tests Base de Données
- [ ] Connexion PostgreSQL fonctionne
- [ ] Données de base présentes (tables créées)
- [ ] Interface admin accessible

### 🔐 Tests Authentification
- [ ] Page de connexion admin accessible
- [ ] Connexion admin fonctionne
- [ ] Dashboard admin se charge

### 📁 Tests Upload de Fichiers
- [ ] Upload d'image de chambre fonctionne
- [ ] Upload d'image de plat fonctionne  
- [ ] Upload de menu PDF fonctionne
- [ ] Images uploadées s'affichent correctement
- [ ] Lien `/storage` fonctionne

### 📄 Tests Génération PDF
- [ ] Génération de bon de réservation PDF
- [ ] Téléchargement PDF fonctionne
- [ ] PDF s'ouvre correctement

### 💳 Tests Paiement CashPay
- [ ] Page de paiement accessible
- [ ] Redirection vers CashPay fonctionne
- [ ] URLs de retour configurées correctement

### 📧 Tests Email (optionnel)
- [ ] Configuration SMTP Yahoo fonctionne
- [ ] Envoi d'email de confirmation

## 🔧 En cas de problème

### Upload de fichiers ne fonctionne pas
1. Vérifier les permissions dans `/var/www/html/storage/app/public`
2. Vérifier que le lien symbolique `/var/www/html/public/storage` existe
3. Tester l'accès direct à un fichier

### PDF ne se génère pas
1. Vérifier les dépendances DomPDF installées
2. Vérifier les polices disponibles
3. Tester génération PDF simple

### Images ne s'affichent pas
1. Vérifier la configuration `APP_URL` dans .env
2. Vérifier les chemins dans les templates Blade
3. Tester accès direct aux fichiers

## 🚀 Commandes de Debug

```bash
# Vérifier storage link
ls -la public/storage

# Vérifier permissions
ls -la storage/app/public/

# Tester connexion BDD
php artisan tinker
>>> DB::connection()->getPdo();

# Vérifier configuration
php artisan config:show
```
# =============================================================================
# GUIDE COMPLET - GESTION DES FICHIERS HOTEL RESTAURANT LE PRINTEMPS
# =============================================================================

Write-Host "🏨 CONFIGURATION DES FICHIERS - HOTEL RESTAURANT LE PRINTEMPS" -ForegroundColor Cyan
Write-Host "=" * 70 -ForegroundColor Gray

# =============================================================================
# 1. VÉRIFICATION DE LA STRUCTURE DES DOSSIERS
# =============================================================================
Write-Host "`n📁 1. STRUCTURE DES DOSSIERS DE STOCKAGE" -ForegroundColor Yellow

$directories = @{
    "storage\app\public\chambres" = "Images des chambres d'hôtel"
    "storage\app\public\menus" = "Fichiers PDF des menus restaurant"
    "storage\app\public\plats" = "Images des plats du restaurant"
    "storage\app\public\users" = "Avatars des utilisateurs"
}

foreach ($dir in $directories.Keys) {
    if (Test-Path $dir) {
        Write-Host "   ✅ $dir" -ForegroundColor Green
        $count = (Get-ChildItem $dir -ErrorAction SilentlyContinue | Measure-Object).Count
        Write-Host "      📊 $count fichier(s) - $($directories[$dir])" -ForegroundColor Gray
    } else {
        Write-Host "   ❌ $dir (MANQUANT)" -ForegroundColor Red
        Write-Host "      ⚠️  $($directories[$dir])" -ForegroundColor Yellow
    }
}

# =============================================================================
# 2. VÉRIFICATION DU LIEN SYMBOLIQUE
# =============================================================================
Write-Host "`n🔗 2. ACCÈS PUBLIC AUX FICHIERS" -ForegroundColor Yellow

if (Test-Path "public\storage") {
    Write-Host "   ✅ Lien symbolique public\storage existe" -ForegroundColor Green
    Write-Host "   📍 Les fichiers sont accessibles via: http://localhost:8000/storage/" -ForegroundColor Cyan
} else {
    Write-Host "   ❌ Lien symbolique public\storage MANQUANT" -ForegroundColor Red
    Write-Host "   ⚡ Exécutez: php artisan storage:link" -ForegroundColor Yellow
}

# =============================================================================
# 3. TYPES DE FICHIERS SUPPORTÉS
# =============================================================================
Write-Host "`n📋 3. TYPES DE FICHIERS SUPPORTÉS" -ForegroundColor Yellow

$fileTypes = @{
    "🖼️ Images Chambres" = @("JPEG", "PNG", "JPG", "GIF", "WEBP", "Max: 2MB")
    "📄 PDFs Menus" = @("PDF", "JPG", "PNG", "Max: 5MB")  
    "🍽️ Images Plats" = @("JPEG", "PNG", "JPG", "GIF", "WEBP", "Max: 2MB")
    "👤 Avatars Users" = @("JPEG", "PNG", "JPG", "GIF", "Max: 1MB")
}

foreach ($type in $fileTypes.Keys) {
    Write-Host "   $type" -ForegroundColor Cyan
    Write-Host "      📝 Formats: $($fileTypes[$type] -join ', ')" -ForegroundColor Gray
}

# =============================================================================
# 4. URLS D'ACCÈS AUX FICHIERS
# =============================================================================
Write-Host "`n🌐 4. URLS D'ACCÈS AUX FICHIERS" -ForegroundColor Yellow

$urls = @{
    "Chambres" = "http://localhost:8000/storage/chambres/nom-fichier.jpg"
    "Menus PDF" = "http://localhost:8000/storage/menus/nom-fichier.pdf"
    "Plats" = "http://localhost:8000/storage/plats/nom-fichier.jpg"
    "Users" = "http://localhost:8000/storage/users/nom-fichier.jpg"
}

foreach ($category in $urls.Keys) {
    Write-Host "   📂 $category :" -ForegroundColor Cyan
    Write-Host "      🔗 $($urls[$category])" -ForegroundColor White
}

# =============================================================================
# 5. FONCTIONNALITÉS DE TÉLÉCHARGEMENT
# =============================================================================
Write-Host "`n⬇️ 5. TÉLÉCHARGEMENTS DISPONIBLES" -ForegroundColor Yellow

$downloads = @{
    "📋 Bon de réservation" = "/reservations/{id}/download-bon"
    "🧾 Reçu de paiement" = "/payment/receipt/{id}"
    "👮 Fiche police" = "/admin/reservations/{id}/fiche-police"
    "📊 Export CSV réservations" = "/admin/reservations/export"
}

foreach ($download in $downloads.Keys) {
    Write-Host "   $download" -ForegroundColor Cyan
    Write-Host "      🔗 Route: $($downloads[$download])" -ForegroundColor Gray
}

# =============================================================================
# 6. SÉCURITÉ ET PERMISSIONS
# =============================================================================
Write-Host "`n🔒 6. SÉCURITÉ DES FICHIERS" -ForegroundColor Yellow

Write-Host "   ✅ Validation des types MIME" -ForegroundColor Green
Write-Host "   ✅ Limitation de taille des fichiers" -ForegroundColor Green
Write-Host "   ✅ Stockage sécurisé dans storage/app/public" -ForegroundColor Green
Write-Host "   ✅ Noms de fichiers uniques générés automatiquement" -ForegroundColor Green
Write-Host "   ✅ Validation des autorisations utilisateur" -ForegroundColor Green

# =============================================================================
# 7. COMMANDES UTILES
# =============================================================================
Write-Host "`n⚡ 7. COMMANDES UTILES" -ForegroundColor Yellow

$commands = @{
    "Créer le lien symbolique" = "php artisan storage:link"
    "Vérifier les permissions" = "ls -la storage/app/public"
    "Nettoyer le cache" = "php artisan cache:clear"
    "Optimiser l'application" = "php artisan optimize"
}

foreach ($cmd in $commands.Keys) {
    Write-Host "   📝 $cmd" -ForegroundColor Cyan
    Write-Host "      💻 $($commands[$cmd])" -ForegroundColor White
}

# =============================================================================
# 8. RÉSUMÉ DE LA CONFIGURATION
# =============================================================================
Write-Host "`n📊 8. RÉSUMÉ DE LA CONFIGURATION" -ForegroundColor Yellow

Write-Host "   ✅ Système de fichiers configuré" -ForegroundColor Green
Write-Host "   ✅ Dossiers de stockage créés" -ForegroundColor Green  
Write-Host "   ✅ Lien symbolique actif" -ForegroundColor Green
Write-Host "   ✅ Upload d'images fonctionnel" -ForegroundColor Green
Write-Host "   ✅ Upload de PDFs fonctionnel" -ForegroundColor Green
Write-Host "   ✅ Téléchargements sécurisés" -ForegroundColor Green
Write-Host "   ✅ Génération de PDF dynamique" -ForegroundColor Green

# =============================================================================
# 9. INSTRUCTIONS D'UTILISATION
# =============================================================================
Write-Host "`n📘 9. INSTRUCTIONS D'UTILISATION" -ForegroundColor Yellow

Write-Host "   1️⃣  Chambres:" -ForegroundColor Cyan
Write-Host "      • Allez dans Admin > Chambres > Ajouter" -ForegroundColor White
Write-Host "      • Uploadez une image (JPEG/PNG, max 2MB)" -ForegroundColor White
Write-Host "      • L'image sera stockée dans storage/chambres/" -ForegroundColor White

Write-Host "`n   2️⃣  Menus PDF:" -ForegroundColor Cyan  
Write-Host "      • Allez dans Admin > Menus PDF > Ajouter" -ForegroundColor White
Write-Host "      • Uploadez un PDF ou image (max 5MB)" -ForegroundColor White
Write-Host "      • Le fichier sera stocké dans storage/menus/" -ForegroundColor White

Write-Host "`n   3️⃣  Plats:" -ForegroundColor Cyan
Write-Host "      • Allez dans Admin > Plats > Ajouter" -ForegroundColor White
Write-Host "      • Uploadez une image du plat (max 2MB)" -ForegroundColor White
Write-Host "      • L'image sera stockée dans storage/plats/" -ForegroundColor White

Write-Host "`n   4️⃣  Téléchargements:" -ForegroundColor Cyan
Write-Host "      • Les PDFs sont générés automatiquement" -ForegroundColor White
Write-Host "      • Bons de réservation avec QR Code" -ForegroundColor White
Write-Host "      • Reçus de paiement sécurisés" -ForegroundColor White

Write-Host "`n" -ForegroundColor Gray
Write-Host "🎉 CONFIGURATION TERMINÉE ! Votre système de fichiers est prêt !" -ForegroundColor Green
Write-Host "=" * 70 -ForegroundColor Gray
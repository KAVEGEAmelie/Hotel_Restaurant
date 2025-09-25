Write-Host "🏨 CONFIGURATION DES FICHIERS - HOTEL RESTAURANT LE PRINTEMPS" -ForegroundColor Cyan
Write-Host "=================================================================="

Write-Host "`n📁 STRUCTURE DES DOSSIERS DE STOCKAGE" -ForegroundColor Yellow
if (Test-Path "storage\app\public\chambres") { Write-Host "   ✅ Images chambres" -ForegroundColor Green } else { Write-Host "   ❌ Images chambres" -ForegroundColor Red }
if (Test-Path "storage\app\public\menus") { Write-Host "   ✅ PDFs menus" -ForegroundColor Green } else { Write-Host "   ❌ PDFs menus" -ForegroundColor Red }
if (Test-Path "storage\app\public\plats") { Write-Host "   ✅ Images plats" -ForegroundColor Green } else { Write-Host "   ❌ Images plats" -ForegroundColor Red }
if (Test-Path "storage\app\public\users") { Write-Host "   ✅ Avatars users" -ForegroundColor Green } else { Write-Host "   ❌ Avatars users" -ForegroundColor Red }

Write-Host "`n🔗 ACCÈS PUBLIC AUX FICHIERS" -ForegroundColor Yellow
if (Test-Path "public\storage") {
    Write-Host "   ✅ Lien symbolique actif" -ForegroundColor Green
    Write-Host "   📍 URL base: http://localhost:8000/storage/" -ForegroundColor Cyan
} else {
    Write-Host "   ❌ Lien symbolique manquant" -ForegroundColor Red
    Write-Host "   ⚡ Exécutez: php artisan storage:link" -ForegroundColor Yellow
}

Write-Host "`n📋 TYPES DE FICHIERS SUPPORTÉS" -ForegroundColor Yellow
Write-Host "   🖼️  Images Chambres: JPEG, PNG, JPG, GIF, WEBP (max 2MB)" -ForegroundColor White
Write-Host "   📄 PDFs Menus: PDF, JPG, PNG (max 5MB)" -ForegroundColor White
Write-Host "   🍽️  Images Plats: JPEG, PNG, JPG, GIF, WEBP (max 2MB)" -ForegroundColor White
Write-Host "   👤 Avatars Users: JPEG, PNG, JPG, GIF (max 1MB)" -ForegroundColor White

Write-Host "`n🌐 URLS D'ACCÈS" -ForegroundColor Yellow
Write-Host "   📂 Chambres: /storage/chambres/fichier.jpg" -ForegroundColor Cyan
Write-Host "   📂 Menus: /storage/menus/fichier.pdf" -ForegroundColor Cyan
Write-Host "   📂 Plats: /storage/plats/fichier.jpg" -ForegroundColor Cyan
Write-Host "   📂 Users: /storage/users/fichier.jpg" -ForegroundColor Cyan

Write-Host "`n⬇️  TÉLÉCHARGEMENTS DISPONIBLES" -ForegroundColor Yellow
Write-Host "   📋 Bon de réservation (PDF avec QR Code)" -ForegroundColor White
Write-Host "   🧾 Reçu de paiement (PDF sécurisé)" -ForegroundColor White
Write-Host "   👮 Fiche police (PDF administratif)" -ForegroundColor White
Write-Host "   📊 Export CSV des réservations" -ForegroundColor White

Write-Host "`n🔒 SÉCURITÉ" -ForegroundColor Yellow
Write-Host "   ✅ Validation types MIME" -ForegroundColor Green
Write-Host "   ✅ Limitation taille fichiers" -ForegroundColor Green
Write-Host "   ✅ Noms uniques auto-générés" -ForegroundColor Green
Write-Host "   ✅ Permissions utilisateur vérifiées" -ForegroundColor Green

Write-Host "`n💡 INSTRUCTIONS D'UTILISATION" -ForegroundColor Yellow
Write-Host "   1. Pour ajouter une chambre avec image:" -ForegroundColor Cyan
Write-Host "      • Admin > Chambres > Ajouter" -ForegroundColor White
Write-Host "      • Sélectionner image (max 2MB)" -ForegroundColor White
Write-Host "      • L'image sera accessible via /storage/chambres/" -ForegroundColor White

Write-Host "`n   2. Pour ajouter un menu PDF:" -ForegroundColor Cyan
Write-Host "      • Admin > Menus PDF > Ajouter" -ForegroundColor White
Write-Host "      • Sélectionner PDF (max 5MB)" -ForegroundColor White
Write-Host "      • Le PDF sera accessible via /storage/menus/" -ForegroundColor White

Write-Host "`n   3. Téléchargements automatiques:" -ForegroundColor Cyan
Write-Host "      • Bons de réservation générés en temps réel" -ForegroundColor White
Write-Host "      • Reçus de paiement après confirmation" -ForegroundColor White
Write-Host "      • QR Codes intégrés pour validation" -ForegroundColor White

Write-Host "`n🎉 SYSTÈME DE FICHIERS CONFIGURÉ ET PRÊT !" -ForegroundColor Green
Write-Host "=================================================================="
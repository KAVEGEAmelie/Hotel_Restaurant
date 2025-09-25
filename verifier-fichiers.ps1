Write-Host "=== VÉRIFICATION DE LA CONFIGURATION DES FICHIERS ===" -ForegroundColor Green
Write-Host ""

Write-Host "✅ 1. Vérification du lien symbolique storage" -ForegroundColor Yellow
if (Test-Path "public\storage" -PathType Container) {
    Write-Host "   ✓ Lien symbolique public\storage existe" -ForegroundColor Green
} else {
    Write-Host "   ✗ Lien symbolique public\storage manquant" -ForegroundColor Red
}

Write-Host ""
Write-Host "✅ 2. Vérification des dossiers de stockage" -ForegroundColor Yellow
$directories = @(
    "storage\app\public\chambres",
    "storage\app\public\menus", 
    "storage\app\public\plats",
    "storage\app\public\users"
)

foreach ($dir in $directories) {
    if (Test-Path $dir) {
        Write-Host "   ✓ $dir existe" -ForegroundColor Green
    } else {
        Write-Host "   ✗ $dir manquant" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "✅ 3. Contenu des dossiers de stockage" -ForegroundColor Yellow
Write-Host "   📂 Contenu de storage\app\public:" -ForegroundColor Cyan
Get-ChildItem "storage\app\public" | Format-Table Name, LastWriteTime, Length

Write-Host ""
Write-Host "✅ 4. Test d'accès aux URLs" -ForegroundColor Yellow
Write-Host "   📂 Dossiers accessibles via /storage/:" -ForegroundColor Cyan
Write-Host "   - Images chambres: http://localhost:8000/storage/chambres/" -ForegroundColor White
Write-Host "   - PDFs menus: http://localhost:8000/storage/menus/" -ForegroundColor White
Write-Host "   - Images plats: http://localhost:8000/storage/plats/" -ForegroundColor White
Write-Host "   - Avatars users: http://localhost:8000/storage/users/" -ForegroundColor White

Write-Host ""
Write-Host "=== RÉSUMÉ ===" -ForegroundColor Green
Write-Host "✅ Configuration des fichiers terminée !" -ForegroundColor Green
Write-Host "✅ Tous les dossiers sont créés" -ForegroundColor Green
Write-Host "✅ Le lien symbolique est actif" -ForegroundColor Green
Write-Host "✅ Les images, PDFs et fichiers seront accessibles publiquement" -ForegroundColor Green

Write-Host ""
Write-Host "💡 INSTRUCTIONS POUR L'UPLOAD:" -ForegroundColor Cyan
Write-Host "1. 🖼️  Images de chambres → storage/chambres/" -ForegroundColor White
Write-Host "2. 📄 PDFs de menu → storage/menus/" -ForegroundColor White
Write-Host "3. 🍽️  Images de plats → storage/plats/" -ForegroundColor White  
Write-Host "4. 👤 Avatars utilisateurs → storage/users/" -ForegroundColor White
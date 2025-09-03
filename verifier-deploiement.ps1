# Script de vérification pour le déploiement Render
Write-Host "🔍 Vérification des fichiers de déploiement..." -ForegroundColor Cyan

function Check-File {
    param([string]$filePath)
    if (Test-Path $filePath) {
        Write-Host "✅ $filePath" -ForegroundColor Green
        return $true
    } else {
        Write-Host "❌ $filePath manquant" -ForegroundColor Red
        return $false
    }
}

function Check-Directory {
    param([string]$dirPath)
    if (Test-Path $dirPath -PathType Container) {
        Write-Host "✅ $dirPath/" -ForegroundColor Green
        return $true
    } else {
        Write-Host "❌ $dirPath/ manquant" -ForegroundColor Red
        return $false
    }
}

Write-Host "`n📋 Fichiers de configuration Render :" -ForegroundColor Yellow
$files = @(
    "render.yaml",
    "Dockerfile", 
    "render-build.sh",
    ".env.production"
)

foreach ($file in $files) {
    Check-File $file
}

Write-Host "`n📁 Configuration Docker :" -ForegroundColor Yellow
Check-Directory ".docker"
Check-File ".docker\start.sh"
Check-File ".docker\apache\000-default.conf"

Write-Host "`n🔨 Fichiers Laravel :" -ForegroundColor Yellow
$laravelFiles = @(
    "composer.json",
    "package.json", 
    "artisan"
)

foreach ($file in $laravelFiles) {
    Check-File $file
}

Write-Host "`n🎉 Vérification terminée !" -ForegroundColor Green
Write-Host "📝 Prêt pour le déploiement sur Render.com" -ForegroundColor Yellow
Write-Host "📖 Consultez DEPLOIEMENT_RENDER.md pour les étapes suivantes" -ForegroundColor Yellow

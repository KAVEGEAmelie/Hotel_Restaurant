#!/bin/bash
echo "🔍 Vérification des fichiers de déploiement..."

# Couleurs pour les messages
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour vérifier un fichier
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅ $1${NC}"
        return 0
    else
        echo -e "${RED}❌ $1 manquant${NC}"
        return 1
    fi
}

# Fonction pour vérifier un dossier
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✅ $1/${NC}"
        return 0
    else
        echo -e "${RED}❌ $1/ manquant${NC}"
        return 1
    fi
}

echo -e "${YELLOW}📋 Fichiers de configuration Render :${NC}"
check_file "render.yaml"
check_file "Dockerfile"
check_file "render-build.sh"
check_file ".env.production"

echo -e "\n${YELLOW}📁 Configuration Docker :${NC}"
check_dir ".docker"
check_file ".docker/start.sh"
check_file ".docker/apache/000-default.conf"

echo -e "\n${YELLOW}🔨 Fichiers Laravel :${NC}"
check_file "composer.json"
check_file "package.json"
check_file "artisan"

echo -e "\n${YELLOW}🎯 Permissions des scripts :${NC}"
if [ -x "render-build.sh" ]; then
    echo -e "${GREEN}✅ render-build.sh exécutable${NC}"
else
    echo -e "${YELLOW}⚠️ Rendons render-build.sh exécutable...${NC}"
    chmod +x render-build.sh
fi

if [ -x ".docker/start.sh" ]; then
    echo -e "${GREEN}✅ .docker/start.sh exécutable${NC}"
else
    echo -e "${YELLOW}⚠️ Rendons .docker/start.sh exécutable...${NC}"
    chmod +x .docker/start.sh
fi

echo -e "\n${GREEN}🎉 Vérification terminée !${NC}"
echo -e "${YELLOW}📝 Prêt pour le déploiement sur Render.com${NC}"
echo -e "${YELLOW}📖 Consultez DEPLOIEMENT_RENDER.md pour les étapes suivantes${NC}"

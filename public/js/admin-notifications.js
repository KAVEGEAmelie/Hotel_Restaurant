// Notifications spécifiques pour l'administration
class AdminNotifications {
    
    static showUserLogin(user) {
        window.notify.success(
            `👋 Bienvenue ${user.name} !`,
            { duration: 4000 }
        );
    }
    
    static showUserLogout() {
        window.notify.info(
            '👋 Déconnexion réussie. À bientôt !',
            { duration: 3000 }
        );
    }
    
    static showDataSaved(type) {
        window.notify.success(
            `✅ ${type} sauvegardé avec succès !`,
            { duration: 4000 }
        );
    }
    
    static showDataDeleted(type) {
        window.notify.warning(
            `🗑️ ${type} supprimé`,
            { duration: 4000 }
        );
    }
    
    static showValidationErrors(errors) {
        const errorMessages = Object.values(errors).flat();
        errorMessages.forEach((error, index) => {
            setTimeout(() => {
                window.notify.error(`❌ ${error}`, { duration: 6000 });
            }, index * 400);
        });
    }
    
    static showPermissionDenied() {
        window.notify.error(
            '🚫 Accès refusé - Permissions insuffisantes',
            { duration: 6000 }
        );
    }
    
    static showDataLoading() {
        return window.notify.info(
            '⏳ Chargement des données...',
            { autoClose: false, clickToClose: false }
        );
    }
    
    static showExportSuccess(filename) {
        window.notify.success(
            `📊 Export réussi : ${filename}`,
            { duration: 5000 }
        );
    }
    
    static showImportSuccess(count) {
        window.notify.success(
            `📥 ${count} élément(s) importé(s) avec succès`,
            { duration: 5000 }
        );
    }
    
    static showBackupCreated() {
        window.notify.success(
            '💾 Sauvegarde créée avec succès',
            { duration: 5000 }
        );
    }
    
    static showSystemError() {
        window.notify.error(
            '🔧 Erreur système - Contactez l\'administrateur',
            { duration: 8000 }
        );
    }
    
    static showNewReservation(reservation) {
        window.notify.reservation(
            `🔔 Nouvelle réservation #${reservation.id} reçue !`,
            { duration: 6000 }
        );
    }
    
    static showPaymentReceived(amount) {
        window.notify.payment(
            `💰 Paiement reçu : ${amount.toLocaleString()} FCFA`,
            { duration: 6000 }
        );
    }
    
    static showQuickAction(action) {
        window.notify.info(
            `⚡ ${action} effectué`,
            { duration: 3000 }
        );
    }
    
    static showBulkAction(count, action) {
        window.notify.success(
            `✅ ${count} élément(s) ${action}`,
            { duration: 4000 }
        );
    }
}

// Rendre disponible globalement
window.AdminNotifications = AdminNotifications;

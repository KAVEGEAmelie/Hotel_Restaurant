// Notifications spécifiques pour les réservations
class ReservationNotifications {
    
    static showBookingSuccess(reservation) {
        window.notify.reservation(
            `🎉 Réservation #${reservation.id} créée avec succès !`,
            { duration: 5000 }
        );
        
        setTimeout(() => {
            window.notify.info(
                `📋 Votre réservation pour ${reservation.chambre} est en attente de paiement`,
                { duration: 7000 }
            );
        }, 1500);
    }
    
    static showPaymentProcessing() {
        return window.notify.payment(
            '⏳ Traitement du paiement en cours...',
            { autoClose: false, clickToClose: false }
        );
    }
    
    static showPaymentSuccess(reservation) {
        window.notify.success(
            `✅ Paiement de ${reservation.prix_total.toLocaleString()} FCFA confirmé !`,
            { duration: 6000 }
        );
    }
    
    static showEmailSent(email) {
        window.notify.email(
            `📧 Confirmation envoyée à ${email}`,
            { duration: 5000 }
        );
    }
    
    static showReceiptReady() {
        window.notify.info(
            '📄 Votre reçu est prêt à télécharger',
            { duration: 8000 }
        );
    }
    
    static showError(message) {
        window.notify.error(
            `❌ ${message}`,
            { duration: 8000 }
        );
    }
    
    static showValidationError(errors) {
        Object.values(errors).flat().forEach((error, index) => {
            setTimeout(() => {
                window.notify.warning(error, { duration: 6000 });
            }, index * 300);
        });
    }
    
    static showSimulationMode() {
        window.notify.warning(
            '🔧 Mode simulation activé - Paiement factice',
            { duration: 4000 }
        );
    }
    
    static showLoginRequired() {
        window.notify.info(
            '🔐 Connexion requise pour effectuer une réservation',
            { duration: 6000 }
        );
    }
    
    static showCheckAvailability() {
        window.notify.info(
            '📅 Vérification de la disponibilité...',
            { duration: 3000 }
        );
    }
    
    static showRoomUnavailable(dates) {
        window.notify.error(
            `❌ Chambre non disponible pour les dates sélectionnées`,
            { duration: 8000 }
        );
    }
    
    static showBookingReminder() {
        window.notify.reservation(
            '⏰ N\'oubliez pas de finaliser votre réservation !',
            { duration: 6000 }
        );
    }
}

// Rendre disponible globalement
window.ReservationNotifications = ReservationNotifications;

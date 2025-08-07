// Système de notifications modernes
class NotificationSystem {
    constructor() {
        this.container = this.createContainer();
        this.init();
    }

    createContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'notification-container';
        document.body.appendChild(container);
        return container;
    }

    init() {
        // Écouter les événements personnalisés
        document.addEventListener('notification:show', (e) => {
            this.show(e.detail.type, e.detail.message, e.detail.options);
        });

        // Traiter les messages Flash de Laravel au chargement
        this.processFlashMessages();
    }

    processFlashMessages() {
        // Messages depuis Laravel session flash
        const flashMessages = window.flashMessages || {};
        
        Object.keys(flashMessages).forEach(type => {
            if (flashMessages[type]) {
                this.show(type, flashMessages[type], { autoClose: true });
            }
        });
    }

    show(type, message, options = {}) {
        const notification = this.createNotification(type, message, options);
        this.container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Auto-fermeture
        if (options.autoClose !== false) {
            setTimeout(() => {
                this.hide(notification);
            }, options.duration || 5000);
        }

        return notification;
    }

    createNotification(type, message, options = {}) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const icon = this.getIcon(type);
        const closeBtn = options.closable !== false ? '<button class="notification-close">&times;</button>' : '';
        
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">${icon}</div>
                <div class="notification-message">${message}</div>
                ${closeBtn}
            </div>
            <div class="notification-progress"></div>
        `;

        // Événement de fermeture
        const closeButton = notification.querySelector('.notification-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                this.hide(notification);
            });
        }

        // Clic sur la notification pour la fermer
        notification.addEventListener('click', () => {
            if (options.clickToClose !== false) {
                this.hide(notification);
            }
        });

        return notification;
    }

    hide(notification) {
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    getIcon(type) {
        const icons = {
            success: '<i class="bi bi-check-circle-fill"></i>',
            error: '<i class="bi bi-x-circle-fill"></i>',
            warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
            info: '<i class="bi bi-info-circle-fill"></i>',
            payment: '<i class="bi bi-credit-card-fill"></i>',
            reservation: '<i class="bi bi-calendar-check-fill"></i>',
            email: '<i class="bi bi-envelope-fill"></i>'
        };
        return icons[type] || icons.info;
    }

    // Méthodes de raccourci
    success(message, options = {}) {
        return this.show('success', message, options);
    }

    error(message, options = {}) {
        return this.show('error', message, options);
    }

    warning(message, options = {}) {
        return this.show('warning', message, options);
    }

    info(message, options = {}) {
        return this.show('info', message, options);
    }

    payment(message, options = {}) {
        return this.show('payment', message, options);
    }

    reservation(message, options = {}) {
        return this.show('reservation', message, options);
    }

    email(message, options = {}) {
        return this.show('email', message, options);
    }
}

// Initialiser le système
document.addEventListener('DOMContentLoaded', () => {
    window.notifications = new NotificationSystem();
});

// API globale pour les notifications
window.notify = {
    success: (message, options) => window.notifications?.success(message, options),
    error: (message, options) => window.notifications?.error(message, options),
    warning: (message, options) => window.notifications?.warning(message, options),
    info: (message, options) => window.notifications?.info(message, options),
    payment: (message, options) => window.notifications?.payment(message, options),
    reservation: (message, options) => window.notifications?.reservation(message, options),
    email: (message, options) => window.notifications?.email(message, options)
};

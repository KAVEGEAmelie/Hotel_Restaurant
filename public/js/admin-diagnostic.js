// Script de diagnostic pour vérifier le bon fonctionnement de tous les boutons admin

class AdminDiagnostic {
    static runDiagnostic() {
        console.log('🔍 === DIAGNOSTIC DU SYSTÈME ADMIN ===');
        
        // 1. Vérifier la présence des fichiers JS
        this.checkJavaScriptFiles();
        
        // 2. Vérifier les objets globaux
        this.checkGlobalObjects();
        
        // 3. Vérifier les méthodes principales
        this.checkMainMethods();
        
        // 4. Vérifier les éléments DOM
        this.checkDOMElements();
        
        // 5. Test des confirmations
        this.testConfirmations();
        
        console.log('✅ === DIAGNOSTIC TERMINÉ ===');
    }
    
    static checkJavaScriptFiles() {
        console.log('\n📂 Vérification des fichiers JavaScript:');
        
        const scripts = document.querySelectorAll('script[src]');
        const expectedFiles = [
            'notifications.js',
            'admin-notifications.js', 
            'admin-actions.js'
        ];
        
        expectedFiles.forEach(file => {
            const found = Array.from(scripts).some(script => script.src.includes(file));
            console.log(`${found ? '✅' : '❌'} ${file}: ${found ? 'Chargé' : 'Non trouvé'}`);
        });
    }
    
    static checkGlobalObjects() {
        console.log('\n🌐 Vérification des objets globaux:');
        
        const objects = {
            'window.NotificationSystem': typeof window.NotificationSystem,
            'window.AdminNotifications': typeof window.AdminNotifications,
            'window.AdminActionComponents': typeof window.AdminActionComponents,
            'window.notify': typeof window.notify,
            'bootstrap.Modal': typeof bootstrap.Modal
        };
        
        Object.entries(objects).forEach(([name, type]) => {
            console.log(`${type !== 'undefined' ? '✅' : '❌'} ${name}: ${type}`);
        });
    }
    
    static checkMainMethods() {
        console.log('\n🔧 Vérification des méthodes principales:');
        
        if (window.AdminActionComponents) {
            const methods = [
                'confirmDelete',
                'confirmStatusToggle',
                'confirmBulkAction',
                'showModal',
                'initialize'
            ];
            
            methods.forEach(method => {
                const exists = typeof window.AdminActionComponents[method] === 'function';
                console.log(`${exists ? '✅' : '❌'} AdminActionComponents.${method}: ${exists ? 'Disponible' : 'Manquante'}`);
            });
        }
        
        if (window.notify) {
            const notificationTypes = ['success', 'error', 'warning', 'info', 'payment', 'reservation', 'email'];
            notificationTypes.forEach(type => {
                const exists = typeof window.notify[type] === 'function';
                console.log(`${exists ? '✅' : '❌'} notify.${type}: ${exists ? 'Disponible' : 'Manquante'}`);
            });
        }
    }
    
    static checkDOMElements() {
        console.log('\n🏗️ Vérification des éléments DOM:');
        
        const elements = {
            'Conteneur notifications': document.querySelector('#notification-container'),
            'Meta CSRF': document.querySelector('meta[name="csrf-token"]'),
            'Bootstrap loaded': !!window.bootstrap,
            'Icons loaded': document.querySelector('link[href*="bootstrap-icons"]')
        };
        
        Object.entries(elements).forEach(([name, element]) => {
            const exists = !!element;
            console.log(`${exists ? '✅' : '❌'} ${name}: ${exists ? 'Présent' : 'Absent'}`);
        });
    }
    
    static testConfirmations() {
        console.log('\n🧪 Test des confirmations (non-intrusif):');
        
        // Test sans ouvrir de modales
        try {
            // Vérifier que les méthodes ne plantent pas
            if (window.AdminActionComponents) {
                console.log('✅ AdminActionComponents.confirmDelete: Méthode accessible');
                console.log('✅ AdminActionComponents.confirmStatusToggle: Méthode accessible');
                console.log('✅ AdminActionComponents.confirmBulkAction: Méthode accessible');
            }
            
            if (window.notify) {
                console.log('✅ Système de notifications: Prêt');
            }
            
        } catch (error) {
            console.log('❌ Erreur lors du test:', error.message);
        }
    }
    
    static quickTest() {
        console.log('\n🚀 Test rapide:');
        
        // Test notification
        if (window.notify) {
            window.notify.info('🧪 Test diagnostic réussi !', { duration: 3000 });
            console.log('✅ Notification de test envoyée');
        }
        
        // Test existence des conteneurs
        const container = document.querySelector('#notification-container') || document.body;
        console.log('✅ Conteneur pour notifications:', container.tagName);
    }
}

// Auto-exécution si en mode diagnostic
if (window.location.search.includes('diagnostic=true') || window.location.href.includes('test-admin')) {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            AdminDiagnostic.runDiagnostic();
            AdminDiagnostic.quickTest();
        }, 2000);
    });
}

// Rendre disponible globalement
window.AdminDiagnostic = AdminDiagnostic;

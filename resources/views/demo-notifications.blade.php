@extends('layouts.app')

@section('title', 'Démonstration des Notifications')

@push('scripts')
<script src="{{ asset('js/reservation-notifications.js') }}"></script>
<script src="{{ asset('js/admin-notifications.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let demoIndex = 0;
    const demos = [
        () => window.notify.success('✅ Action réussie avec succès !'),
        () => window.notify.error('❌ Une erreur est survenue'),
        () => window.notify.warning('⚠️ Attention, vérifiez vos données'),
        () => window.notify.info('ℹ️ Information importante'),
        () => window.notify.payment('💳 Paiement en cours de traitement'),
        () => window.notify.reservation('🏨 Nouvelle réservation créée'),
        () => window.notify.email('📧 Email envoyé avec succès'),
        () => ReservationNotifications.showBookingSuccess({id: 123, chambre: 'Suite Deluxe'}),
        () => ReservationNotifications.showPaymentSuccess({prix_total: 75000}),
        () => AdminNotifications.showUserLogin({name: 'Amélie'}),
        () => AdminNotifications.showDataSaved('Réservation'),
        () => AdminNotifications.showNewReservation({id: 456})
    ];
    
    function runDemo() {
        if (demoIndex < demos.length) {
            demos[demoIndex]();
            demoIndex++;
            setTimeout(runDemo, 2000);
        } else {
            setTimeout(() => {
                window.notify.success('🎉 Démonstration terminée !', {duration: 5000});
            }, 1000);
        }
    }
    
    // Démarrer la démo après 1 seconde
    setTimeout(runDemo, 1000);
});
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h2 class="mb-4">🎨 Démonstration des Notifications Modernes</h2>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Système de notifications activé !</strong><br>
                        Observez les notifications qui apparaissent dans le coin supérieur droit.
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>🎯 Types de notifications :</h5>
                            <ul class="list-unstyled text-start">
                                <li>✅ <strong>Succès</strong> - Actions réussies</li>
                                <li>❌ <strong>Erreur</strong> - Problèmes rencontrés</li>
                                <li>⚠️ <strong>Avertissement</strong> - Points d'attention</li>
                                <li>ℹ️ <strong>Information</strong> - Messages informatifs</li>
                                <li>💳 <strong>Paiement</strong> - Transactions</li>
                                <li>🏨 <strong>Réservation</strong> - Réservations</li>
                                <li>📧 <strong>Email</strong> - Communications</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>✨ Fonctionnalités :</h5>
                            <ul class="list-unstyled text-start">
                                <li>🎭 <strong>Animations fluides</strong></li>
                                <li>📱 <strong>Design responsive</strong></li>
                                <li>🖱️ <strong>Fermeture au clic</strong></li>
                                <li>⏱️ <strong>Fermeture automatique</strong></li>
                                <li>🎨 <strong>Couleurs thématiques</strong></li>
                                <li>🔧 <strong>Intégration Laravel</strong></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button onclick="window.notify.success('🎉 Test réussi !')" class="btn btn-success me-2">
                            Tester Succès
                        </button>
                        <button onclick="window.notify.error('❌ Test d\'erreur')" class="btn btn-danger me-2">
                            Tester Erreur
                        </button>
                        <button onclick="ReservationNotifications.showBookingSuccess({id: Math.floor(Math.random()*1000), chambre: 'Suite Test'})" class="btn btn-primary">
                            Tester Réservation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

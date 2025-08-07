@extends('layouts.app')

@section('title', 'Simulation de Paiement')

@push('scripts')
<script src="{{ asset('js/reservation-notifications.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification d'entrée en mode simulation
    ReservationNotifications.showSimulationMode();
    
    // Ajouter des gestionnaires d'événements aux boutons
    const successBtn = document.querySelector('button[value="success"]');
    const cancelBtn = document.querySelector('button[value="cancel"]');
    
    successBtn.addEventListener('click', function(e) {
        const processingNotification = ReservationNotifications.showPaymentProcessing();
        
        // Simulation du délai de traitement
        setTimeout(() => {
            if (processingNotification && processingNotification.parentNode) {
                window.notifications.hide(processingNotification);
            }
        }, 1500);
    });
    
    cancelBtn.addEventListener('click', function(e) {
        window.notify.warning('❌ Annulation du paiement...', { duration: 2000 });
    });
});
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Mode Simulation CashPay
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <strong>Mode Test Activé</strong><br>
                        Aucun vrai paiement ne sera effectué.
                    </div>
                    
                    <h5>Montant à payer :</h5>
                    <div class="display-4 text-primary mb-4">
                        {{ number_format($amount, 0, ',', ' ') }} FCFA
                    </div>
                    
                    <p class="text-muted mb-4">
                        Cette page simule le processus de paiement CashPay. 
                        Choisissez le résultat souhaité pour tester votre application.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('payment.simulation.complete') }}">
                            @csrf
                            <button type="submit" name="action" value="success" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>
                                Simuler un Paiement Réussi
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('payment.simulation.complete') }}">
                            @csrf
                            <button type="submit" name="action" value="cancel" class="btn btn-secondary btn-lg w-100">
                                <i class="bi bi-x-circle me-2"></i>
                                Simuler un Paiement Annulé
                            </button>
                        </form>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Pour désactiver le mode simulation, modifiez votre fichier .env
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

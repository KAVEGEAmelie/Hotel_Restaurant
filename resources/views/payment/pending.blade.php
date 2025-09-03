@extends('layouts.app')

@section('title', 'Paiement en cours - Hôtel Le Printemps')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <br>
                        Paiement en cours de traitement
                    </h4>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">Vérification du statut de votre paiement...</h5>
                        <p class="lead">Nous vérifions l'état de votre transaction auprès de notre partenaire CashPay.</p>
                    </div>

                    <!-- Spinner de chargement -->
                    <div class="mb-4">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>

                    <!-- Détails de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="bg-light p-4 rounded">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-bed me-2"></i>
                                    Votre réservation
                                </h6>
                                <p><strong>Numéro :</strong> #{{ $reservation->id }}</p>
                                <p><strong>Chambre :</strong> {{ $reservation->chambre->nom ?? $reservation->chambre->numero }}</p>
                                <p><strong>Check-in :</strong> {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y') }}</p>
                                <p><strong>Check-out :</strong> {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y') }}</p>
                                <hr>
                                <p class="text-muted">Montant : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</p>
                                @if($reservation->cashpay_invoice_id)
                                    <p class="text-muted small">ID Transaction : {{ $reservation->cashpay_invoice_id }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Messages d'état -->
                    <div id="status-message" class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Vérification en cours... Veuillez patienter.
                    </div>

                    <!-- Actions -->
                    <div class="mt-4">
                        <button id="check-status-btn" class="btn btn-outline-warning me-2" onclick="checkPaymentStatus()">
                            <i class="fas fa-sync-alt me-1"></i> Vérifier le statut
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i> Retour à l'accueil
                        </a>
                    </div>

                    <!-- Instructions -->
                    <div class="alert alert-light mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>Que faire maintenant ?</h6>
                        <ul class="text-start mb-0 small">
                            <li>Si vous avez effectué le paiement via CashPay, attendez quelques instants</li>
                            <li>Le statut sera mis à jour automatiquement</li>
                            <li>Vous recevrez un email de confirmation une fois le paiement validé</li>
                            <li>En cas de problème, contactez notre service client</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let statusCheckInterval;
let checkCount = 0;
const maxChecks = 20; // Maximum 20 vérifications

function checkPaymentStatus() {
    const btn = document.getElementById('check-status-btn');
    const statusMessage = document.getElementById('status-message');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Vérification...';
    
    fetch('{{ route("payment.status", $reservation->id) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                switch (data.reservation_status) {
                    case 'paye':
                        statusMessage.className = 'alert alert-success';
                        statusMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>Paiement confirmé ! Redirection en cours...';
                        setTimeout(() => {
                            window.location.href = '{{ route("payment.success", $reservation->id) }}';
                        }, 2000);
                        return;
                        
                    case 'echec':
                        statusMessage.className = 'alert alert-danger';
                        statusMessage.innerHTML = '<i class="fas fa-times-circle me-2"></i>Paiement échoué. Veuillez réessayer.';
                        clearInterval(statusCheckInterval);
                        break;
                        
                    default:
                        statusMessage.className = 'alert alert-warning';
                        statusMessage.innerHTML = '<i class="fas fa-clock me-2"></i>Paiement en cours... Vérification automatique dans 5 secondes.';
                }
            } else {
                statusMessage.className = 'alert alert-warning';
                statusMessage.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Impossible de vérifier le statut. Réessayez dans quelques instants.';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            statusMessage.className = 'alert alert-danger';
            statusMessage.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erreur de connexion. Veuillez réessayer.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Vérifier le statut';
        });
}

// Vérification automatique toutes les 5 secondes
statusCheckInterval = setInterval(() => {
    checkCount++;
    if (checkCount > maxChecks) {
        clearInterval(statusCheckInterval);
        document.getElementById('status-message').className = 'alert alert-warning';
        document.getElementById('status-message').innerHTML = '<i class="fas fa-clock me-2"></i>Temps d\'attente dépassé. Veuillez vérifier manuellement le statut.';
        return;
    }
    checkPaymentStatus();
}, 5000);

// Première vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(checkPaymentStatus, 2000);
});
</script>

@endsection

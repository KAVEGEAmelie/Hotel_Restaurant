@extends('layouts.app')

@section('title', 'Paiement en cours')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-warning">
                <div class="card-header bg-warning text-dark text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <br>
                        Paiement en cours de traitement
                    </h3>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h4 class="text-warning mb-3">Votre paiement est en cours de vérification</h4>
                        <p class="lead">Veuillez patienter pendant que nous confirmons votre transaction.</p>
                    </div>

                    <!-- Spinner de chargement -->
                    <div class="mb-4">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-3 text-muted">Vérification en cours...</p>
                    </div>

                    <!-- Détails de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="bg-light p-4 rounded">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-bed me-2"></i>
                                    Votre réservation
                                </h5>
                                <p><strong>Numéro :</strong> #{{ $reservation->id }}</p>
                                <p><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</p>
                                <p><strong>Check-in :</strong> {{ \Carbon\Carbon::parse($reservation->date_arrivee)->format('d/m/Y') }}</p>
                                <p><strong>Check-out :</strong> {{ \Carbon\Carbon::parse($reservation->date_depart)->format('d/m/Y') }}</p>
                                @if($reservation->transaction_id)
                                <p><strong>Transaction :</strong> {{ $reservation->transaction_id }}</p>
                                @endif
                                <hr>
                                <h5 class="text-warning">
                                    <strong>Statut : En cours de traitement</strong>
                                </h5>
                                <p class="text-muted">Montant : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de vérification -->
                    <div class="mb-4">
                        <button onclick="checkPaymentStatus()" class="btn btn-primary btn-lg" id="check-button">
                            <i class="fas fa-sync-alt me-2"></i>
                            Vérifier le statut du paiement
                        </button>
                    </div>

                    <!-- Actions alternatives -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-home me-2"></i>
                                    Retour à l'accueil
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="mailto:support@hotel.com" class="btn btn-outline-info btn-lg w-100">
                                    <i class="fas fa-envelope me-2"></i>
                                    Contacter le support
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Que faire maintenant ?</h6>
                        <ul class="text-start mb-0">
                            <li>Votre paiement peut prendre quelques minutes à être confirmé</li>
                            <li>Vous recevrez un email de confirmation dès que le paiement sera validé</li>
                            <li>N'actualisez pas cette page et ne refaites pas de paiement</li>
                            <li>En cas de problème, notre équipe vous contactera</li>
                        </ul>
                    </div>

                    <!-- Auto-refresh notice -->
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-sync-alt me-2"></i>
                            Cette page se actualise automatiquement toutes les 30 secondes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border-radius: 15px;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fa-clock {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.spinner-border {
    animation: spin 1s linear infinite;
}
</style>
@endpush

@push('scripts')
<script>
function checkPaymentStatus() {
    const button = document.getElementById('check-button');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Vérification...';
    
    fetch('{{ route("payment.check", $reservation->id) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'ACCEPTED' || data.status === 'COMPLETED') {
                    window.location.href = '{{ route("payment.success", $reservation->id) }}';
                } else if (data.status === 'REFUSED' || data.status === 'CANCELLED') {
                    window.location.href = '{{ route("payment.failed", $reservation->id) }}';
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Vérifier le statut du paiement';
        });
}

// Auto-refresh toutes les 30 secondes
setInterval(() => {
    checkPaymentStatus();
}, 30000);
</script>
@endpush
@endsection

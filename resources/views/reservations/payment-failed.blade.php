@extends('layouts.app')

@section('title', 'Paiement échoué')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <br>
                        Paiement échoué
                    </h3>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h4 class="text-danger mb-3">Votre paiement n'a pas pu être traité</h4>
                        <p class="lead">Ne vous inquiétez pas, aucun montant n'a été débité de votre compte.</p>
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
                                <hr>
                                <h5 class="text-warning">
                                    <strong>Statut : En attente de paiement</strong>
                                </h5>
                                <p class="text-muted">Montant : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('payment.cinetpay', $reservation->id) }}" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-redo me-2"></i>
                                    Réessayer le paiement
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-home me-2"></i>
                                    Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Causes possibles -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Causes possibles de l'échec</h6>
                        <ul class="text-start mb-0">
                            <li>Solde insuffisant sur votre compte</li>
                            <li>Données de paiement incorrectes</li>
                            <li>Problème de connexion internet</li>
                            <li>Limite de transaction dépassée</li>
                            <li>Carte bancaire expirée ou bloquée</li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-headset me-2"></i>Besoin d'aide ?</h6>
                        <p class="mb-0">
                            Si le problème persiste, contactez notre service client au 
                            <strong>+228 XX XX XX XX</strong> ou par email à 
                            <strong>support@hotel.com</strong>
                        </p>
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

.fa-times-circle {
    animation: shake 0.6s ease-in-out;
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-5px);
    }
    75% {
        transform: translateX(5px);
    }
}
</style>
@endpush
@endsection

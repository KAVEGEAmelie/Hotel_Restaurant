@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-success">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <br>
                        Paiement réussi !
                    </h3>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h4 class="text-success mb-3">Félicitations ! Votre réservation est confirmée</h4>
                        <p class="lead">Merci pour votre confiance. Votre paiement a été traité avec succès.</p>
                    </div>

                    <!-- Détails de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="bg-light p-4 rounded">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-receipt me-2"></i>
                                    Détails de votre réservation
                                </h5>
                                <p><strong>Numéro :</strong> #{{ $reservation->id }}</p>
                                <p><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</p>
                                <p><strong>Check-in :</strong> {{ \Carbon\Carbon::parse($reservation->date_arrivee)->format('d/m/Y') }}</p>
                                <p><strong>Check-out :</strong> {{ \Carbon\Carbon::parse($reservation->date_depart)->format('d/m/Y') }}</p>
                                @if($reservation->transaction_id)
                                <p><strong>Transaction :</strong> {{ $reservation->transaction_id }}</p>
                                @endif
                                <hr>
                                <h5 class="text-success">
                                    <strong>Montant payé : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</strong>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('payment.receipt', $reservation->id) }}" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-download me-2"></i>
                                    Télécharger le reçu
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('home') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-home me-2"></i>
                                    Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Informations importantes -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-envelope me-2"></i>Email de confirmation</h6>
                        <p class="mb-0">
                            Un email de confirmation a été envoyé à <strong>{{ $reservation->user->email }}</strong> 
                            avec tous les détails de votre réservation.
                        </p>
                    </div>

                    <!-- Informations pratiques -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations importantes</h6>
                        <ul class="text-start mb-0">
                            <li>Présentez-vous à la réception avec une pièce d'identité valide</li>
                            <li>L'arrivée est possible à partir de 14h00</li>
                            <li>Le départ doit se faire avant 12h00</li>
                            <li>Pour toute modification, contactez notre service client</li>
                        </ul>
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

.fa-check-circle {
    animation: bounce 1s ease-in-out;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>
@endpush
@endsection

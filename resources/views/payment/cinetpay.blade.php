@extends('layouts.app')

@section('title', 'Paiement de votre réservation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Paiement sécurisé avec CinetPay
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Résumé de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-bed me-2"></i>
                                Détails de votre réservation
                            </h5>
                            <div class="bg-light p-3 rounded">
                                <p><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</p>
                                <p><strong>Check-in :</strong> {{ \Carbon\Carbon::parse($reservation->date_arrivee)->format('d/m/Y') }}</p>
                                <p><strong>Check-out :</strong> {{ \Carbon\Carbon::parse($reservation->date_depart)->format('d/m/Y') }}</p>
                                <p><strong>Nombre d'adultes :</strong> {{ $reservation->nombre_adultes }}</p>
                                @if($reservation->nombre_enfants > 0)
                                <p><strong>Nombre d'enfants :</strong> {{ $reservation->nombre_enfants }}</p>
                                @endif
                                <hr>
                                <h5 class="text-success">
                                    <strong>Total : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</strong>
                                </h5>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-user me-2"></i>
                                Informations client
                            </h5>
                            <div class="bg-light p-3 rounded">
                                <p><strong>Nom :</strong> {{ $reservation->user->name }}</p>
                                <p><strong>Email :</strong> {{ $reservation->user->email }}</p>
                                @if($reservation->user->telephone)
                                <p><strong>Téléphone :</strong> {{ $reservation->user->telephone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Moyens de paiement disponibles -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-mobile-alt me-2"></i>
                            Moyens de paiement disponibles
                        </h5>
                        <div class="row text-center">
                            <div class="col-6 col-md-3 mb-2">
                                <div class="payment-method p-2 border rounded">
                                    <i class="fab fa-cc-visa fa-2x text-primary"></i>
                                    <small class="d-block">Carte Visa</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="payment-method p-2 border rounded">
                                    <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                                    <small class="d-block">Mastercard</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="payment-method p-2 border rounded">
                                    <i class="fas fa-mobile fa-2x text-success"></i>
                                    <small class="d-block">Orange Money</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="payment-method p-2 border rounded">
                                    <i class="fas fa-mobile fa-2x text-warning"></i>
                                    <small class="d-block">MTN Money</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de paiement -->
                    <form action="{{ route('payment.initiate', $reservation->id) }}" method="POST" id="payment-form">
                        @csrf
                        
                        <!-- Informations complémentaires -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">
                                    Numéro de téléphone <span class="text-muted">(optionnel)</span>
                                </label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                       value="{{ old('customer_phone', $reservation->user->telephone) }}"
                                       placeholder="+228 XX XX XX XX">
                            </div>
                            <div class="col-md-6">
                                <label for="customer_address" class="form-label">
                                    Adresse <span class="text-muted">(optionnel)</span>
                                </label>
                                <input type="text" class="form-control" id="customer_address" name="customer_address" 
                                       value="{{ old('customer_address') }}"
                                       placeholder="Votre adresse">
                            </div>
                        </div>

                        <!-- Bouton de paiement -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5" id="pay-button">
                                <i class="fas fa-lock me-2"></i>
                                Procéder au paiement sécurisé
                                <br>
                                <small>{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</small>
                            </button>
                        </div>

                        <!-- Informations de sécurité -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Paiement 100% sécurisé par CinetPay
                            </small>
                        </div>
                    </form>

                    <!-- Informations importantes -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations importantes :</h6>
                        <ul class="mb-0">
                            <li>Vous serez redirigé vers la plateforme sécurisée CinetPay</li>
                            <li>Vous recevrez un email de confirmation après le paiement</li>
                            <li>En cas de problème, contactez notre service client</li>
                            <li>Le paiement est sécurisé et vos données sont protégées</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.payment-method {
    transition: all 0.3s ease;
}

.payment-method:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
    transform: translateY(-2px);
}

#pay-button {
    transition: all 0.3s ease;
}

#pay-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    const button = document.getElementById('pay-button');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redirection en cours...';
});
</script>
@endpush
@endsection

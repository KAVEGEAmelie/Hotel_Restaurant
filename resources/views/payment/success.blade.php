@extends('layouts.app')

@section('title', 'Paiement Confirmé')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de succès principale
    setTimeout(() => {
        window.notify.payment('💳 Paiement confirmé avec succès !', { 
            duration: 6000,
            clickToClose: false 
        });
    }, 500);
    
    // Notification d'email envoyé
    setTimeout(() => {
        window.notify.email('📧 Email de confirmation envoyé à {{ $reservation->client_email }}', { 
            duration: 8000 
        });
    }, 2000);
    
    // Notification d'informations importantes
    setTimeout(() => {
        window.notify.reservation('ℹ️ Arrivée à partir de 14h00, départ avant 12h00', { 
            duration: 10000 
        });
    }, 4000);
});
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center">
                    <!-- Icon de succès -->
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Message de succès -->
                    <h2 class="text-success mb-3">Paiement Confirmé !</h2>
                    <p class="lead mb-4">Votre réservation a été confirmée avec succès.</p>

                    <!-- Détails de la réservation -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Détails de votre réservation</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Numéro de réservation :</strong> #{{ $reservation->id }}</p>
                                    <p><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</p>
                                    <p><strong>Client :</strong> {{ $reservation->client_prenom }} {{ $reservation->client_nom }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Check-in :</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</p>
                                    <p><strong>Check-out :</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</p>
                                    <p><strong>Montant payé :</strong> <span class="text-success fw-bold">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</span></p>
                                </div>
                            </div>
                            @if($reservation->transaction_id)
                            <p><strong>Référence de transaction :</strong> {{ $reservation->transaction_id }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('payment.receipt', $reservation) }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-download me-2"></i>
                            Télécharger le Reçu
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-house me-2"></i>
                            Retour à l'Accueil
                        </a>
                    </div>

                    <!-- Information supplémentaire -->
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Un email de confirmation a été envoyé à <strong>{{ $reservation->client_email }}</strong>
                            <br><small class="text-muted">
                                @if(config('mail.default') === 'log')
                                    <i class="bi bi-exclamation-triangle text-warning"></i> 
                                    Mode développement : l'email est sauvegardé dans les logs (storage/logs/laravel.log)
                                @else
                                    Vérifiez votre boîte de réception et vos spams.
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

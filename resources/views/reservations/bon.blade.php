@extends('layouts.app')

@section('title', 'Bon de Réservation #' . $reservation->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-success">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                        <br>
                        Bon de Réservation
                    </h3>
                    <p class="mb-0 mt-2">Veuillez présenter ce bon à l'hôtel</p>
                </div>
                
                <div class="card-body">
                    <!-- Numéro de réservation prominent -->
                    <div class="text-center mb-4">
                        <h2 class="text-success">
                            <i class="fas fa-hashtag"></i> {{ $reservation->id }}
                        </h2>
                        <p class="text-muted">Numéro de réservation</p>
                    </div>

                    <!-- Informations de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-bed me-2"></i>
                                    Détails de la chambre
                                </h5>
                                <p><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</p>
                                <p><strong>Type :</strong> {{ $reservation->chambre->categorie->nom ?? 'Standard' }}</p>
                                <p><strong>Capacité :</strong> {{ $reservation->chambre->capacite }} personne(s)</p>
                                <p><strong>Prix par nuit :</strong> {{ number_format($reservation->chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Informations client
                                </h5>
                                <p><strong>Nom :</strong> {{ $reservation->client_nom }} {{ $reservation->client_prenom }}</p>
                                <p><strong>Email :</strong> {{ $reservation->client_email }}</p>
                                <p><strong>Téléphone :</strong> {{ $reservation->client_telephone }}</p>
                                <p><strong>Nombre d'invités :</strong> {{ $reservation->nombre_invites }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dates de séjour -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="bg-primary text-white p-3 rounded">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h6><i class="fas fa-calendar-check"></i> Arrivée</h6>
                                        <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</p>
                                        <small>À partir de 14h00</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6><i class="fas fa-calendar-times"></i> Départ</h6>
                                        <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</p>
                                        <small>Avant 12h00</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6><i class="fas fa-moon"></i> Durée</h6>
                                        <p class="mb-0 fw-bold">
                                            {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }} nuit(s)
                                        </p>
                                        <small>{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }} jour(s)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total à payer -->
                    <div class="alert alert-warning text-center">
                        <h4 class="mb-2">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Montant à régler à l'hôtel
                        </h4>
                        <h2 class="text-success mb-0">
                            {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA
                        </h2>
                    </div>

                    <!-- Instructions importantes -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Instructions importantes</h5>
                        <ul class="mb-0">
                            <li><strong>Présentez-vous à la réception</strong> avec ce bon et une pièce d'identité valide</li>
                            <li><strong>Réglement sur place :</strong> Espèces, carte bancaire ou Mobile Money acceptés</li>
                            <li><strong>Arrivée :</strong> Check-in possible à partir de 14h00</li>
                            <li><strong>Départ :</strong> Check-out avant 12h00</li>
                            <li><strong>Modification :</strong> Contactez-nous au +228 71 34 88 88</li>
                        </ul>
                    </div>

                    <!-- Status de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="bg-warning text-dark p-3 rounded text-center">
                                <h5 class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    Statut : EN ATTENTE DE CONFIRMATION
                                </h5>
                                <p class="mb-0">Votre réservation sera confirmée définitivement lors du règlement à l'hôtel</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('reservations.download-bon', $reservation->id) }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-download me-2"></i>
                                    Télécharger en PDF
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button onclick="window.print()" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-print me-2"></i>
                                    Imprimer
                                </button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>

                    <!-- Contact de l'hôtel -->
                    <div class="mt-4 text-center">
                        <hr>
                        <h6 class="text-muted mb-3">Contact Hôtel Le Printemps</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> +228 71 34 88 88
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> +228 96 06 88 88
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="fas fa-envelope"></i> hotelrestaurantleprintemps@yahoo.com
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

@media print {
    body * {
        visibility: hidden;
    }
    .card, .card * {
        visibility: visible;
    }
    .card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        box-shadow: none !important;
    }
    .btn {
        display: none !important;
    }
}
</style>
@endpush

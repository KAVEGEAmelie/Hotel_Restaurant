@extends('layouts.app')
@section('title', 'Confirmation de Réservation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Récapitulatif de votre réservation</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Détails de la chambre</h5>
                            <img src="{{ asset('storage/'.$reservation->chambre->image_principale) }}"
                                 class="img-fluid rounded mb-3" alt="Chambre">
                            <p><strong>{{ $reservation->chambre->nom }}</strong></p>
                            <p>{{ $reservation->chambre->description_courte }}</p>
                        </div>

                        <div class="col-md-6">
                            <h5>Vos informations</h5>
                            <p><strong>Nom:</strong> {{ $reservation->client_nom }} {{ $reservation->client_prenom }}</p>
                            <p><strong>Email:</strong> {{ $reservation->client_email }}</p>
                            <p><strong>Téléphone:</strong> {{ $reservation->client_telephone }}</p>
                            <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</p>
                            <p><strong>Invités:</strong> {{ $reservation->nombre_invites }}</p>
                            <p><strong>Total:</strong> {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h5 class="text-center mb-3">Choisissez votre méthode de paiement</h5>
                        <form action="{{ route('payment.initiate', $reservation->id) }}" method="POST">
                            @csrf
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-mobile-alt me-2"></i> Payer avec Mobile Money
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i> Payer par Carte Bancaire
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

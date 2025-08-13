@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Confirmation de paiement</h5>
                </div>

                <div class="card-body">
                    <p class="lead">Réservation #{{ $reservation->id }} - Chambre {{ $reservation->chambre->numero }}</p>
                    <ul class="list-group mb-4">
                        <li class="list-group-item">Montant: <strong>{{ number_format($reservation->montant_total, 0, ',', ' ') }} XOF</strong></li>
                        <li class="list-group-item">Dates: Du {{ $reservation->date_arrivee }} au {{ $reservation->date_depart }}</li>
                    </ul>

                    <form action="{{ route('payment.initiate', $reservation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-credit-card me-2"></i> Payer maintenant via CinetPay
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <img src="{{ asset('images/cinetpay-methods.png') }}" alt="Méthodes de paiement" class="img-fluid" style="max-width: 300px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

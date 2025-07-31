@extends('layouts.app')
@section('title', 'Réservation Confirmée')
@section('content')
<main id="main">
    <section class="py-5 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                <h1 class="fw-light mt-3">Merci !</h1>
                <p class="lead text-muted">Votre réservation pour la chambre **{{ $reservation->chambre->nom }}** a été confirmée avec succès.</p>
                <p>Un e-mail de confirmation vient de vous être envoyé. Votre numéro de réservation est le **#{{ $reservation->id }}**.</p>
                <p class="mt-4">
                    <a href="{{ route('reservation.receipt', $reservation) }}" class="btn btn-primary my-2">
                        <i class="bi bi-download me-2"></i>Télécharger mon reçu
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-secondary my-2">Retour à l'accueil</a>
                </p>
            </div>
        </div>
    </section>
</main>
@endsection

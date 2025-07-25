@extends('layouts.app')

@section('title', 'Paiement de votre Réservation - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-title-section" style="background-color: #f8f6f0;">
        <div class="container text-center py-5" data-aos="fade-up">
            <h1 class="payment-title">Finalisez votre Réservation</h1>
            <p>Vous y êtes presque ! Vérifiez les détails et procédez au paiement sécurisé.</p>
        </div>
    </section>

    <!-- ======= Section Paiement ======= -->
    <section id="payment-section" class="payment-section">
        <div class="container">

            <div class="row g-5">

                <!-- Colonne de gauche : Récapitulatif de la commande -->
                <div class="col-lg-5 order-lg-2" data-aos="fade-left">
                    <div class="order-summary-card">
                        <h3 class="summary-header">Récapitulatif de votre séjour</h3>
                        <div class="summary-body">
                            <div class="summary-item">
                                <span>Chambre</span>
                                <strong>{{ $reservation->chambre->nom }}</strong>
                            </div>
                            <hr>
                            <div class="summary-item">
                                <span>Arrivée</span>
                                <strong>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</strong>
                            </div>
                            <div class="summary-item">
                                <span>Départ</span>
                                <strong>{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</strong>
                            </div>
                            <div class="summary-item">
                                <span>Invités</span>
                                <strong>{{ $reservation->nombre_invites }} personne(s)</strong>
                            </div>
                            <hr>
                            <div class="summary-total">
                                <span>Montant Total</span>
                                <strong class="total-price">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite : Formulaire de paiement -->
                <div class="col-lg-7 order-lg-1" data-aos="fade-right">
                    <div class="payment-form-card">
    <h4><i class="bi bi-shield-check"></i> Paiement 100% Sécurisé</h4>
    <p>Vous allez être redirigé vers la plateforme sécurisée de notre partenaire CashPay pour finaliser votre paiement.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('payment.process', $reservation) }}" method="POST">
        @csrf
        <div class="alert alert-info">
            En cliquant sur "Procéder au paiement", vous confirmez les détails de votre séjour.
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-brand btn-lg">
                Procéder au paiement de {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA
            </button>
        </div>
    </form>
</div>
                </div>

            </div>
        </div>
    </section>

</main>

@endsection

@push('styles')
<style>
    .payment-title { font-family: 'Playfair Display', serif; }

    /* Carte de récapitulatif */
    .order-summary-card {
        background: #f8f6f0;
        border: 1px solid #e9e9e9;
        border-radius: 10px;
        position: sticky; top: 120px;
    }
    .summary-header {
        padding: 20px;
        font-size: 18px;
        font-weight: 700;
        border-bottom: 1px solid #e9e9e9;
    }
    .summary-body { padding: 20px; }
    .summary-item, .summary-total {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 16px;
    }
    .summary-item span, .summary-total span { color: #777; }
    .summary-item strong, .summary-total strong { color: var(--color-texte-principal); }
    .summary-total {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--color-marron-titre);
    }
    .summary-total .total-price {
        font-size: 24px;
        color: var(--color-vert-foret);
    }

    /* Carte de formulaire de paiement */
    .payment-form-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    }
    .payment-form-card h4 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .payment-form-card h4 i { color: var(--color-vert-foret); }
    .payment-form-card p { margin-bottom: 25px; }
    .payment-methods .form-check { margin-bottom: 10px; }
</style>
@endpush

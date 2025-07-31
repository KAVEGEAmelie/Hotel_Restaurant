@extends('layouts.app')

@section('title', 'Paiement de votre Réservation - Hôtel Le Printemps')

@section('content')

<main id="main">

    <section id="payment-page" class="payment-page-section">
        <div class="container">
            <div class="row g-5 justify-content-center">

                <!-- Colonne de gauche : Formulaire de paiement -->
                <div class="col-lg-7" data-aos="fade-up">
                    <div class="payment-header">
                        <h1 class="payment-title">Finalisez votre Réservation</h1>
                        <p class="lead text-muted">Vous y êtes presque ! Confirmez et procédez au paiement sécurisé.</p>
                    </div>

                    <div class="payment-form-wrapper">
                        <div class="payment-provider-info">
                            {{-- TODO: Ajouter le logo de CashPay dans public/assets/img/ --}}
                            {{-- <img src="{{ asset('assets/img/cashpay-logo.png') }}" alt="CashPay Logo" class="provider-logo"> --}}
                            <i class="bi bi-credit-card-2-front-fill provider-icon"></i>
                            <p>Vous allez être redirigé vers la plateforme sécurisée de notre partenaire de paiement pour finaliser votre transaction par carte ou Mobile Money.</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('payment.process', $reservation) }}" method="POST">
                            @csrf

                            <!-- Choix de la méthode de paiement -->
                            <div class="payment-method-selection mb-4">
                                <h5 class="mb-3">Choisissez votre méthode de paiement</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="payment-method-option">
                                            <input type="radio" id="card" name="payment_method" value="card" class="payment-method-input" checked>
                                            <label for="card" class="payment-method-label">
                                                <i class="bi bi-credit-card-fill"></i>
                                                <span>Carte Bancaire</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="payment-method-option">
                                            <input type="radio" id="mobile_money" name="payment_method" value="mobile_money" class="payment-method-input">
                                            <label for="mobile_money" class="payment-method-label">
                                                <i class="bi bi-phone-fill"></i>
                                                <span>Mobile Money</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @error('payment_method')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="payment-notice">
                                <i class="bi bi-info-circle-fill"></i>
                                En cliquant sur "Procéder au paiement", vous acceptez nos <a href="#">conditions de réservation</a>.
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-brand btn-lg">
                                    Payer {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Colonne de droite : Récapitulatif de la commande -->
                <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
                    <div class="order-summary-card-v2">
                        <div class="summary-header">
                            <img src="{{ asset('uploads/' . $reservation->chambre->image_principale) }}" alt="{{ $reservation->chambre->nom }}">
                            <div class="header-text">
                                <h5>{{ $reservation->chambre->nom }}</h5>
                                <p>Réservation pour {{ $reservation->client_prenom }} {{ $reservation->client_nom }}</p>
                            </div>
                        </div>
                        <div class="summary-body">
                            <ul>
                                <li>
                                    <span>Arrivée</span>
                                    <strong>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</strong>
                                </li>
                                <li>
                                    <span>Départ</span>
                                    <strong>{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</strong>
                                </li>
                                <li>
                                    <span>Durée du séjour</span>
                                    <strong>{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} Nuit(s)</strong>
                                </li>
                                <li>
                                    <span>Invités</span>
                                    <strong>{{ $reservation->nombre_invites }} personne(s)</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="summary-footer">
                            <span>Montant Total à Payer</span>
                            <strong class="total-price">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

@endsection

@push('styles')
<style>
    .payment-page-section { padding: 80px 0; background: #f8f6f0; }
    .payment-title { font-family: 'Playfair Display', serif; font-size: 42px; }

    /* Formulaire de paiement */
    .payment-form-wrapper { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.07); }
    .payment-provider-info { display: flex; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px; }
    .provider-icon { font-size: 32px; color: var(--color-marron-titre); margin-right: 15px; }
    .payment-notice { background: #e9f7ef; color: #1e6643; padding: 15px; border-radius: 8px; display: flex; align-items: center; font-size: 14px; }
    .payment-notice i { font-size: 20px; margin-right: 10px; }
    .payment-notice a { color: #1e6643; font-weight: 600; text-decoration: underline; }

    /* Sélection de méthode de paiement */
    .payment-method-selection h5 { color: #333; font-weight: 600; }
    .payment-method-option { position: relative; }
    .payment-method-input { display: none; }
    .payment-method-label {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }
    .payment-method-label:hover { border-color: var(--color-vert-foret); }
    .payment-method-label i {
        font-size: 24px;
        margin-right: 12px;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    .payment-method-label span {
        font-weight: 500;
        color: #495057;
        transition: color 0.3s ease;
    }
    .payment-method-input:checked + .payment-method-label {
        border-color: var(--color-vert-foret);
        background: #f8f9fa;
    }
    .payment-method-input:checked + .payment-method-label i { color: var(--color-vert-foret); }
    .payment-method-input:checked + .payment-method-label span { color: var(--color-vert-foret); font-weight: 600; }

    /* Carte de récapitulatif v2 */
    .order-summary-card-v2 { background: var(--color-blanc); border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: sticky; top: 120px; }
    .summary-header { display: flex; align-items: center; padding: 20px; border-bottom: 1px solid #eee; }
    .summary-header img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
    .summary-header h5 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 2px; }
    .summary-header p { font-size: 14px; color: #777; margin-bottom: 0; }
    .summary-body ul { list-style: none; padding: 20px; margin: 0; }
    .summary-body li { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f5f5f5; }
    .summary-body li:last-child { border-bottom: none; }
    .summary-body span { color: #777; }
    .summary-body strong { font-weight: 600; }
    .summary-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #f8f6f0; border-radius: 0 0 10px 10px; }
    .summary-footer span { font-weight: 700; font-size: 18px; }
    .summary-footer .total-price { font-size: 24px; color: var(--color-vert-foret); font-weight: 700; }
</style>
@endpush

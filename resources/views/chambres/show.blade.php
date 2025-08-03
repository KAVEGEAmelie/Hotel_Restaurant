@extends('layouts.app')
@section('title', $chambre->nom . ' - Hôtel Le Printemps')

@section('content')
<main id="main">
    <section id="room-details" class="room-details-section-final">
        <div class="container">
            <div class="room-page-header" data-aos="fade-up">
                <h1>{{ $chambre->nom }}</h1>
                <p class="lead">{{ $chambre->description_courte }}</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="room-main-content" data-aos="fade-up">
                        <div class="room-gallery-final mb-4">
                            <img src="{{ asset('storage/' . $chambre->image_principale) }}" class="img-fluid" alt="Image de la chambre {{ $chambre->nom }}">
                        </div>
                        <div class="room-description-final">
                            <h3 class="section-heading">Description de la chambre</h3>
                            <div class="text-content">{!! nl2br(e($chambre->description_longue)) !!}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="booking-sidebar-final" data-aos="fade-left" data-aos-delay="200">
                        <div class="booking-card">
                            <div class="price-header">
                                <div class="price">{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</div>
                                <span>par nuit</span>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger mx-4 mt-3">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger mx-4 mt-3">
                                    <ul class="mb-0 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

<!-- Dans la colonne de droite "Formulaire de réservation" -->
<form action="{{ route('reservation.create') }}" method="POST" class="p-4">
    @csrf
    <input type="hidden" name="chambre_id" value="{{ $chambre->id }}">

    <h4 class="form-title">Réservez vos dates</h4>
    <div class="row g-3">
        <div class="col-12">
            <label for="check_in_date" class="form-label">Arrivée</label>
            <input type="date" name="check_in_date" id="check_in_date" class="form-control" required>
        </div>
        <div class="col-12">
            <label for="check_out_date" class="form-label">Départ</label>
            <input type="date" name="check_out_date" id="check_out_date" class="form-control" required>
        </div>
    </div>

    <hr class="my-4">

    <h4 class="form-title">Vos informations</h4>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="client_nom" class="form-label">Nom</label>
            <input type="text" name="client_nom" id="client_nom" class="form-control" placeholder="Votre nom" required>
        </div>
        <div class="col-md-6">
            <label for="client_prenom" class="form-label">Prénom</label>
            <input type="text" name="client_prenom" id="client_prenom" class="form-control" placeholder="Votre prénom" required>
        </div>
        <div class="col-12">
            <label for="client_email" class="form-label">Email</label>
            <input type="email" name="client_email" id="client_email" class="form-control" placeholder="Votre email" required>
        </div>
        <div class="col-12">
            <label for="client_telephone" class="form-label">Téléphone</label>
            <input type="tel" name="client_telephone" id="client_telephone" class="form-control" placeholder="Votre téléphone" required>
        </div>
        <div class="col-12">
            <label for="nombre_invites" class="form-label">Nombre de personnes (max: {{ $chambre->capacite }})</label>
            <select name="nombre_invites" id="nombre_invites" class="form-select">
                @for ($i = 1; $i <= $chambre->capacite; $i++)
                    <option value="{{ $i }}">{{ $i }} personne(s)</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-brand btn-lg">Procéder au Paiement</button>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
{{-- Styles spécifiques pour le nouveau design final --}}
<style>
    .room-details-section-final { padding: 80px 0; }
    .room-page-header { text-align: center; margin-bottom: 60px; }
    .room-page-header h1 { font-family: 'Playfair Display', serif; font-size: 52px; color: var(--color-marron-titre); }
    .room-page-header .lead { font-size: 20px; color: #777; max-width: 600px; margin: 10px auto 0 auto; }

    /* === NOUVEAU STYLE POUR L'IMAGE === */
    .room-gallery-final img {
        border-radius: 12px; /* Coins arrondis */
        box-shadow: 0 15px 35px -10px rgba(0,0,0,0.25);
        width: 100%; /* L'image prend toute la largeur de sa colonne */
        aspect-ratio: 16 / 10; /* Contrôle la hauteur de l'image */
        object-fit: cover;
    }

    .room-description-final .section-heading { font-size: 24px; margin-bottom: 15px; }
    .room-description-final .text-content { font-size: 16px; line-height: 1.8; color: #555; }

    /* === Colonne de droite (Formulaire) === */
    .booking-sidebar-final { position: sticky; top: 120px; }
    .booking-card { background: var(--color-blanc); border-radius: 10px; border: 1px solid #e9e9e9; box-shadow: 0 5px 20px rgba(0,0,0,0.07); }
    .price-header { padding: 20px; text-align: right; }
    .price-header .price { font-size: 28px; font-weight: 700; color: var(--color-vert-foret); }
    .price-header span { font-size: 16px; color: #777; }
    .booking-card form { border-top: 1px solid #e9e9e9; }
    .form-title { font-size: 18px; font-weight: 700; margin-bottom: 15px; }
    .booking-card .form-control, .booking-card .form-select { padding: 12px; font-size: 16px; }
</style>
@endpush

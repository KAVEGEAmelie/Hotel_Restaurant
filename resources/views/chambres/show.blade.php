@extends('layouts.app')
@section('title', $chambre->nom . ' - Hôtel Le Printemps')

@section('content')
<main id="main">

    <!-- Alerte si la chambre est indisponible -->
    @if(!$chambre->est_disponible)
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert" style="border-radius: 0;">
            <div class="container">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading mb-1">⚠️ Chambre actuellement indisponible</h4>
                        <p class="mb-0">
                            Cette chambre n'est pas disponible à la réservation pour le moment.
                            Contactez-nous au <strong>+228 71 34 88 88</strong> pour plus d'informations.
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <section id="room-details" class="room-details-section-final">
        <div class="container">
            <div class="room-page-header" data-aos="fade-up">
                <h1>{{ $chambre->nom }}</h1>
                <p class="lead">{{ $chambre->description_courte }}</p>

                <!-- Badge de statut sous le titre -->
                <div class="mt-3">
                    @if($chambre->est_disponible)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="bi bi-check-circle"></i> Disponible pour réservation
                        </span>
                    @else
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="bi bi-lock-fill"></i> Temporairement indisponible
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="room-main-content" data-aos="fade-up">
                        <div class="room-gallery-final mb-4 position-relative">
                            <img src="{{ asset('storage/' . $chambre->image_principale) }}"
                                 class="img-fluid {{ !$chambre->est_disponible ? 'img-unavailable' : '' }}"
                                 alt="Image de la chambre {{ $chambre->nom }}">

                            <!-- Overlay indisponible sur l'image -->
                            @if(!$chambre->est_disponible)
                                <div class="image-unavailable-overlay">
                                    <div class="unavailable-message">
                                        <i class="bi bi-lock-fill"></i>
                                        <span>INDISPONIBLE</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="room-description-final {{ !$chambre->est_disponible ? 'content-unavailable' : '' }}">
                            <h3 class="section-heading">Description de la chambre</h3>
                            <div class="text-content">{!! nl2br(e($chambre->description_longue)) !!}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="booking-sidebar-final" data-aos="fade-left" data-aos-delay="200">
                        <div class="booking-card {{ !$chambre->est_disponible ? 'booking-card-unavailable' : '' }}">
                            <div class="price-header {{ !$chambre->est_disponible ? 'price-unavailable' : '' }}">
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

                            @if($chambre->est_disponible)
                                <!-- Formulaire de réservation normal -->
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
                            @else
                                <!-- Section indisponible -->
                                <div class="p-4 text-center">
                                    <div class="unavailable-section">
                                        <i class="bi bi-lock-fill text-danger" style="font-size: 3rem;"></i>
                                        <h4 class="mt-3 text-danger">Réservation indisponible</h4>
                                        <p class="text-muted">
                                            Cette chambre est temporairement fermée à la réservation.
                                        </p>

                                        <!-- Contacts -->
                                        <div class="contact-section mt-4">
                                            <h5>Contactez-nous pour plus d'infos</h5>
                                            <div class="d-grid gap-2 mt-3">
                                                <a href="tel:+22871348888" class="btn btn-outline-primary">
                                                    <i class="bi bi-telephone-fill"></i> +228 71 34 88 88
                                                </a>
                                                <a href="tel:+22896068888" class="btn btn-outline-primary">
                                                    <i class="bi bi-telephone-fill"></i> +228 96 06 88 88
                                                </a>
                                                <a href="mailto:hotelrestaurantleprintemps@yahoo.com" class="btn btn-outline-info">
                                                    <i class="bi bi-envelope-fill"></i> Email
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Suggestions alternatives -->
                                        @php
                                            $autresChambres = App\Models\Chambre::where('est_disponible', true)
                                                                                ->where('id', '!=', $chambre->id)
                                                                                ->take(3)
                                                                                ->get();
                                        @endphp

                                        @if($autresChambres->count() > 0)
                                            <div class="alternatives-section mt-4">
                                                <h5>Autres chambres disponibles</h5>
                                                <div class="list-group list-group-flush mt-3">
                                                    @foreach($autresChambres as $autreChambre)
                                                        <a href="{{ route('chambres.show', $autreChambre->slug) }}"
                                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $autreChambre->nom }}</strong><br>
                                                                <small class="text-muted">{{ Str::limit($autreChambre->description_courte, 50) }}</small>
                                                            </div>
                                                            <span class="badge bg-success rounded-pill">
                                                                {{ number_format($autreChambre->prix_par_nuit, 0, ',', ' ') }} F
                                                            </span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
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
.room-details-section-final { padding: 80px 0; }
.room-page-header { text-align: center; margin-bottom: 60px; }
.room-page-header h1 { font-family: 'Playfair Display', serif; font-size: 52px; color: var(--color-marron-titre); }
.room-page-header .lead { font-size: 20px; color: #777; max-width: 600px; margin: 10px auto 0 auto; }

/* === STYLES POUR L'INDISPONIBILITÉ === */
.img-unavailable {
    opacity: 0.6;
    filter: grayscale(30%);
}
.image-unavailable-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 15px 25px;
    border-radius: 25px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}
.unavailable-message i {
    margin-right: 8px;
    font-size: 1.2em;
}
.content-unavailable {
    opacity: 0.7;
}
.booking-card-unavailable {
    border: 2px solid #dc3545;
    background: #fff5f5;
}
.price-unavailable .price {
    color: #6c757d;
    text-decoration: line-through;
}

/* === IMAGE NORMALE === */
.room-gallery-final img {
    border-radius: 12px;
    box-shadow: 0 15px 35px -10px rgba(0,0,0,0.25);
    width: 100%;
    aspect-ratio: 16 / 10;
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

/* === Section indisponible === */
.unavailable-section i {
    opacity: 0.8;
}
.contact-section h5,
.alternatives-section h5 {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
}
</style>
@endpush

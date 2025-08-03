@php
    $makeHeaderTransparent = true;
@endphp

@extends('layouts.app')
@section('title', 'Nos Chambres & Suites - Hôtel Le Printemps')

@section('content')
<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-header-section" style="background-image: url('{{ asset('assets/img/chambres-bg.jpg') }}');">
        <div class="container text-center" data-aos="fade-up">
            <h1>Nos Chambres & Suites</h1>
            <p class="text-white-50">Trouvez l'espace parfait pour un séjour inoubliable</p>
        </div>
    </section>

    <!-- ======= Section Formulaire de Réservation ======= -->
    <section class="booking-form-section" style="background-color: #f8f6f0; padding: 50px 0;">
        <div class="container">
            <div class="booking-form-wrapper" data-aos="fade-up">
                <form action="{{ route('chambres.index') }}" method="GET">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group-v2">
                                <i class="bi bi-calendar-check icon"></i>
                                <div class="input-wrapper">
                                    <label for="checkin_date">Arrivée</label>
                                    <input type="text" class="form-control" id="checkin_date" name="check_in_date" value="{{ request('check_in_date') }}" placeholder="Choisissez une date" onfocus="(this.type='date')" onblur="(this.type='text')">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group-v2">
                                <i class="bi bi-calendar-x icon"></i>
                                <div class="input-wrapper">
                                    <label for="checkout_date">Départ</label>
                                    <input type="text" class="form-control" id="checkout_date" name="check_out_date" value="{{ request('check_out_date') }}" placeholder="Choisissez une date" onfocus="(this.type='date')" onblur="(this.type='text')">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group-v2">
                                <i class="bi bi-people icon"></i>
                                <div class="input-wrapper">
                                    <label for="guests">Personnes</label>
                                    <select class="form-select" id="guests" name="guests">
                                        <option value="1" @selected(request('guests') == 1)>1 Personne</option>
                                        <option value="2" @selected(request('guests', 2) == 2)>2 Personnes</option>
                                        <option value="3" @selected(request('guests') == 3)>3 Personnes</option>
                                        <option value="4" @selected(request('guests') == 4)>4 Personnes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 d-grid">
                            <button type="submit" class="btn-search">Vérifier la disponibilité</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- ======= Section Liste des Chambres ======= -->
    <section id="chambres-list" class="chambres-list-section py-5">
        <div class="container">
            <div class="row g-4">
                @forelse($chambres as $chambre)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        <div class="room-card-compact">
                            <div class="room-card-image-wrapper">
                                <img src="{{ asset('storage/' . $chambre->image_principale) }}" class="img-fluid" alt="{{ $chambre->nom }}">
                                <div class="price-badge">{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} F / nuit</div>
                            </div>
                            <div class="room-card-body">
                                <h3 class="room-title">{{ $chambre->nom }}</h3>
                                <p class="room-description">{{ Str::limit($chambre->description_courte, 90) }}</p>
                                <a href="{{ route('chambres.show', ['chambre' => $chambre, 'check_in_date' => request('check_in_date'), 'check_out_date' => request('check_out_date'), 'guests' => request('guests')]) }}" class="btn-details">
                                    Détails & Réservation <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-warning">
                            <h4>Aucune chambre disponible</h4>
                            <p class="lead">Désolé, aucune chambre ne correspond à vos critères de recherche. Veuillez essayer d'autres dates.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
/*--------------------------------------------------------------
# Page Chambres - Formulaire de Recherche et Cartes
--------------------------------------------------------------*/

.booking-form-wrapper {
    background: var(--color-blanc);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.form-group-v2 {
    display: flex;
    align-items: center;
    border-right: 1px solid #eee;
    padding: 10px 20px;
}
.form-group-v2 .icon {
    font-size: 24px;
    color: var(--color-dore-leger);
    margin-right: 15px;
}
.form-group-v2 .input-wrapper {
    display: flex;
    flex-direction: column;
}
.form-group-v2 label {
    font-weight: 600;
    font-size: 14px;
    color: #888;
    margin-bottom: 2px;
}
.form-group-v2 .form-control,
.form-group-v2 .form-select {
    border: none;
    box-shadow: none;
    padding: 0;
    background: transparent;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-marron-titre);
}
.form-group-v2 input::placeholder {
    color: var(--color-marron-titre);
    opacity: 0.8;
}
.btn-search {
    background: var(--color-vert-foret);
    color: var(--color-blanc);
    border: none;
    padding: 25px 30px;
    font-size: 16px;
    font-weight: 700;
    width: 100%;
    transition: background-color 0.3s ease;
    border-radius: 0 8px 8px 0;
}
.btn-search:hover {
    background-color: var(--color-vert-fonce-lien);
}
.room-card-compact {
    background: var(--color-blanc);
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.room-card-compact:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
}
.room-card-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 10px 10px 0 0;
}
.room-card-image-wrapper img {
    height: 220px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.room-card-compact:hover .room-card-image-wrapper img {
    transform: scale(1.1);
}
.price-badge {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(34, 139, 34, 0.9);
    color: var(--color-blanc);
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    backdrop-filter: blur(5px);
}
.room-card-body {
    padding: 25px;
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}
.room-card-body .room-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    margin-bottom: 10px;
}
.room-card-body .room-description {
    font-size: 15px;
    color: #555;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
}
.room-card-body .btn-details {
    color: var(--color-vert-fonce-lien);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s;
}
.room-card-body .btn-details:hover {
    color: var(--color-vert-foret);
}
.room-card-body .btn-details i {
    transition: transform 0.3s;
    vertical-align: middle;
}
.room-card-body .btn-details:hover i {
    transform: translateX(5px);
}
@media (max-width: 991.98px) {
    .form-group-v2 {
        border-right: none;
        border-bottom: 1px solid #eee;
    }
    .booking-form-wrapper .row > div:last-child .form-group-v2 {
        border-bottom: none;
    }
    .btn-search {
        border-radius: 0 0 8px 8px;
    }
}
</style>
@endpush

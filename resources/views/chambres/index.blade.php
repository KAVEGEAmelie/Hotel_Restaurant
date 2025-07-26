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
                                        <input type="text" class="form-control" id="checkin_date" name="checkin_date" value="{{ request('checkin_date') }}" placeholder="Choisissez une date" onfocus="(this.type='date')" onblur="(this.type='text')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group-v2">
                                    <i class="bi bi-calendar-x icon"></i>
                                    <div class="input-wrapper">
                                        <label for="checkout_date">Départ</label>
                                        <input type="text" class="form-control" id="checkout_date" name="checkout_date" value="{{ request('checkout_date') }}" placeholder="Choisissez une date" onfocus="(this.type='date')" onblur="(this.type='text')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group-v2">
                                    <i class="bi bi-people icon"></i>
                                    <div class="input-wrapper">
                                        <label for="guests">Personnes</label>
                                        <select class="form-select" id="guests" name="guests">
                                            <option value="1" {{ request('guests') == 1 ? 'selected' : '' }}>1 Personne</option>
                                            <option value="2" {{ request('guests', 2) == 2 ? 'selected' : '' }}>2 Personnes</option>
                                            <option value="3" {{ request('guests') == 3 ? 'selected' : '' }}>3 Personnes</option>
                                            <option value="4" {{ request('guests') == 4 ? 'selected' : '' }}>4 Personnes</option>
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
        </section><!-- Fin Formulaire de Réservation -->

        <!-- ======= Section Liste des Chambres (Design Vertical Compact) ======= -->
        <section id="chambres-list" class="chambres-list-section py-5">
            <div class="container">
                <div class="row g-4">

                    @forelse($chambres as $chambre)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                            <div class="room-card-compact">
                                <div class="room-card-image-wrapper">
                                    <img src="{{ asset('uploads/' . $chambre->image_principale) }}" class="img-fluid" alt="{{ $chambre->nom }}">
                                    <div class="price-badge">{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} F / nuit</div>
                                </div>
                                <div class="room-card-body">
                                    <h3 class="room-title">{{ $chambre->nom }}</h3>
                                    <p class="room-description">{{ Str::limit($chambre->description_courte, 90) }}</p>
                                    <a href="{{ route('chambres.show', ['chambre' => $chambre, 'checkin_date' => request('checkin_date'), 'checkout_date' => request('checkout_date'), 'guests' => request('guests')]) }}"
                                       class="btn-details">
                                       Détails & Réservation <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-warning">
                                <h4>Aucune chambre disponible</h4>
                                <p class="lead">Désolé, aucune chambre ne correspond à vos critères de recherche pour le moment. Veuillez essayer d'autres dates.</p>
                            </div>
                        </div>
                    @endforelse

                </div>
            </div>
        </section>

    </main>
@endsection

@extends('layouts.app')

@section('title', 'Nos Chambres & Suites - Hôtel Le Printemps')

@section('content')

    <main id="main">

        <!-- ======= Section Titre de Page ======= -->
        <section class="page-title-section" style="background-image: url('{{ asset('assets/img/chambres-bg.jpg') }}');">
            <div class="container text-center" data-aos="fade-up">
                <h1>Nos Chambres & Suites</h1>
                <p class="text-white-50">Trouvez l'espace parfait pour un séjour inoubliable</p>
            </div>
        </section><!-- Fin Section Titre de Page -->

        <!-- ======= Section Formulaire de Réservation ======= -->
        <section class="booking-form-section" style="background-color: #fdfaf6; padding: 40px 0;">
            <div class="container">
                <form action="{{ route('chambres.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="checkin_date" class="form-label fw-bold">Arrivée</label>
                            <input type="date" class="form-control" id="checkin_date" name="checkin_date" value="{{ request('checkin_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="checkout_date" class="form-label fw-bold">Départ</label>
                            <input type="date" class="form-control" id="checkout_date" name="checkout_date" value="{{ request('checkout_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="guests" class="form-label fw-bold">Personnes</label>
                            <input type="number" class="form-control" id="guests" name="guests" min="1" value="{{ request('guests', 1) }}">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-brand">Rechercher</button>
                        </div>
                    </div>
                </form>
            </div>
        </section><!-- Fin Formulaire de Réservation -->

        <!-- ======= Section Liste des Chambres ======= -->
        <section id="features" class="features py-5">
            <div class="container">
                <div class="row g-4">
                    {{-- On boucle sur les chambres envoyées par le contrôleur --}}
                    @forelse($chambres as $chambre)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            <div class="card card-brand h-100">
                                {{-- TODO: Remplacer par la vraie image de la chambre --}}
                                <img src="https://via.placeholder.com/400x250.png/228B22/FFFFFF?text={{ urlencode($chambre->nom) }}" class="card-img-top" alt="{{ $chambre->nom }}" style="height: 250px; object-fit: cover;">
                                <div class="card-body text-center d-flex flex-column">
                                    <h5 class="card-title">{{ $chambre->nom }}</h5>
                                    <p class="card-text">{{ $chambre->description_courte }}</p>
                                    <div class="my-3">
                                        <strong>{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</strong> / nuit
                                    </div>
                                    <a href="#" class="btn btn-outline-custom mt-auto align-self-center">Détails & Réservation →</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="lead">Désolé, aucune chambre n'est disponible pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section><!-- Fin Liste des Chambres -->

    </main>
@endsection

@push('styles')
    {{-- On ajoute un peu de style pour le titre de la page --}}
    <style>
        .page-title-section {
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            position: relative;
            color: white;
        }
        .page-title-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .page-title-section .container {
            position: relative;
            z-index: 1;
        }
    </style>
@endpush
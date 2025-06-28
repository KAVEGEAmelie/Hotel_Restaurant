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

        <!-- ======= Section Formulaire de Réservation (Nouveau Design) ======= -->
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
<!-- ======= Section Liste des Chambres ======= -->
<section id="chambres-list" class="chambres-list-section py-5">
    <div class="container">
        <div class="row g-4">

            {{-- On boucle sur les chambres envoyées par le contrôleur --}}
            @forelse($chambres as $chambre)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                    <div class="card card-brand h-100">
                        {{-- Assurez-vous d'avoir une colonne 'image_principale' dans votre table chambres --}}
                        <img src="{{ asset('storage/' . $chambre->image_principale) }}" class="card-img-top" alt="{{ $chambre->nom }}" style="height: 250px; object-fit: cover;">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title">{{ $chambre->nom }}</h5>
                            <p class="card-text">{{ $chambre->description_courte }}</p>
                            <div class="my-3">
                                <strong>{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</strong> / nuit
                            </div>
                            
                            {{-- LE VOICI : Le bouton "Détails & Réservation" --}}
                            <a href="{{ route('chambres.show', ['chambre' => $chambre, 'checkin_date' => request('checkin_date'), 'checkout_date' => request('checkout_date'), 'guests' => request('guests')]) }}" 
                               class="btn btn-outline-custom mt-auto align-self-center">
                               Détails & Réservation →
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
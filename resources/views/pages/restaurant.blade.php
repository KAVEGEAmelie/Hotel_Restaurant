@php
    $makeHeaderTransparent = true;
@endphp

@extends('layouts.app')
@section('title', 'Restaurant & Ambiance - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-header-section" style="background-image: url('{{ asset('assets/img/restaurant-bg.jpg') }}');">
    <div class="container text-center" data-aos="fade-up">
        <h1>Notre Restaurant</h1>
        <p class="text-white-50">Une invitation au voyage des saveurs</p>
    </div>
</section>

    <!-- ======= Section Ambiance (Nouveau Design) ======= -->
    <section id="ambiance" class="ambiance-section-v2 py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="ambiance-image-wrapper">
                        <img src="{{ asset('assets/img/salle-interieure.jpg') }}" class="img-main" alt="Salle de restaurant principale">
                        <img src="{{ asset('assets/img/bar-ambiance.jpg') }}" class="img-secondary" alt="Ambiance chaleureuse du bar">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="section-title text-start">
                        <h2>L'Ambiance</h2>
                        <p>Chaleur & Convivialité</p>
                    </div>
                    <p class="fst-italic">
                        Notre salle est un espace de partage où le bois noble rencontre un design moderne et confortable.
                    </p>
                    <p>
                        Que ce soit pour un dîner romantique, un repas d'affaires ou un moment en famille, notre cadre chaleureux et notre atmosphère "Zen" sont la promesse d'une expérience mémorable.
                    </p>
                    <div class="horaires mt-4">
                        <i class="bi bi-clock-history"></i>
                        <div>
                            <strong>Horaires d'ouverture</strong>
                            <span>12h00 - 15h00  |  19h00 - 22h00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Section Télécharger le Menu PDF ======= -->
    @if($menuActif)
    <section id="menu-download" class="menu-download-section">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="text-white">{{ $menuActif->titre }}</h2>
            <p class="text-white-50 mx-auto" style="max-width: 600px;">
                Consultez notre carte complète pour découvrir l'ensemble de nos créations, des sandwichs gourmands aux assiettes généreuses.
            </p>
            <a href="{{ asset('storage/' . $menuActif->fichier_pdf) }}" download class="btn-download mt-3">
                <i class="bi bi-file-earmark-pdf-fill"></i> Télécharger le Menu
            </a>
        </div>
    </section>
    @else
    {{-- On peut afficher un message si aucun menu PDF n'est uploadé --}}
    <section class="py-5 bg-light">
        <div class="container text-center">
            <p class="lead">Le menu détaillé sera bientôt disponible.</p>
        </div>
    </section>
    @endif

</main>
@endsection


@push('styles')
{{-- Styles spécifiques pour cette nouvelle page Restaurant --}}
<style>
    /* ======= Section Ambiance (Nouveau Design) ======= */
    .ambiance-section-v2 {
        background: #fdfaf6;
    }
    .ambiance-image-wrapper {
        position: relative;
        height: 450px;
    }
    .ambiance-image-wrapper .img-main {
        width: 80%;
        height: 80%;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        position: relative;
        z-index: 2;
        transition: transform 0.5s ease;
    }
    .ambiance-image-wrapper .img-secondary {
        width: 50%;
        height: 50%;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border: 5px solid white;
        position: absolute;
        bottom: 0;
        right: 0;
        z-index: 3;
        transition: all 0.5s ease;
    }
    .ambiance-image-wrapper:hover .img-main {
        transform: scale(1.05);
    }
    .ambiance-image-wrapper:hover .img-secondary {
        transform: scale(1.1) rotate(3deg);
    }
    @media (max-width: 991px) {
        .ambiance-image-wrapper {
            height: auto; /* Hauteur auto sur mobile */
        }
        .ambiance-image-wrapper .img-main,
        .ambiance-image-wrapper .img-secondary {
            position: relative;
            width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
        .ambiance-image-wrapper .img-secondary {
            display: none; /* On cache la 2eme image sur mobile pour plus de clarté */
        }
    }

    .ambiance-section-v2 .horaires {
        display: flex;
        align-items: center;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .ambiance-section-v2 .horaires i {
        font-size: 32px;
        color: var(--color-vert-foret);
        margin-right: 15px;
    }
    .ambiance-section-v2 .horaires div {
        display: flex;
        flex-direction: column;
    }
    .ambiance-section-v2 .horaires strong {
        font-size: 16px;
        font-family: 'Montserrat', sans-serif;
    }
    .ambiance-section-v2 .horaires span {
        font-size: 15px;
        color: #555;
    }

    /* ======= Section Téléchargement PDF ======= */
    .menu-download-section {
        background: linear-gradient(rgba(40,40,40,0.8), rgba(40,40,40,0.8)), url('{{ asset('assets/img/cta-bg.jpg') }}') center center fixed;
        background-size: cover;
        padding: 100px 0;
    }
    .btn-download {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 1px;
        display: inline-block;
        padding: 12px 40px;
        border-radius: 50px;
        transition: 0.5s;
        margin: 10px;
        border: 2px solid var(--color-dore-leger);
        color: var(--color-blanc);
        text-decoration: none;
    }
    .btn-download:hover {
        background: var(--color-dore-leger);
        color: var(--color-marron-titre);
    }
    .btn-download i {
        margin-right: 8px;
        font-size: 20px;
        vertical-align: middle;
    }
</style>
@endpush
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

    <!-- ======= Section Menu & Spécialités (Nouveau Design) ======= -->
    <section id="menu" class="menu-section-v3 py-5" style="background-color: #fcfbf7;">
        <div class="container">
            <!-- Titre et téléchargement du PDF -->
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 text-center">
                    <div class="section-title">
                        <h2>Notre Carte</h2>
                        <p>Une Cuisine Authentique & Raffinée</p>
                    </div>
                    <p class="mb-4">Découvrez ci-dessous un aperçu de nos plats signatures, ou téléchargez notre menu complet pour explorer l'étendue de notre offre culinaire.</p>
                    @if($menuActif)
                        <a href="{{ asset('uploads/' . $menuActif->fichier) }}" download class="btn-download-v3">
                            <i class="bi bi-download me-2"></i> Télécharger le Menu Complet
                        </a>
                    @endif
                </div>
            </div>

            <!-- Galerie de plats interactive -->
            @if($platsGalerie->isNotEmpty())
                <div class="row gy-4 mt-5">
                    @foreach($platsGalerie as $plat)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="plat-gallery-item" data-bs-toggle="modal" data-bs-target="#platModal"
                                 data-nom="{{ $plat->nom }}"
                                 data-prix="{{ number_format($plat->prix, 0, ',', ' ') }} FCFA"
                                 data-description="{{ $plat->description }}"
                                 data-image="{{ asset('uploads/' . $plat->image) }}">
                                <img src="{{ asset('uploads/' . $plat->image) }}" alt="{{ $plat->nom }}" class="img-fluid">
                                <div class="plat-gallery-overlay">
                                    <div class="overlay-content">
                                        <i class="bi bi-eye-fill"></i>
                                        <span>Voir Détails</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</main>

<!-- ======= Fenêtre Modale pour les Plats ======= -->
<div class="modal fade" id="platModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <img id="modalPlatImage" src="" class="img-fluid modal-image" alt="Image du plat">
                    </div>
                    <div class="col-lg-6 d-flex flex-column">
                        <div class="p-4">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal"></button>
                            <h3 id="modalPlatNom" class="modal-title"></h3>
                            <p id="modalPlatDescription" class="modal-description"></p>
                            <div id="modalPlatPrix" class="modal-price"></div>
                        </div>
                        <div class="mt-auto p-4 bg-light text-center">
                            <a href="#" class="btn btn-brand">Réserver une table</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const platModal = document.getElementById('platModal');
    if(platModal) {
        platModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const nom = button.getAttribute('data-nom');
            const prix = button.getAttribute('data-prix');
            const description = button.getAttribute('data-description');
            const image = button.getAttribute('data-image');

            platModal.querySelector('#modalPlatNom').textContent = nom;
            platModal.querySelector('#modalPlatPrix').textContent = prix;
            platModal.querySelector('#modalPlatDescription').textContent = description;
            platModal.querySelector('#modalPlatImage').setAttribute('src', image);
        });
    }
});
</script>
@endpush
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

     /* ======= NOUVEAU STYLE : Section Plats en Vedette ======= */
    .plats-galerie-section {
        padding: 80px 0;
    }
    .plat-card {
        background: var(--color-blanc);
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    .plat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    .plat-card-img {
        overflow: hidden;
        border-radius: 10px 10px 0 0;
    }
    .plat-card-img img {
        height: 250px;
        width: 100%;
        object-fit: cover;
    }
    .plat-card-body {
        padding: 25px;
        text-align: center;
    }
    .plat-card-body h4 {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--color-marron-titre);
    }
    .plat-card-body p {
        font-size: 15px;
        color: #555;
        min-height: 70px; /* Assure une hauteur minimale pour l'alignement */
    }
    .plat-card-body .price {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-vert-foret);
    }
</style>
@endpush

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

    <!-- ======= Section Ambiance ======= -->
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

    <!-- ======= Section Menu & Spécialités (Design Interactif) ======= -->
   <section id="menu" class="menu-section-v4 py-5" style="background-color: #fcfbf7;">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 text-center">
                    <div class="section-title">
                        <h2>Notre Carte</h2>
                        <p>Un Aperçu de nos Saveurs</p>
                    </div>
                    
                    @if($menuActif && $menuActif->fichier)
                        @php
                            $extension = pathinfo($menuActif->fichier, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        @if($isImage)
                            <p class="mb-4">Explorez quelques-unes de nos créations ci-dessous. Voici notre menu actuel :</p>
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/' . $menuActif->fichier) }}"
                                     alt="{{ $menuActif->titre }}"
                                     class="img-fluid rounded shadow"
                                     style="max-height: 600px; cursor: pointer;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#menuModal">
                            </div>
                            <a href="{{ asset('storage/' . $menuActif->fichier) }}" target="_blank" class="btn-download-v3 me-3">
                                <i class="bi bi-eye me-2"></i> Voir en grand
                            </a>
                            <a href="{{ asset('storage/' . $menuActif->fichier) }}" download class="btn-download-v3">
                                <i class="bi bi-download me-2"></i> Télécharger l'image
                            </a>
                        @else
                            <p class="mb-4">Explorez quelques-unes de nos créations ci-dessous. Pour une vue complète de notre offre, n'hésitez pas à télécharger notre menu détaillé.</p>
                            <a href="{{ asset('storage/' . $menuActif->fichier) }}" target="_blank" class="btn-download-v3 me-3">
                                <i class="bi bi-eye me-2"></i> Voir le Menu PDF
                            </a>
                            <a href="{{ asset('storage/' . $menuActif->fichier) }}" download class="btn-download-v3">
                                <i class="bi bi-download me-2"></i> Télécharger le Menu Complet
                            </a>
                        @endif
                    @else
                        <p class="mb-4">Aucun menu n'est actuellement configuré. Explorez nos spécialités ci-dessous.</p>
                    @endif
                    @else
                        <!-- Debug: Variable menuActif non définie -->
                        <p class="mb-4">Variable menuActif non transmise. Explorez nos spécialités ci-dessous.</p>
                    @endif
                </div>
            </div>

            @if($platsGalerie->isNotEmpty())
                <div class="gallery-wrapper mt-5">
                    @foreach($platsGalerie as $plat)
                        <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->iteration * 50 }}"
                             data-bs-toggle="modal" data-bs-target="#platModal"
                             data-nom="{{ $plat->nom }}" data-prix="{{ number_format($plat->prix, 0, ',', ' ') }} FCFA"
                             data-description="{{ $plat->description }}" data-image="{{ asset('storage/' . $plat->image) }}">
                            <img src="{{ asset('storage/' . $plat->image) }}" alt="{{ $plat->nom }}" class="img-fluid">
                            <div class="gallery-item-info">
                                <h3>{{ $plat->nom }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</main>

<!-- ======= Fenêtre Modale pour les Plats ======= -->
<div class="modal fade" id="platModal" tabindex="-1" aria-labelledby="platModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <img id="modalPlatImage" src="" class="img-fluid modal-image" alt="Image du plat">
                    </div>
                    <div class="col-lg-6 d-flex flex-column">
                        <div class="p-4">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            <h3 id="modalPlatNom" class="modal-title"></h3>
                            <p id="modalPlatDescription" class="modal-description"></p>
                            <div id="modalPlatPrix" class="modal-price"></div>
                        </div>
                        {{-- <div class="mt-auto p-4 bg-light text-center">
                            <a href="#" class="btn btn-brand">Réserver une table</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======= Fenêtre Modale pour le Menu ======= -->
@if($menuActif)
    @php
        $extension = pathinfo($menuActif->fichier, PATHINFO_EXTENSION);
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    @endphp

    @if($isImage)
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">{{ $menuActif->titre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img src="{{ asset('storage/' . $menuActif->fichier) }}"
                         alt="{{ $menuActif->titre }}"
                         class="img-fluid rounded"
                         style="max-height: 80vh;">
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="{{ asset('storage/' . $menuActif->fichier) }}" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Télécharger
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif

@endsection

@push('styles')
<style>
    /* Styles pour la section Ambiance */
    .ambiance-section-v2 { background: #fdfaf6; }
    .ambiance-image-wrapper { position: relative; height: 450px; }
    .ambiance-image-wrapper .img-main { width: 80%; height: 80%; object-fit: cover; border-radius: 10px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); position: relative; z-index: 2; transition: transform 0.5s ease; }
    .ambiance-image-wrapper .img-secondary { width: 50%; height: 50%; object-fit: cover; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 5px solid white; position: absolute; bottom: 0; right: 0; z-index: 3; transition: all 0.5s ease; }
    .ambiance-image-wrapper:hover .img-main { transform: scale(1.05); }
    .ambiance-image-wrapper:hover .img-secondary { transform: scale(1.1) rotate(3deg); }
    @media (max-width: 991px) {
        .ambiance-image-wrapper { height: auto; }
        .ambiance-image-wrapper .img-secondary { display: none; }
    }
    .ambiance-section-v2 .horaires { display: flex; align-items: center; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .ambiance-section-v2 .horaires i { font-size: 32px; color: var(--color-vert-foret); margin-right: 15px; }

    /* Styles pour la section Menu V3 */
    .btn-download-v3 { display: inline-block; background-color: transparent; border: 2px solid var(--color-marron-titre); color: var(--color-marron-titre); padding: 10px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s; }
    .btn-download-v3:hover { background-color: var(--color-marron-titre); color: var(--color-blanc); }
    .plat-gallery-item { position: relative; cursor: pointer; overflow: hidden; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .plat-gallery-item img { aspect-ratio: 1 / 1; object-fit: cover; width: 100%; transition: transform 0.4s ease; }
    .plat-gallery-item:hover img { transform: scale(1.1); }
    .plat-gallery-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.4s ease; display: flex; align-items: center; justify-content: center; color: white; text-align: center; }
    .plat-gallery-item:hover .plat-gallery-overlay { opacity: 1; }
    .overlay-content i { font-size: 2rem; }
    .overlay-content span { display: block; font-weight: 600; margin-top: 5px; }

    .gallery-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); /* Crée une grille flexible */
        gap: 1.5rem; /* Espace entre les images */
    }

    .gallery-item {
        position: relative;
        cursor: pointer;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        aspect-ratio: 1 / 1; /* Force les images à être carrées */
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease, filter 0.5s ease;
    }

    .gallery-item .gallery-item-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px 15px;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
        color: white;
        transform: translateY(100%);
        transition: transform 0.5s ease;
    }

    .gallery-item .gallery-item-info h3 {
        color: white;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    /* Animation au survol */
    .gallery-item:hover img {
        transform: scale(1.1);
        filter: brightness(0.8);
    }

    .gallery-item:hover .gallery-item-info {
        transform: translateY(0);
    }
    /* Styles pour la Fenêtre Modale */
    .modal-content { border: none; border-radius: 10px; overflow: hidden; }
    .modal-image { width: 100%; height: 100%; object-fit: cover; min-height: 400px; }
    .modal-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--color-marron-titre); margin-top: 10px; }
    .modal-description { color: #555; margin: 15px 0; }
    .modal-price { font-size: 24px; font-weight: 700; color: var(--color-vert-foret); }
</style>
@endpush

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

@php
    $makeHeaderTransparent = true;
@endphp

@extends('layouts.app')
@section('title', 'Accueil - Hôtel Le Printemps de Kpalimé') {{-- On définit le titre pour cette page --}}

@section('content') {{-- Tout ce qui suit sera placé dans le @yield('content') du layout --}}

<!-- =======================================================
  Section Hero - Plein Écran
======================================================== -->
<section id="hero" class="hero d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/1.jpg') }}');">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="hero-content" data-aos="fade-up">
                    <h1 class="text-white mb-4">Votre havre de paix au cœur de Kpalimé</h1>
                    <p class="text-white-50 mb-4">Découvrez le charme authentique de notre hôtel-restaurant, où confort et tradition se rencontrent.</p>
                    {{-- CORRIGÉ : Ce bouton pointe maintenant vers la page des chambres --}}
                    <a href="{{ route('chambres.index') }}" class="btn btn-brand btn-lg">Découvrir nos chambres</a>
                </div>
            </div>
        </div>
    </div>

    {{-- La flèche pour défiler vers le bas (ne change pas) --}}
    <a href="#features" class="scroll-down-arrow">
        <i class="bi bi-chevron-down"></i>
    </a>

</section><!-- Fin de la section Hero -->


<!-- =======================================================
  Section Nos Services (Design Professionnel V3)
======================================================== -->
<section id="features" class="experience-section">
    <div class="container">

        <div class="section-title" data-aos="fade-up">
            <h2>Nos Services</h2>
            <p>Nous avons imaginé des services uniques pour faire de votre passage un moment mémorable. Découvrez celui qui vous ressemble.</p>
        </div>

        <div class="row gy-5 justify-content-center">

            <!-- Carte 1: L'Hébergement -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="experience-card">
                    <div class="experience-image">
                        <img src="{{ asset('assets/img/4.jpg') }}" alt="Chambre confortable avec vue">
                    </div>
                    <div class="experience-content">
                        <h3>Nuits Sereines</h3>
                        <p>Plongez dans le confort de nos chambres climatisées au design unique.</p>
                        {{-- DÉJÀ CORRECT : Ce lien pointe bien vers la page des chambres --}}
                        <a href="{{ route('chambres.index') }}" class="btn-read-more">Découvrir</a>
                    </div>
                </div>
            </div>

            <!-- Carte 2: La Restauration -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="experience-card">
                    <div class="experience-image">
                        <img src="{{ asset('assets/img/bar-ambiance.jpg') }}" alt="Bar et ambiance du restaurant">
                    </div>
                    <div class="experience-content">
                        <h3>Saveurs Authentiques</h3>
                        <p>Dégustez une cuisine qui célèbre les trésors du terroir togolais.</p>
                        {{-- DÉJÀ CORRECT : Ce lien pointe bien vers la page du restaurant --}}
                        <a href="{{ route('restaurant.index') }}" class="btn-read-more">Explorer</a>
                    </div>
                </div>
            </div>

            <!-- Carte 3: Le Cadre -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="experience-card">
                    <div class="experience-image">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Vue sur les montagnes de Kpalimé">
                    </div>
                    <div class="experience-content">
                        <h3>Cadre d'Exception</h3>
                        <p>Admirez des panoramas à couper le souffle depuis nos balcons.</p>
                        <a href="{{ route('chambres.index') }}" class="btn-read-more">Découvrir</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- Fin de la section Nos Services -->


<!-- =======================================================
  Section "À Propos"
======================================================== -->
<section id="about" class="about py-5" style="background-color: #fcfbf7;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="{{ asset('assets/img/1.jpg') }}" class="img-fluid rounded shadow-lg" alt="Façade de l'hôtel Le Printemps">
            </div>
            <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0" data-aos="fade-left">
                <div class="section-title">
                    <h2>Notre Histoire</h2>
                    <p>L'âme de l'hôtel Le Printemps</p>
                </div>
                <p class="fst-italic">
                    Niché dans le cadre verdoyant de Kpalimé, notre établissement est plus qu'un simple hôtel : c'est une invitation au voyage et à la détente.
                </p>
                <ul>
                    <li><i class="bi bi-check-circle-fill text-success"></i> Une tradition d'hospitalité transmise de génération en génération.</li>
                    <li><i class="bi bi-check-circle-fill text-success"></i> Un engagement pour la promotion de la culture et des produits locaux.</li>
                    <li><i class="bi bi-check-circle-fill text-success"></i> Un confort moderne respectueux de son environnement.</li>
                </ul>
                {{-- CORRIGÉ : Ce bouton pointe maintenant vers la page "À Propos" --}}
                <a href="{{ route('about') }}" class="btn btn-brand">En savoir plus</a>
            </div>
        </div>
    </div>
</section><!-- Fin de la section "À Propos" -->


@endsection {{-- Fin de la section content --}}

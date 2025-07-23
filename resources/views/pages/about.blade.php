@php
    $makeHeaderTransparent = true;
@endphp

@extends('layouts.app')
@section('title', 'À Propos - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-header-section" style="background-image: url('{{ asset('assets/img/about-bg.jpg') }}');">
        <div class="container text-center" data-aos="fade-up">
            <h1>Notre Philosophie</h1>
            <p class="text-white-50">Bien plus qu'un hôtel, une invitation à la sérénité</p>
        </div>
    </section>

    <!-- ======= Section 1 : Notre Philosophie "Zen" ======= -->
    <section id="philosophy" class="philosophy-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="section-title text-start">
                        <h2>L'Univers du Printemps</h2>
                        <p>Retrouvez la Zen Attitude</p>
                    </div>
                    <p>
                        À l'heure où les hôtels se multiplient et ont tendance à se ressembler, Le Printemps se distingue par une philosophie unique : la **Zen Attitude**.
                    </p>
                    <p class="fst-italic">
                        Chaque détail, des décorations murales à l'agencement des chambres, est une invitation à faire le calme intérieur et à vous libérer des tensions qui alourdissent l'esprit. C'est notre promesse : vous offrir un état de quiétude, aussi bien intérieur qu'extérieur.
                    </p>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                    <img src="{{ asset('assets/img/tete.jpg') }}" class="img-fluid about-page-image" alt="Décoration zen de l'hôtel">
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Section 2 : Les Espaces, un monde à explorer ======= -->
    <section id="spaces" class="spaces-section py-5" style="background-color: #fcfbf7;">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Nos Espaces</h2>
                <p>Un monde à explorer</p>
            </div>

            <div class="row align-items-center mb-5">
                <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                    <h3>Les Extérieurs : Un Écrin de Nature</h3>
                    <p>Situé en contrebas du célèbre Château Viale, l'hôtel est accessible par une voie pittoresque qui vous plonge immédiatement dans le calme de Kuma. À votre arrivée, deux options de parking extérieur s'offrent à vous. La nature environnante et notre jardin supérieur, donnant une vue imprenable sur la cour, sont les premières étapes de votre voyage vers la tranquillité.</p>
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-right">
                    <img src="{{ asset('assets/img/espace.jpg') }}" class="img-fluid about-page-image" alt="Jardins et extérieurs de l'hôtel">
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h3>Les Intérieurs : Quatre Lieux, Quatre Ambiances</h3>
                    <p>À l'intérieur, nous avons conçu quatre espaces distincts pour varier vos plaisirs :</p>
                    <ul class="styled-list">
                        <li><i class="bi bi-check-circle-fill"></i> Une **terrasse carrelée** spacieuse, parfaite pour vos repas en plein air.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Une **paillote intime**, idéale pour un moment de méditation ou une discussion feutrée.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Un **jardin d'eau** (avec piscine en construction), accessible par une magnifique porte Torii.</li>
                        <li><i class="bi bi-check-circle-fill"></i> Notre **salle de restauration** principale, pouvant accueillir une trentaine de convives dans une atmosphère de zénitude absolue.</li>
                    </ul>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="{{ asset('assets/img/interieur.jpg') }}" class="img-fluid about-page-image" alt="Terrasse et paillote de l'hôtel">
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Section 3 : L'Édifice et l'Accès ======= -->
    <section id="access" class="access-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-up">
                    <h3>L'Édifice : Deux Niveaux de Bien-être</h3>
                    <p>Notre bâtiment principal est pensé sur deux niveaux pour séparer et sublimer vos expériences. Le premier niveau est réservé à nos **chambres Classiques** et au restaurant principal. Le second est entièrement consacré à nos **chambres Privilège**, offrant un accès exclusif à la salle de massage, au sauna et à un espace de méditation dédié.</p>
                    <hr class="my-4">
                    <h3>Comment nous trouver ?</h3>
                    <p>Nous sommes situés à environ 1km après la barrière de ticket municipal, sur la voie menant au Château Viale. Nous vous recommandons vivement cet accès pour plus de confort.</p>
                </div>
                <div class="col-lg-5 text-center" data-aos="fade-left" data-aos-delay="100">
                    <i class="bi bi-geo-alt-fill display-1" style="color: var(--color-dore-leger);"></i>
                    <h4 class="mt-3">Auberge Le Printemps</h4>
                    <p>Kuma, en contrebas du Château Viale</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=6.90486,0.630454" target="_blank" class="btn btn-outline-custom">Voir sur la carte <i class="bi bi-box-arrow-up-right"></i></a>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
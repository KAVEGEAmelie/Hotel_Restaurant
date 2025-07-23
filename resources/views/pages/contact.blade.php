@php
    $makeHeaderTransparent = true;
@endphp

@extends('layouts.app')
@section('title', 'Contactez-nous - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-header-section" style="background-image: url('{{ asset('assets/img/contact-bg.jpg') }}');">
        <div class="container text-center" data-aos="fade-up">
            <h1>Contactez-nous</h1>
            <p class="text-white-50">Nous sommes à votre écoute pour toute question ou réservation.</p>
        </div>
    </section>

    <!-- ======= Section Contact ======= -->
<section id="contact" class="contact-section pt-5 pb-0">        <div class="container" data-aos="fade-up">
            <div class="row gy-4">

                <div class="col-lg-4">
                    <div class="info-item" data-aos="fade-up" data-aos-delay="100">
                        <i class="bi bi-geo-alt flex-shrink-0"></i>
                        <div>
                            <h3>Adresse</h3>
                            <p>Rue de l'Hôtel, Kpalimé, Togo</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item" data-aos="fade-up" data-aos-delay="200">
                        <i class="bi bi-telephone flex-shrink-0"></i>
                        <div>
                            <h3>Appelez-nous</h3>
                            <p>+228 71 34 88 88</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-envelope flex-shrink-0"></i>
                        <div>
                            <h3>Email</h3>
                            <p>hotelrestaurantleprintemps@yahoo.com</p>
                        </div>
                    </div><!-- End Info Item -->
                </div>

                <div class="col-lg-8">
                    <form action="{{ route('contact.submit') }}" method="post" class="contact-form" data-aos="fade-up" data-aos-delay="200">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="name" class="form-control" placeholder="Votre Nom" required>
                            </div>
                            <div class="col-md-6 form-group mt-3 mt-md-0">
                                <input type="email" class="form-control" name="email" placeholder="Votre Email" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control" name="subject" placeholder="Sujet" required>
                        </div>
                        <div class="form-group mt-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                        </div>
                        
                        @if (session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        
<div class="text-center mt-3">
    <button type="submit" class="btn btn-brand">Envoyer le Message</button>
</div>                </div><!-- End Contact Form -->

            </div>
        </div>
    </section><!-- End Contact Section -->

</main>
@endsection
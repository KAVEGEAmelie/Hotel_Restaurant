@extends('layouts.app')

@section('title', 'Contactez-nous - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-title-section" style="background-image: url('{{ asset('assets/img/contact-bg.jpg') }}');">
        <div class="container text-center" data-aos="fade-up">
            <h1>Contactez-nous</h1>
            <p class="text-white-50">Nous sommes à votre écoute pour toute question ou réservation.</p>
        </div>
    </section>

    <!-- ======= Section Contact ======= -->
    <section id="contact" class="contact-section py-5">
        <div class="container" data-aos="fade-up">

            <div class="row gy-4">

                <div class="col-lg-4">
                    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="100">
                        <i class="bi bi-geo-alt flex-shrink-0"></i>
                        <div>
                            <h3>Adresse</h3>
                            <p>Rue de l'Hôtel, Kpalimé, Togo</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                        <i class="bi bi-telephone flex-shrink-0"></i>
                        <div>
                            <h3>Appelez-nous</h3>
                            <p>+228 71 34 88 88</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-envelope flex-shrink-0"></i>
                        <div>
                            <h3>Email</h3>
                            <p>hotelrestaurantleprintemps@yahoo.com</p>
                        </div>
                    </div><!-- End Info Item -->
                </div>

                <div class="col-lg-8">
                    <form action="{{ route('contact.submit') }}" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                        @csrf <!-- Protection CSRF de Laravel, très important ! -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Votre Nom" required>
                            </div>
                            <div class="col-md-6 form-group mt-3 mt-md-0">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Votre Email" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Sujet" required>
                        </div>
                        <div class="form-group mt-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                        </div>
                        <div class="my-3">
                            {{-- Ici s'afficheront les messages de succès ou d'erreur --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center"><button type="submit">Envoyer le Message</button></div>
                    </form>
                </div><!-- End Contact Form -->

            </div>
        </div>
    </section><!-- End Contact Section -->


</main>
@endsection

@push('scripts')
{{-- On réutilise le script Leaflet pour la carte --}}
<script>
    if (document.getElementById('contact-map')) {
        const lat = 6.90486;
        const lng = 0.630454;
        var contactMap = L.map('contact-map').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(contactMap);
        L.marker([lat, lng]).addTo(contactMap).bindPopup('<b>Auberge Le Printemps</b>').openPopup();
    }
</script>
@endpush

@push('styles')
{{-- On ajoute le style spécifique à cette page --}}
<style>
    .contact-section .info-item {
        background: #f4f4f4;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .contact-section .info-item i {
        font-size: 24px;
        color: var(--color-vert-foret);
        margin-right: 20px;
    }
    .contact-section .info-item h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: var(--color-marron-titre);
    }
    .contact-section .php-email-form {
        width: 100%;
        background: #fff;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
        padding: 30px;
        border-radius: 8px;
    }
    .contact-section .php-email-form .form-control {
        border-radius: 0;
        box-shadow: none;
        font-size: 14px;
        border-radius: 8px;
        border-color: #ddd;
    }
    .contact-section .php-email-form .form-control:focus {
        border-color: var(--color-vert-foret);
    }
    .contact-section .php-email-form button[type="submit"] {
        background: var(--color-vert-foret);
        border: 0;
        padding: 12px 34px;
        color: #fff;
        transition: 0.4s;
        border-radius: 50px;
    }
    .contact-section .php-email-form button[type="submit"]:hover {
        background: var(--color-vert-fonce-lien);
    }
</style>
@endpush
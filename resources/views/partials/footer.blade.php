<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="container">
        <div class="row gy-4">

            <!-- Colonne Info Hôtel -->
            <div class="col-lg-4 col-md-12 footer-info">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center mb-3">
                    <span>Hôtel Le Printemps</span>
                </a>
                <p>Votre havre de paix au cœur de Kpalimé. Découvrez le charme authentique où confort, tradition et hospitalité se rencontrent.</p>
                <div class="social-links d-flex mt-4">
                    <a href="https://www.facebook.com/profile.php?id=61560070130721" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <!-- Colonne Liens -->
            <div class="col-lg-2 col-md-6 footer-links">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Accueil</a></li>
                    <li><a href="{{ route('chambres.index') }}">Nos Chambres</a></li>
                    <li><a href="{{ route('restaurant.index') }}">Le Restaurant</a></li>
                    <li><a href="{{ route('contact')}}">Contact</a></li>
                </ul>
            </div>

            <!-- Colonne Contact -->
            <div class="col-lg-3 col-md-6 footer-contact">
                <h4>Contactez-nous</h4>
                <p>
                    Rue de l'Hôtel, Kpalimé<br>
                    Togo <br><br>
                    <strong>Téléphone:</strong> +228 71 34 88 88<br>
                    <strong>Email:</strong> hotelrestaurantleprintemps@yahoo.com<br>
                </p>
            </div>

            <!-- Colonne pour la carte (maintenant cliquable) -->
            <div class="col-lg-3 col-md-12 footer-map">
                <a href="https://www.google.com/maps/search/?api=1&query=6.90486,0.630454" target="_blank" rel="noopener noreferrer" class="map-link">
                    <h4>Notre Emplacement <i class="bi bi-box-arrow-up-right"></i></h4>
                    <div id="map" style="width: 100%; height: 200px; border-radius: 5px; z-index: 0;"></div>
                </a>
            </div>

        </div>
    </div>

    <div class="container mt-4">
        <div class="copyright">
            © Copyright {{ date('Y') }} <strong><span>Hôtel Le Printemps</span></strong>. Tous droits réservés.
        </div>
    </div>
</footer><!-- End Footer -->


{{-- Ce script sera "poussé" à l'emplacement @stack('scripts') dans le layout principal --}}
@push('scripts')
<script>
    // Vérifie que la div #map existe avant d'exécuter le script
    if (document.getElementById('map')) {
        // Coordonnées GPS de l'Auberge Le Printemps
        const lat = 6.90486;
        const lng = 0.630454;

        // Initialisation de la carte Leaflet
        var map = L.map('map').setView([lat, lng], 16);

        // Ajout du fond de carte OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Ajout du marqueur sur la carte
        L.marker([lat, lng]).addTo(map)
            .bindPopup('<b>Hotel Le Printemps</b><br>Nous sommes ici !')
            .openPopup();
    }
</script>
@endpush

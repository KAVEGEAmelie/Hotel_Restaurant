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
        <div class="copyright d-flex justify-content-between align-items-center">
            <div>
                © Copyright {{ date('Y') }} <strong><span>Hôtel Le Printemps</span></strong>. Tous droits réservés.
            </div>
            
            @auth
                @if(auth()->user()->canAccessAdmin())
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="admin-access-btn btn btn-outline-light btn-sm">
                            <i class="bi bi-gear-fill me-1"></i>
                            Administration
                        </a>
                    </div>
                @endif
            @endauth
        </div>
        
        <!-- Signature du développeur -->
        <div class="developer-signature text-center mt-3" style="
            position: relative !important;
            z-index: 1000 !important;
            background: rgba(0, 0, 0, 0.8) !important;
            padding: 12px 25px !important;
            border-radius: 10px !important;
            backdrop-filter: blur(5px) !important;
            -webkit-backdrop-filter: blur(5px) !important;
            border: 2px solid rgba(212, 175, 55, 0.6) !important;
            margin: 25px auto !important;
            display: inline-block !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
        ">
            <small style="
                color: #ffffff !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8) !important;
                font-family: 'Arial', sans-serif !important;
            ">
                Développé par 
                <strong style="
                    color: #ffffff !important;
                    font-weight: 700 !important;
                    font-size: 16px !important;
                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9) !important;
                ">KA.A</strong> 
                • 
                <a href="mailto:camillekvg99@gmail.com" style="
                    color: #ffd700 !important;
                    text-decoration: none !important;
                    font-weight: 600 !important;
                    font-size: 14px !important;
                    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8) !important;
                    transition: all 0.3s ease !important;
                " onmouseover="this.style.color='#ffed4e'; this.style.textDecoration='underline'; this.style.transform='scale(1.05)';" 
                   onmouseout="this.style.color='#ffd700'; this.style.textDecoration='none'; this.style.transform='scale(1)';">
                    camillekvg99@gmail.com
                </a>
            </small>
        </div>
    </div>
    
    <!-- Style de sécurité pour forcer l'affichage -->
    <style>
        .developer-signature {
            position: relative !important;
            z-index: 9999 !important;
            background: rgba(0, 0, 0, 0.8) !important;
            color: #ffffff !important;
            padding: 12px 25px !important;
            border-radius: 10px !important;
            border: 2px solid #ffd700 !important;
            margin: 25px auto !important;
            display: inline-block !important;
            text-align: center !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
            backdrop-filter: blur(5px) !important;
            -webkit-backdrop-filter: blur(5px) !important;
        }
        
        .developer-signature,
        .developer-signature *,
        .developer-signature small,
        .developer-signature strong {
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8) !important;
        }
        
        .developer-signature a,
        .developer-signature a:link,
        .developer-signature a:visited {
            color: #ffd700 !important;
            text-decoration: none !important;
            font-weight: 600 !important;
        }
        
        .developer-signature a:hover,
        .developer-signature a:focus {
            color: #ffed4e !important;
            text-decoration: underline !important;
            transform: scale(1.05) !important;
        }
        
        /* Forcer pour tous les thèmes */
        .footer .developer-signature,
        #footer .developer-signature {
            background: rgba(0, 0, 0, 0.8) !important;
            color: #ffffff !important;
        }
        
        .footer .developer-signature *,
        #footer .developer-signature * {
            color: #ffffff !important;
        }
        
        .footer .developer-signature a,
        #footer .developer-signature a {
            color: #ffd700 !important;
        }
    </style>
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

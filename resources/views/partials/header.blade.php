<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center @if(isset($transparent) && $transparent) header-transparent @endif">    <div class="container d-flex align-items-center justify-content-between">

        <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-lg-0">
            <img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo Hôtel Le Printemps">
            <div class="logo-text-container">
                <span>Hôtel Restaurant</span>
                <h1>Le Printemps</h1>
            </div>
        </a>

        <nav id="navbar" class="navbar">
            <ul>
                <li><a href="{{ route('home') }}" class="{{ Request::routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                <li><a href="{{ route('chambres.index') }}" class="{{ Request::routeIs('chambres.*') ? 'active' : '' }}">Chambres</a></li>
                <li><a href="{{ route('restaurant.index')}}" class="{{ Request::routeIs('restaurant.index') ? 'active' : '' }}">Restaurant</a></li>
                <li><a href="{{ route('about') }}" class="{{ Request::routeIs('about') ? 'active' : '' }}">À Propos</a></li>
                <li><a href="{{ route('contact') }}" class="{{ Request::routeIs('contact') ? 'active' : '' }}">Contact</a></li>
            </ul>
        </nav><!-- .navbar -->

        <a class="btn btn-brand" href="{{ route('chambres.index') }}">Réserver</a>

        <!-- Icône pour OUVRIR le menu mobile -->
        <i class="mobile-nav-toggle bi bi-list"></i>

    </div>
</header><!-- End Header -->

<!-- ======= Menu Mobile (Panneau Glissant) ======= -->
<div class="mobile-nav">
    <div class="mobile-nav-header">
        <h5 class="logo-text">Menu</h5>
        <!-- Icône pour FERMER le menu mobile (sélecteur différent) -->
        <i class="mobile-nav-close bi bi-x"></i>
    </div>
    <div class="mobile-nav-body">
        <ul>
            <li><a href="{{ route('home') }}">Accueil</a></li>
            <li><a href="{{ route('chambres.index') }}">Chambres</a></li>
            <li><a href="{{ route('restaurant.index') }}">Restaurant</a></li>
            <li><a href="{{ route('about') }}">À Propos</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
        </ul>
    </div>
    <div class="mobile-nav-footer">
        <a class="btn-mobile-reserve" href="{{ route('chambres.index') }}">Réserver un Séjour</a>
    </div>
</div>

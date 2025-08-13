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

        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-brand" href="{{ route('chambres.index') }}">Réserver</a>
            
            @auth
                @if(auth()->user()->canAccessAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm admin-header-btn" title="Administration" style="
                        width: 40px !important;
                        height: 40px !important;
                        border-radius: 50% !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        border: 1px solid #ddd !important;
                        background: #fff !important;
                        color: #666 !important;
                        transition: all 0.3s ease !important;
                        text-decoration: none !important;
                    " onmouseover="this.style.background='#f8f9fa'; this.style.borderColor='#bbb'; this.style.color='#333'; this.style.transform='scale(1.05)';" 
                       onmouseout="this.style.background='#fff'; this.style.borderColor='#ddd'; this.style.color='#666'; this.style.transform='scale(1)';">
                        <i class="bi bi-gear-fill"></i>
                    </a>
                @endif
            @endauth
        </div>

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
            @auth
                @if(auth()->user()->canAccessAdmin())
                    <li class="admin-mobile-item" style="
                        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                        margin-top: 10px !important;
                        padding-top: 10px !important;
                    ">
                        <a href="{{ route('admin.dashboard') }}" class="admin-mobile-link" style="
                            color: #ffc107 !important;
                            font-weight: 600 !important;
                            display: flex !important;
                            align-items: center !important;
                            text-decoration: none !important;
                        " onmouseover="this.style.color='#ffca2c';" onmouseout="this.style.color='#ffc107';">
                            <i class="bi bi-gear-fill me-2"></i>Administration
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </div>
    <div class="mobile-nav-footer">
        <a class="btn-mobile-reserve" href="{{ route('chambres.index') }}">Réserver un Séjour</a>
    </div>
</div>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Notre CSS Admin (qui charge aussi Bootstrap) -->
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">

        <nav class="sidebar p-0" id="admin-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="logo d-block">
                    Le Printemps
                </a>
            </div>

            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Tableau de Bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chambres.*') ? 'active' : '' }}" href="{{ route('admin.chambres.index') }}">
                        <i class="bi bi-door-open-fill"></i> Gestion Chambres
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}" href="{{ route('admin.reservations.index') }}">
                        <i class="bi bi-journal-text"></i> Réservations
                    </a>
                </li>

                <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}" href="{{ route('admin.utilisateurs.index') }}">
            <i class="bi bi-people-fill"></i> Gestion Utilisateurs
        </a>
    </li>
                <li class="nav-heading mt-4">Restaurant</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.plats.*') ? 'active' : '' }}" href="{{ route('admin.plats.index') }}">
                        <i class="bi bi-egg-fried"></i> Plats
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags-fill"></i> Catégories
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-content flex-grow-1">
            <header class="content-header d-flex justify-content-between align-items-center px-4 py-2">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="dropdown ms-auto">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-person-fill me-2"></i>{{ Auth::user()->name }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
        {{-- Lien vers le profil --}}
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon Profil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Déconnexion</button>
            </form>
        </li>
    </ul>
</div>
            </header>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('sidebar-toggled');
        });
    </script>

    @stack('scripts')
</body>
</html>

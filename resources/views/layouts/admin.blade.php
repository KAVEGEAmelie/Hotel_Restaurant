<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Styles principaux -->
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <nav class="sidebar p-0" id="admin-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="logo d-block">
                    Le Printemps
                </a>
            </div>

            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Tableau de Bord
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chambres.*') ? 'active' : '' }}"
                       href="{{ route('admin.chambres.index') }}">
                        <i class="bi bi-door-open-fill"></i> Gestion Chambres
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}"
                       href="{{ route('admin.reservations.index') }}">
                        <i class="bi bi-journal-text"></i> Réservations
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}"
                       href="{{ route('admin.utilisateurs.index') }}">
                        <i class="bi bi-people-fill"></i> Gestion Utilisateurs
                    </a>
                </li>

                <li class="nav-heading mt-4">Restaurant</li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.menus-pdf.*') ? 'active' : '' }}"
                       href="{{ route('admin.menus-pdf.index') }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Gérer les Menus (PDF)
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.plats-galerie.*') ? 'active' : '' }}"
                       href="{{ route('admin.plats-galerie.index') }}">
                        <i class="bi bi-images"></i> Plats en Galerie
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Header -->
            <header class="content-header d-flex justify-content-between align-items-center px-4 py-2">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="dropdown ms-auto">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-fill me-2"></i>{{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
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

            <!-- Page Content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    <script src="{{ asset('js/admin-notifications.js') }}"></script>
    <script src="{{ asset('js/admin-actions.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    <script src="{{ asset('js/admin-actions.js') }}"></script>

    <!-- Flash Messages JS -->
    <script>
        window.flashMessages = {
            @if(session('success')) success: @json(session('success')), @endif
            @if(session('error')) error: @json(session('error')), @endif
            @if(session('warning')) warning: @json(session('warning')), @endif
            @if(session('info')) info: @json(session('info')), @endif
        };
    </script>

    <!-- Sidebar Toggle -->
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function () {
            document.getElementById('wrapper').classList.toggle('sidebar-toggled');
        });
    </script>

    <!-- Gestion des Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('notification'))
                @php $notif = session('notification'); @endphp
                setTimeout(() => {
                    window.notify.{{ $notif['type'] ?? 'info' }}(
                        '{{ $notif['title'] ?? '' }}',
                        {
                            message: '{{ $notif['message'] ?? '' }}',
                            duration: {{ $notif['duration'] ?? 5000 }}
                        }
                    );
                }, 500);
            @endif

            @if(session('notification_secondary'))
                @php $notif = session('notification_secondary'); @endphp
                setTimeout(() => {
                    window.notify.{{ $notif['type'] ?? 'info' }}(
                        '{{ $notif['title'] ?? '' }}',
                        {
                            message: '{{ $notif['message'] ?? '' }}',
                            duration: {{ $notif['duration'] ?? 5000 }}
                        }
                    );
                }, 2500);
            @endif

            // Flash simples
            @if(session('success'))
                setTimeout(() => {
                    window.notify.success('{{ session('success') }}', { duration: 5000 });
                }, 500);
            @endif

            @if(session('error'))
                setTimeout(() => {
                    window.notify.error('{{ session('error') }}', { duration: 6000 });
                }, 500);
            @endif

            @if(session('info'))
                setTimeout(() => {
                    window.notify.info('{{ session('info') }}', { duration: 4000 });
                }, 1500);
            @endif

            @if(session('warning'))
                setTimeout(() => {
                    window.notify.warning('{{ session('warning') }}', { duration: 5000 });
                }, 500);
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>

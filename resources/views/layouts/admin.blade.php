<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite (pour Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- ============================================= -->
        <!-- Barre de Navigation Latérale - C'EST ICI ! -->
        <!-- ============================================= -->
        <aside class="bg-gray-800 text-white w-64 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-30" 
               :class="{'translate-x-0': open, '-translate-x-full': !open}">
            
            <div class="p-4 text-2xl font-bold border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}">Hôtel Le Printemps</a>
            </div>

            <nav class="mt-4">
                {{-- Lien vers le Tableau de Bord --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center mt-4 py-2 px-6 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900' : '' }}">
                    <i class="bi bi-speedometer2 mr-3"></i> Tableau de Bord
                </a>

                {{-- Lien vers la Gestion des Chambres --}}
                <a href="{{ route('admin.chambres.index') }}" class="flex items-center mt-4 py-2 px-6 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.chambres.*') ? 'bg-gray-900' : '' }}">
                    <i class="bi bi-door-open-fill mr-3"></i> Gestion Chambres
                </a>

                {{-- C'est ici que vous ajouterez les nouveaux liens --}}
                {{-- Exemple pour les réservations : --}}
                {{-- 
                <a href="#" class="flex items-center mt-4 py-2 px-6 text-gray-300 hover:bg-gray-700 hover:text-white">
                    <i class="bi bi-journal-text mr-3"></i> Réservations
                </a>
                --}}
                 
            </nav>
        </aside>


        <!-- Contenu Principal -->
        <div class="md:ml-64">
            <!-- Header (juste pour le menu utilisateur) -->
            <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-end h-16">
                        @include('layouts.partials.user-dropdown')
                    </div>
                </div>
            </header>

            <!-- Contenu de la Page Spécifique -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
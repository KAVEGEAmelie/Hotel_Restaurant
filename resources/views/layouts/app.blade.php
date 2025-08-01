<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    @php
        $isPublicPage = !isset($header);
    @endphp

    @if($isPublicPage)
        {{-- === ASSETS POUR LE SITE PUBLIC === --}}
        <!-- Favicons -->
        <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
        <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">

        <!-- CSS pour Leaflet (la carte) -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

        <!-- Votre fichier CSS Principal -->
        <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

        <!-- Alpine.js pour le menu hamburger -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @stack('styles')
    @else
        {{-- === ASSETS POUR LE BACK-OFFICE (DASHBOARD) === --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased">

    @if($isPublicPage)
    {{-- On inclut le header en lui passant une variable --}}
    @include('partials.header', ['transparent' => isset($makeHeaderTransparent) && $makeHeaderTransparent])

    <main>
        @yield('content')
    </main>
    @include('partials.footer')
@endif


    {{-- On charge les scripts JS uniquement pour les pages publiques --}}
    @if($isPublicPage)
        <!-- Vendor JS Files -->
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>

        <!-- Votre fichier JS Principal -->
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <!-- SCRIPT pour Leaflet (la carte) -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        @stack('scripts')
    @endif
</body>
</html>

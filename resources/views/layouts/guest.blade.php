<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} - Connexion</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" 
             style="background-image: url('{{ asset('assets/img/login-bg.jpg') }}'); background-size: cover; background-position: center;">
            
            {{-- Voile sombre pour le contraste --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>

            {{-- Le contenu sera positionné par-dessus --}}
            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-8 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
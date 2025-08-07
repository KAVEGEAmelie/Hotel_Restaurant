<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- === ASSETS POUR LE SITE PUBLIC === -->

    <!-- Favicons -->
    {{-- <link href="{{ asset('assets/img/favicon.png') }}" rel="icon"> --}}
    {{-- <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon"> --}}

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <!-- Votre fichier CSS Principal -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

    <!-- Système de notifications modernes -->
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

    {{-- On inclut le header en lui passant une variable pour la transparence --}}
    @include('partials.header', ['transparent' => isset($makeHeaderTransparent) && $makeHeaderTransparent])

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>

    <!-- SCRIPT pour Leaflet (la carte) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Votre fichier JS Principal -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Système de notifications modernes -->
    <script src="{{ asset('js/notifications.js') }}"></script>

    <!-- Messages Flash Laravel vers JavaScript -->
    <script>
        window.flashMessages = {
            @if(session('success'))
                success: @json(session('success')),
            @endif
            @if(session('error'))
                error: @json(session('error')),
            @endif
            @if(session('warning'))
                warning: @json(session('warning')),
            @endif
            @if(session('info'))
                info: @json(session('info')),
            @endif
        };
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des notifications Flash structurées
    @if(session('notification'))
        @php $notification = session('notification'); @endphp
        setTimeout(() => {
            window.notify.{{ $notification['type'] ?? 'info' }}(
                '{{ $notification['title'] ?? '' }}',
                {
                    message: '{{ $notification['message'] ?? '' }}',
                    duration: {{ $notification['duration'] ?? 5000 }}
                }
            );
        }, 500);
     @endif

    @if(session('notification_secondary'))
        @php $notification = session('notification_secondary'); @endphp
        setTimeout(() => {
            window.notify.{{ $notification['type'] ?? 'info' }}(
                '{{ $notification['title'] ?? '' }}',
                {
                    message: '{{ $notification['message'] ?? '' }}',
                    duration: {{ $notification['duration'] ?? 5000 }}
                }
            );
        }, 2000);
    @endif
    // Gestion des anciens messages Flash (rétrocompatibilité)
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

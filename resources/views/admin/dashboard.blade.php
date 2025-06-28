@extends('layouts.admin')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Tableau de Bord
            </h1>
            <a href="{{ route('admin.chambres.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 shadow-md flex items-center">
                <i class="bi bi-plus-circle-fill mr-2"></i>
                Ajouter une Chambre
            </a>
        </div>
        
        <!-- Section des statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Carte Chambres -->
            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-door-open"></i>
                </div>
                <div class="info">
                    <p class="title">Chambres Totales</p>
                    <p class="number">{{ $stats['chambres'] }}</p>
                </div>
            </div>
            
            <!-- Carte Réservations -->
            <div class="stat-card">
                <div class="icon blue">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="info">
                    <p class="title">Réservations Totales</p>
                    <p class="number">{{ $stats['reservations'] }}</p>
                </div>
            </div>

            <!-- Carte Utilisateurs -->
            <div class="stat-card">
                <div class="icon yellow">
                    <i class="bi bi-people"></i>
                </div>
                <div class="info">
                    <p class="title">Utilisateurs Enregistrés</p>
                    <p class="number">{{ $stats['utilisateurs'] }}</p>
                </div>
            </div>
        </div>

        <!-- Section des dernières réservations -->
        <div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Dernières Réservations
            </h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Dates du séjour</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($dernieresReservations as $reservation)
                            <tr>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->chambre->nom }}</td>
                                <td>
                                    Du {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}<br>
                                    Au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $reservation->statut == 'confirmée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($reservation->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Aucune réservation pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
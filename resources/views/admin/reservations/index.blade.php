@extends('layouts.admin')

@section('title', 'Gestion des Réservations')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">
            Gestion des Réservations
        </h1>

        <!-- Formulaire de Recherche -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-6">
            <form action="{{ route('admin.reservations.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Champ de recherche texte -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rechercher (Nom, Email...)</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <!-- Filtre par date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date d'arrivée</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <!-- Filtre par statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                        <select name="statut" id="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                            <option value="all" @selected(request('statut') == 'all')>Tous</option>
                            <option value="pending" @selected(request('statut') == 'pending')>En attente</option>
                            <option value="confirmée" @selected(request('statut') == 'confirmée')>Confirmée</option>
                            <option value="annulée" @selected(request('statut') == 'annulée')>Annulée</option>
                        </select>
                    </div>
                    <!-- Boutons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filtrer</button>
                        <a href="{{ route('admin.reservations.index') }}" class="w-full text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Chambre</th>
                                <th>Dates du séjour</th>
                                <th>Prix Total</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $reservation->client_nom }} {{ $reservation->client_prenom }}</div>
                                        <div class="text-sm text-gray-500">{{ $reservation->client_email }}</div>
                                    </td>
                                    <td>{{ $reservation->chambre->nom ?? 'Chambre supprimée' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($reservation->statut == 'confirmée') bg-green-100 text-green-800 @elseif($reservation->statut == 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($reservation->statut) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        {{-- Lien vers la page de détails (à créer) --}}
                                        {{-- <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-indigo-600 hover:underline">Voir</a> --}}
                                        <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-4">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">Aucune réservation trouvée pour les critères sélectionnés.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($reservations->hasPages())
                    <div class="p-6">
                        {{-- Affiche les liens de pagination en gardant les filtres --}}
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
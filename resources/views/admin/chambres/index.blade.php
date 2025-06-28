@extends('layouts.admin')

@section('title', 'Gestion des Chambres')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Gestion des Chambres
            </h1>
            <a href="{{ route('admin.chambres.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 shadow-md flex items-center">
                <i class="bi bi-plus-circle-fill mr-2"></i>
                Ajouter une Chambre
            </a>
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
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Prix / nuit</th>
                                <th>Capacité</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chambres as $chambre)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $chambre->image_principale) }}" alt="{{ $chambre->nom }}" class="h-12 w-16 object-cover rounded">
                                    </td>
                                    <td class="font-medium text-gray-900 dark:text-white">{{ $chambre->nom }}</td>
                                    <td>{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $chambre->capacite }} pers.</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.chambres.edit', $chambre) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Modifier</a>
                                        <form action="{{ route('admin.chambres.destroy', $chambre) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ? Cette action est irréversible.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline ml-4">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8">Aucune chambre n'a été ajoutée pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($chambres->hasPages())
                    <div class="p-6">
                        {{ $chambres->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
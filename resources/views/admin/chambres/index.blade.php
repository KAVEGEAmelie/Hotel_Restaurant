@extends('layouts.admin')

@section('title', 'Gestion des Chambres')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestion des Chambres</h1>
            <a href="{{ route('admin.chambres.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une Chambre
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Image</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Prix / nuit</th>
                                <th scope="col">Capacité</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chambres as $chambre)
                                <tr>
                                    <td>
                                        <img src="{{ asset('public/' . $chambre->image_principale) }}" alt="{{ $chambre->nom }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                    </td>
                                    <td class="fw-bold">{{ $chambre->nom }}</td>
                                    <td>{{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $chambre->capacite }} pers.</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.chambres.edit', $chambre) }}" class="btn btn-sm btn-primary me-2">
                                            <i class="bi bi-pencil-fill"></i> Modifier
                                        </a>
                                        <form action="{{ route('admin.chambres.destroy', $chambre) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        Aucune chambre n'a été ajoutée pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($chambres->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $chambres->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

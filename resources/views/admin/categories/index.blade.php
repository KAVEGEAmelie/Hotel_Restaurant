@extends('layouts.admin')

@section('title', 'Gestion des Catégories de Menu')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Catégories de Menu</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Ajouter une catégorie</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Ordre d'affichage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $categorie)
                            <tr>
                                <td>{{ $categorie->nom }}</td>
                                <td>{{ $categorie->ordre }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $categorie) }}" class="btn btn-sm btn-primary">Modifier</a>
                                    <form action="{{ route('admin.categories.destroy', $categorie) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

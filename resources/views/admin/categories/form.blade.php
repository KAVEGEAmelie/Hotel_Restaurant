@extends('layouts.admin')
@section('title', isset($categorie) ? 'Modifier' : 'Ajouter une Catégorie')
@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">{{ isset($categorie) ? 'Modifier : ' . $categorie->nom : 'Ajouter une catégorie' }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ isset($categorie) ? route('admin.categories.update', $categorie) : route('admin.categories.store') }}" method="POST">
                @csrf
                @if(isset($categorie))
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom de la catégorie</label>
                    <input type="text" name="nom" class="form-control" value="{{ old('nom', $categorie->nom ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label for="ordre" class="form-label">Ordre d'affichage</label>
                    <input type="number" name="ordre" class="form-control" value="{{ old('ordre', $categorie->ordre ?? '0') }}" required>
                    <small class="form-text text-muted">Un chiffre plus petit s'affichera avant.</small>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-success">{{ isset($categorie) ? 'Mettre à jour' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

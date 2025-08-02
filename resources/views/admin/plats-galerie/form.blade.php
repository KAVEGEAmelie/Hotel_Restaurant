@extends('layouts.admin')
@section('title', isset($plat) ? 'Modifier le Plat' : 'Ajouter un Plat à la Galerie')
@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">{{ isset($plat) ? 'Modifier : ' . $plat->nom : 'Ajouter un Plat à la Galerie' }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Oups ! Erreur de validation</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($plat) ? route('admin.plats-galerie.update', $plat) : route('admin.plats-galerie.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($plat))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="nom" class="form-label">Nom du plat</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom', $plat->nom ?? '') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="prix" class="form-label">Prix (FCFA)</label>
                        <input type="number" name="prix" id="prix" class="form-control" value="{{ old('prix', $plat->prix ?? '') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description courte</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description', $plat->description ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Image du plat</label>
                    <input type="file" name="image" id="image" class="form-control" {{ isset($plat) ? '' : 'required' }}>
                    @if(isset($plat) && $plat->image)
                        <div class="mt-3">
                            <p class="small text-muted">Image actuelle :</p>
                            <img src="{{ asset('storage/' . $plat->image) }}" alt="Image actuelle" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <a href="{{ route('admin.plats-galerie.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-success">{{ isset($plat) ? 'Mettre à jour' : 'Enregistrer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

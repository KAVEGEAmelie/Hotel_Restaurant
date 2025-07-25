@extends('layouts.admin')

@section('title', isset($chambre) ? 'Modifier la Chambre' : 'Ajouter une Chambre')

@section('content')
    <div class="container-fluid">
        <h1 class="h2 mb-4">
            {{ isset($chambre) ? 'Modifier : ' . $chambre->nom : 'Ajouter une nouvelle chambre' }}
        </h1>

        <div class="card shadow-sm">
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Oups ! Erreur de validation.</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ isset($chambre) ? route('admin.chambres.update', $chambre) : route('admin.chambres.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($chambre))
                        @method('PUT')
                    @endif

                    <!-- Nom -->
                    <div class="mb-4">
                        <label for="nom" class="form-label">Nom de la chambre</label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $chambre->nom ?? '') }}" required class="form-control">
                    </div>

                    <!-- Prix & Capacité -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="prix_par_nuit" class="form-label">Prix / nuit (FCFA)</label>
                            <input type="number" step="500" name="prix_par_nuit" id="prix_par_nuit" value="{{ old('prix_par_nuit', $chambre->prix_par_nuit ?? '') }}" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="capacite" class="form-label">Capacité (personnes)</label>
                            <input type="number" name="capacite" id="capacite" value="{{ old('capacite', $chambre->capacite ?? '') }}" required class="form-control">
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="mb-4">
                        <label for="description_courte" class="form-label">Description Courte</label>
                        <input type="text" name="description_courte" id="description_courte" value="{{ old('description_courte', $chambre->description_courte ?? '') }}" required class="form-control" placeholder="Une phrase d'accroche pour la liste des chambres.">
                    </div>
                    <div class="mb-4">
                        <label for="description_longue" class="form-label">Description Détaillée</label>
                        <textarea name="description_longue" id="description_longue" rows="5" class="form-control" placeholder="Détaillez les équipements, l'ambiance, etc.">{{ old('description_longue', $chambre->description_longue ?? '') }}</textarea>
                    </div>

                    <!-- Image Principale -->
                    <div class="mb-4">
                        <label for="image_principale" class="form-label">Image Principale</label>
                        <input type="file" name="image_principale" id="image_principale" class="form-control">
                        @if(isset($chambre) && $chambre->image_principale)
                            <div class="mt-3">
                                <p class="small text-muted">Image actuelle :</p>
                                <img src="{{ asset('storage/' . $chambre->image_principale) }}" alt="Image actuelle" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end pt-4 border-top">
                        <a href="{{ route('admin.chambres.index') }}" class="btn btn-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-success">
                            {{ isset($chambre) ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

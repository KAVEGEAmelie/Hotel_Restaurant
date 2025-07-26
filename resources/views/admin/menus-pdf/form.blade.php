@extends('layouts.admin')

@section('title', isset($menu_pdf) ? 'Modifier le Menu' : 'Ajouter un Menu')

@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">
        {{ isset($menu_pdf) ? 'Modifier le Menu : ' . $menu_pdf->titre : 'Ajouter un nouveau Menu (PDF/Image)' }}
    </h1>

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

            <form action="{{ isset($menu_pdf) ? route('admin.menus-pdf.update', $menu_pdf) : route('admin.menus-pdf.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($menu_pdf))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="titre" class="form-label">Titre du menu (ex: Menu Principal 2025)</label>
                    <input type="text" name="titre" id="titre" class="form-control" value="{{ old('titre', $menu_pdf->titre ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="fichier" class="form-label">Fichier (PDF ou Image)</label>
                    <input type="file" name="fichier" id="fichier" class="form-control" {{ isset($menu_pdf) ? '' : 'required' }}>
                    @if(isset($menu_pdf) && $menu_pdf->fichier)
                        <div class="mt-3">
                            <p class="small text-muted">Fichier actuel :
                                <a href="{{ asset('uploads/' . $menu_pdf->fichier) }}" target="_blank">Voir le fichier</a>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="est_actif" name="est_actif" value="1"
                           @if(old('est_actif') || (isset($menu_pdf) && $menu_pdf->est_actif)) checked @endif>
                    <label class="form-check-label" for="est_actif">Définir comme menu actif sur le site public</label>
                    <small class="form-text d-block text-muted">Cocher cette case désactivera les autres menus actifs.</small>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <a href="{{ route('admin.menus-pdf.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-success">{{ isset($menu_pdf) ? 'Mettre à jour' : 'Enregistrer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

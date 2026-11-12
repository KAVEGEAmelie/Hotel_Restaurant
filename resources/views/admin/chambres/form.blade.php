@extends('layouts.admin')

@section('title', isset($chambre) ? 'Modifier la Chambre' : 'Ajouter une Chambre')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des erreurs de validation
    @if($errors->any())
        const errors = @json($errors->all());
        AdminNotifications.showValidationErrors({errors: errors});
    @endif
    
    // Notification d'aide pour le formulaire
    setTimeout(() => {
        window.notify.info('💡 Remplissez tous les champs obligatoires', { duration: 4000 });
    }, 500);
    
    // Ajouter des listeners sur le formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                window.notify.info('⏳ Enregistrement en cours...', { autoClose: false });
            }
        });
    }

    // Aperçu des images de galerie sélectionnées
    const imagesInput = document.getElementById('images');
    const preview = document.getElementById('images-preview');
    if (imagesInput && preview) {
        imagesInput.addEventListener('change', function() {
            preview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail';
                img.style.cssText = 'width: 120px; height: 90px; object-fit: cover;';
                preview.appendChild(img);
            });
        });
    }
});
</script>
@endpush

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
                        <input type="file" name="image_principale" id="image_principale" accept="image/*" class="form-control">
                        @if(isset($chambre) && $chambre->image_principale)
                            <div class="mt-3">
                                <p class="small text-muted">Image actuelle :</p>
                                <img src="{{ asset('storage/' . $chambre->image_principale) }}" alt="Image actuelle" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                    </div>

                    <!-- Galerie d'images -->
                    <div class="mb-4">
                        <label for="images" class="form-label">Galerie d'images (plusieurs possibles)</label>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple class="form-control">
                        <div class="form-text">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs images. Formats : jpeg, png, jpg, gif, webp — 2 Mo max par image.</div>
                        <div id="images-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end pt-4 border-top">
                        <a href="{{ route('admin.chambres.index') }}" class="btn btn-secondary me-2">Annuler</a>
                        <button type="submit" class="btn btn-success">
                            {{ isset($chambre) ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>

                @if(isset($chambre) && $chambre->images->isNotEmpty())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="mb-3">Images de la galerie ({{ $chambre->images->count() }})</h5>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($chambre->images as $image)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image) }}" alt="Image galerie" class="img-thumbnail" style="width: 150px; height: 110px; object-fit: cover;">
                                    <form action="{{ route('admin.chambres.images.destroy', $image) }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Supprimer cette image ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

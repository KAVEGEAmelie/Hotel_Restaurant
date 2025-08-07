@extends('layouts.admin')

@section('title', 'Détails de la Chambre')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header avec boutons d'action -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $chambre->nom }}</h1>
                    <p class="text-muted">Détails de la chambre #{{ $chambre->id }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.chambres.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                    <a href="{{ route('admin.chambres.edit', $chambre) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Colonne principale -->
                <div class="col-lg-8">
                    <!-- Images de la chambre -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-images"></i> Images
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($chambre->image_principale)
                                <div class="mb-3">
                                    <h6>Image principale</h6>
                                    <img src="{{ asset('storage/' . $chambre->image_principale) }}"
                                         alt="Image principale de {{ $chambre->nom }}"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 300px;">
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-image-fill" style="font-size: 3rem;"></i>
                                    <p>Aucune image disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-card-text"></i> Description
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Description courte</h6>
                                <p class="card-text">{{ $chambre->description_courte ?? 'Aucune description courte disponible.' }}</p>
                            </div>

                            @if($chambre->description_longue)
                                <div>
                                    <h6>Description détaillée</h6>
                                    <p class="card-text">{{ $chambre->description_longue }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <div class="col-lg-4">
                    <!-- Informations générales -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Informations générales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <strong>Statut :</strong>
                                    @if($chambre->est_disponible)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Disponible
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Indisponible
                                        </span>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <strong>Capacité :</strong>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-people"></i> {{ $chambre->capacite }} personne(s)
                                    </span>
                                </div>

                                <div class="col-12">
                                    <strong>Prix par nuit :</strong>
                                    <span class="h5 text-success">
                                        {{ number_format($chambre->prix_par_nuit, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>

                                <div class="col-12">
                                    <strong>Slug :</strong>
                                    <code>{{ $chambre->slug }}</code>
                                </div>

                                <div class="col-12">
                                    <strong>Créée le :</strong>
                                    <span>{{ $chambre->created_at->format('d/m/Y à H:i') }}</span>
                                </div>

                                <div class="col-12">
                                    <strong>Modifiée le :</strong>
                                    <span>{{ $chambre->updated_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning"></i> Actions rapides
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <!-- Toggle statut -->
                                <button type="button"
                                        class="btn {{ $chambre->est_disponible ? 'btn-warning' : 'btn-success' }}"
                                        onclick="AdminActionComponents.confirmStatusToggle(
                                            {{ $chambre->id }},
                                            '{{ $chambre->nom }}',
                                            {{ $chambre->est_disponible ? 'true' : 'false' }}
                                        )">
                                    <i class="bi bi-{{ $chambre->est_disponible ? 'pause' : 'play' }}-circle"></i>
                                    {{ $chambre->est_disponible ? 'Rendre indisponible' : 'Rendre disponible' }}
                                </button>

                                <!-- Voir les réservations -->
                                <a href="{{ route('admin.reservations.index') }}?chambre_id={{ $chambre->id }}"
                                   class="btn btn-info">
                                    <i class="bi bi-calendar"></i>
                                    Voir les réservations
                                </a>

                                <!-- Voir sur le site public -->
                                <a href="{{ route('chambres.show', $chambre->slug) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                    Voir sur le site
                                </a>

                                <!-- Supprimer -->
                                <form action="{{ route('admin.chambres.destroy', $chambre) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-danger w-100 delete-btn"
                                            data-item-name="{{ $chambre->nom }}">
                                        <i class="bi bi-trash"></i>
                                        Supprimer la chambre
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up"></i> Statistiques
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">
                                            {{ $chambre->reservations()->count() }}
                                        </h4>
                                        <small class="text-muted">Réservations totales</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">
                                        {{ $chambre->reservations()->where('statut', 'confirmed')->count() }}
                                    </h4>
                                    <small class="text-muted">Confirmées</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les composants d'action admin
    if (typeof AdminActionComponents !== 'undefined') {
        AdminActionComponents.initialize();
    }
});
</script>
@endpush

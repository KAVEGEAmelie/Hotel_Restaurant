@extends('layouts.admin')

@section('title', 'Gestion des Chambres')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de chargement de page
    window.notify.info('🏨 Gestion des chambres chargée', { duration: 3000 });
    
    // Initialiser les tooltips Bootstrap pour les boutons d'action
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Ajouter des animations aux cartes au survol
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Gestion des exports
    document.querySelectorAll('.export-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const exportType = this.dataset.exportType;
            window.notify.info(`📄 Export ${exportType} en cours...`, { duration: 4000 });
            
            // Simuler l'export après un délai
            setTimeout(() => {
                window.notify.success(`✅ Export ${exportType} terminé!`, { duration: 3000 });
            }, 2000);
        });
    });
});

// Fonction pour les checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateBulkActions();
}

// Mise à jour des actions groupées
function updateBulkActions() {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkSubmit = document.getElementById('bulk-submit');
    const bulkAction = document.getElementById('bulk-action');
    
    if (checkedItems.length > 0) {
        bulkSubmit.disabled = false;
        bulkSubmit.innerHTML = `<i class="bi bi-lightning-fill me-1"></i>Appliquer (${checkedItems.length})`;
    } else {
        bulkSubmit.disabled = true;
        bulkSubmit.innerHTML = '<i class="bi bi-lightning-fill me-1"></i>Appliquer';
    }
}

// Écouter les changements sur les checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-checkbox')) {
        updateBulkActions();
    }
});

// Gestion du formulaire d'actions groupées
document.getElementById('bulk-actions-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkAction = document.getElementById('bulk-action').value;
    
    if (checkedItems.length === 0) {
        window.notify.warning('⚠️ Veuillez sélectionner au moins un élément');
        return;
    }
    
    if (!bulkAction) {
        window.notify.warning('⚠️ Veuillez choisir une action à effectuer');
        return;
    }
    
    // Préparer les données pour la confirmation
    const itemIds = Array.from(checkedItems).map(cb => cb.value);
    const actionText = {
        'activate': 'activer',
        'deactivate': 'désactiver', 
        'delete': 'supprimer'
    }[bulkAction];
    
    // Utiliser le système de confirmation sophistiqué
    AdminActionComponents.confirmBulkAction(itemIds, actionText, bulkAction);
});
</script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestion des Chambres</h1>
            <a href="{{ route('admin.chambres.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une Chambre
            </a>
        </div>

        @if(session('success'))
            {{-- Les messages Flash sont maintenant gérés par le système de notifications JavaScript --}}
        @endif

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Chambres</h5>
                <div class="d-flex gap-2">
                    <!-- Actions groupées -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary export-btn" 
                                data-export-type="PDF" 
                                data-bs-toggle="tooltip" 
                                title="Exporter en PDF">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary export-btn" 
                                data-export-type="Excel" 
                                data-bs-toggle="tooltip" 
                                title="Exporter en Excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Formulaire actions groupées -->
                <form id="bulk-actions-form" method="POST" action="#" class="mb-3">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select id="bulk-action" name="bulk_action" class="form-select form-select-sm">
                                <option value="">Choisir une action...</option>
                                <option value="activate">Activer sélectionnés</option>
                                <option value="deactivate">Désactiver sélectionnés</option>
                                <option value="delete">Supprimer sélectionnés</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" id="bulk-submit" class="btn btn-sm btn-primary" disabled>
                                <i class="bi bi-lightning-fill me-1"></i>Appliquer
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Sélectionnez des éléments pour activer les actions groupées
                            </small>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th scope="col">Image</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Prix / nuit</th>
                                <th scope="col">Capacité</th>
                                <th scope="col">Statut</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chambres as $chambre)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $chambre->id }}" id="item_{{ $chambre->id }}">
                                            <label class="form-check-label" for="item_{{ $chambre->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        @if($chambre->image_principale)
                                            <img src="{{ asset('storage/' . $chambre->image_principale) }}" alt="{{ $chambre->nom }}" class="img-thumbnail" style="width: 60px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="width: 60px; height: 50px;">
                                                <i class="bi bi-house-door text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $chambre->nom }}</strong>
                                        @if($chambre->description)
                                            <br><small class="text-muted">{{ Str::limit($chambre->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ number_format($chambre->prix_par_nuit ?? $chambre->prix, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="bi bi-people"></i> {{ $chambre->capacite }} pers.
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $chambre->est_disponible ? 'bg-success' : 'bg-danger' }}">
                                            <i class="bi bi-{{ $chambre->est_disponible ? 'check-circle' : 'x-circle' }}"></i>
                                            {{ $chambre->est_disponible ? 'Disponible' : 'Indisponible' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.chambres.show', $chambre) }}" class="btn btn-outline-info btn-sm" data-bs-toggle="tooltip" title="Voir les détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.chambres.edit', $chambre) }}" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-{{ $chambre->est_disponible ? 'secondary' : 'success' }} btn-sm" 
                                                    onclick="AdminActionComponents.confirmStatusToggle({{ $chambre->id }}, '{{ $chambre->nom }}', {{ $chambre->est_disponible ? 'true' : 'false' }})"
                                                    data-bs-toggle="tooltip" title="{{ $chambre->est_disponible ? 'Marquer indisponible' : 'Marquer disponible' }}">
                                                <i class="bi bi-toggle-{{ $chambre->est_disponible ? 'on' : 'off' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="AdminActionComponents.confirmDelete({{ $chambre->id }}, '{{ $chambre->nom }}', 'chambres')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-house-x fs-1 mb-3 d-block"></i>
                                            <h5>Aucune chambre disponible</h5>
                                            <p>Commencez par ajouter votre première chambre.</p>
                                            <a href="{{ route('admin.chambres.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Ajouter une chambre
                                            </a>
                                        </div>
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

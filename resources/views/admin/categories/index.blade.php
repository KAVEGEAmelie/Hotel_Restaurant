@extends('layouts.admin')

@section('title', 'Gestion des Catégories de Menu')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de chargement de page
    window.notify.info('🏷️ Gestion des catégories chargée', { duration: 3000 });
    
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Fonctions pour les checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkSubmit = document.getElementById('bulk-submit');
    
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
    
    const itemIds = Array.from(checkedItems).map(cb => cb.value);
    const actionText = {
        'delete': 'supprimer'
    }[bulkAction];
    
    AdminActionComponents.confirmBulkAction(itemIds, actionText, bulkAction);
});
</script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-tags-fill text-primary me-2"></i>
                Catégories de Menu
            </h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une catégorie
            </a>
        </div>

        @if(session('success'))
            {{-- Les messages Flash sont maintenant gérés par le système de notifications JavaScript --}}
        @endif

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Catégories</h5>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                data-bs-toggle="tooltip" 
                                title="Exporter en PDF">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
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
                                <th scope="col">Nom</th>
                                <th scope="col">Ordre d'affichage</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $categorie)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $categorie->id }}" id="item_{{ $categorie->id }}">
                                            <label class="form-check-label" for="item_{{ $categorie->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $categorie->nom }}</strong>
                                        @if($categorie->description)
                                            <br><small class="text-muted">{{ Str::limit($categorie->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $categorie->ordre }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.edit', $categorie) }}" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="AdminActionComponents.confirmDelete({{ $categorie->id }}, '{{ $categorie->nom }}', 'categories')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-tags fs-1 mb-3 d-block"></i>
                                            <h5>Aucune catégorie disponible</h5>
                                            <p>Commencez par ajouter votre première catégorie de menu.</p>
                                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Ajouter une catégorie
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

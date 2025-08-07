@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de chargement de page
    window.notify.info('👥 Gestion des utilisateurs chargée', { duration: 3000 });
    
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
        'activate': 'activer',
        'deactivate': 'désactiver',
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
                <i class="bi bi-people-fill text-primary me-2"></i>
                Gestion des Utilisateurs
            </h1>
            <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter un utilisateur
            </a>
        </div>

        @if(session('success'))
            {{-- Les messages Flash sont maintenant gérés par le système de notifications JavaScript --}}
        @endif

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Utilisateurs</h5>
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
                                <th scope="col">Nom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Rôle</th>
                                <th scope="col">Statut</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $user->id }}" id="item_{{ $user->id }}">
                                            <label class="form-check-label" for="item_{{ $user->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br><small class="text-muted">Membre depuis {{ $user->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $user->email }}</span>
                                        @if($user->email_verified_at)
                                            <i class="bi bi-patch-check-fill text-success ms-1" data-bs-toggle="tooltip" title="Email vérifié"></i>
                                        @else
                                            <i class="bi bi-exclamation-triangle-fill text-warning ms-1" data-bs-toggle="tooltip" title="Email non vérifié"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-primary">
                                                <i class="bi bi-shield-fill me-1"></i>Admin
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-person-fill me-1"></i>Utilisateur
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->is_active ?? true ? 'bg-success' : 'bg-danger' }}">
                                            <i class="bi bi-{{ $user->is_active ?? true ? 'check-circle' : 'x-circle' }}"></i>
                                            {{ $user->is_active ?? true ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.utilisateurs.edit', $user) }}" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-{{ $user->is_active ?? true ? 'secondary' : 'success' }} btn-sm" 
                                                    onclick="AdminActionComponents.confirmStatusToggle({{ $user->id }}, '{{ $user->name }}', '{{ $user->is_active ?? true ? 'true' : 'false' }}')"
                                                    data-bs-toggle="tooltip" title="{{ $user->is_active ?? true ? 'Désactiver' : 'Activer' }} l'utilisateur">
                                                <i class="bi bi-toggle-{{ $user->is_active ?? true ? 'on' : 'off' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="AdminActionComponents.confirmDelete({{ $user->id }}, '{{ $user->name }}', 'utilisateurs')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                            <h5>Aucun utilisateur disponible</h5>
                                            <p>Commencez par ajouter votre premier utilisateur.</p>
                                            <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Ajouter un utilisateur
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Gestion des Réservations')


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de chargement de page
    showSuccessModal('📅 Gestion des réservations chargée');

    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// === FONCTIONS DE MODALES PERSONNALISÉES ===

/**
 * Afficher une modal de confirmation personnalisée
 */
function showConfirmModal(title, message, details, onConfirm, confirmButtonText = 'Confirmer', confirmButtonClass = 'btn-primary') {
    document.getElementById('confirmationModalLabel').innerHTML = title;
    document.getElementById('confirmationMessage').textContent = message;
    document.getElementById('confirmationDetails').textContent = details || '';

    const confirmBtn = document.getElementById('confirmationConfirm');
    confirmBtn.textContent = confirmButtonText;
    confirmBtn.className = `btn ${confirmButtonClass}`;

    // Supprimer les anciens événements
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

    // Ajouter le nouvel événement
    newConfirmBtn.addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
        modal.hide();
        onConfirm();
    });

    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();
}

/**
 * Afficher une modal de succès
 */
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();

    // Auto-fermer après 3 secondes
    setTimeout(() => {
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('successModal'));
        if (modalInstance) modalInstance.hide();
    }, 3000);
}

/**
 * Afficher une modal d'erreur
 */
function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

/**
 * Afficher/masquer la modal de chargement
 */
function showLoadingModal(message = 'Opération en cours...') {
    document.getElementById('loadingMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoadingModal() {
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modalInstance) modalInstance.hide();
}

// === FONCTIONS POUR LES RÉSERVATIONS (AVEC MODALES) ===

/**
 * Confirmer une réservation
 */
function confirmReservation(id, clientName) {
    showConfirmModal(
        '<i class="bi bi-check-circle text-success me-2"></i>Confirmation de réservation',
        `Confirmer la réservation de ${clientName} ?`,
        'Cette action changera le statut de la réservation en "Confirmée".',
        function() {
            showLoadingModal('Confirmation en cours...');

            fetch(`/admin/reservations/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ statut: 'confirmee' })
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showSuccessModal(`✅ Réservation de ${clientName} confirmée avec succès !`);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showErrorModal(data.message || 'Erreur lors de la confirmation de la réservation');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Erreur:', error);
                showErrorModal('Une erreur de connexion s\'est produite');
            });
        },
        'Confirmer',
        'btn-success'
    );
}

/**
 * Annuler une réservation
 */
function cancelReservation(id, clientName) {
    showConfirmModal(
        '<i class="bi bi-x-circle text-warning me-2"></i>Annulation de réservation',
        `Annuler la réservation de ${clientName} ?`,
        'Le client devra être informé de cette annulation. Cette action changera le statut en "Annulée".',
        function() {
            showLoadingModal('Annulation en cours...');

            fetch(`/admin/reservations/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ statut: 'annulee' })
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showSuccessModal(`❌ Réservation de ${clientName} annulée avec succès !`);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showErrorModal('Erreur lors de l\'annulation de la réservation');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Erreur:', error);
                showErrorModal('Une erreur de connexion s\'est produite');
            });
        },
        'Annuler la réservation',
        'btn-warning'
    );
}

/**
 * Supprimer une réservation
 */
function deleteReservation(id, clientName) {
    showConfirmModal(
        '<i class="bi bi-trash text-danger me-2"></i>Suppression définitive',
        `Supprimer définitivement la réservation de ${clientName} ?`,
        '⚠️ ATTENTION : Cette action est irréversible. Toutes les données seront perdues définitivement.',
        function() {
            showLoadingModal('Suppression en cours...');

            fetch(`/admin/reservations/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    // ✅ LIGNE AJOUTÉE POUR IDENTIFIER LA REQUÊTE COMME AJAX
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                let data;
                try {
                    // Tente de lire la réponse comme du JSON
                    data = await response.json();
                } catch (e) {
                    // Si ce n'est pas du JSON, on continue avec un objet vide
                    data = {};
                }

                hideLoadingModal();

                // On vérifie que la requête a réussi ET que le JSON contient "success: true"
                if (response.ok && data.success) {
                    showSuccessModal(data.message || `🗑️ Réservation de ${clientName} supprimée avec succès !`);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showErrorModal(data.message || 'Erreur lors de la suppression de la réservation');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Erreur:', error);
                showErrorModal('Une erreur de connexion s\'est produite');
            });
        },
        'Supprimer définitivement',
        'btn-danger'
    );
}

// === FONCTIONS D'EXPORTATION (AVEC MODALES) ===

/**
 * Exporter les réservations
 */
function exportReservations(format) {
    const formatNames = {
        'pdf': 'PDF',
        'excel': 'Excel',
        'stats': 'Statistiques'
    };

    showConfirmModal(
        '<i class="bi bi-download text-primary me-2"></i>Exportation des données',
        `Exporter les réservations en format ${formatNames[format]} ?`,
        'Les filtres actuellement appliqués seront pris en compte dans l\'export.',
        function() {
            showLoadingModal(`Export ${formatNames[format]} en cours...`);

            // Récupérer les paramètres de filtrage actuels
            const searchParams = new URLSearchParams(window.location.search);

            let exportUrl = `/admin/reservations/export/${format}`;

            // Ajouter les paramètres de filtre à l'URL d'export
            if (searchParams.toString()) {
                exportUrl += '?' + searchParams.toString();
            }

            // Créer un lien temporaire pour déclencher le téléchargement
            const link = document.createElement('a');
            link.href = exportUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Simuler un délai de téléchargement
            setTimeout(() => {
                hideLoadingModal();
                showSuccessModal(`✅ Export ${formatNames[format]} terminé avec succès !`);
            }, 2000);
        },
        'Exporter maintenant',
        'btn-primary'
    );
}

// === FONCTIONS POUR LES CHECKBOXES ===

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

// === GESTION DU FORMULAIRE D'ACTIONS GROUPÉES (AVEC MODALES) ===

document.getElementById('bulk-actions-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkAction = document.getElementById('bulk-action').value;

    if (checkedItems.length === 0) {
        showErrorModal('Veuillez sélectionner au moins un élément');
        return;
    }

    if (!bulkAction) {
        showErrorModal('Veuillez choisir une action à effectuer');
        return;
    }

    const itemIds = Array.from(checkedItems).map(cb => cb.value);
    const actionText = {
        'confirm': 'confirmer',
        'cancel': 'annuler',
        'delete': 'supprimer'
    }[bulkAction];

    const actionIcons = {
        'confirm': '<i class="bi bi-check-circle text-success me-2"></i>',
        'cancel': '<i class="bi bi-x-circle text-warning me-2"></i>',
        'delete': '<i class="bi bi-trash text-danger me-2"></i>'
    };

    showConfirmModal(
        `${actionIcons[bulkAction]}Action groupée`,
        `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} ${itemIds.length} réservation(s) sélectionnée(s) ?`,
        bulkAction === 'delete' ? '⚠️ ATTENTION : Cette action est irréversible pour la suppression.' : '',
        function() {
            showLoadingModal(`${actionText.charAt(0).toUpperCase() + actionText.slice(1)} en cours...`);

            // Soumettre l'action groupée
            fetch('/admin/reservations/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: bulkAction,
                    ids: itemIds
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showSuccessModal(`✅ ${data.message}`);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showErrorModal('Erreur lors de l\'action groupée');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Erreur:', error);
                showErrorModal('Une erreur de connexion s\'est produite');
            });
        },
        actionText.charAt(0).toUpperCase() + actionText.slice(1),
        bulkAction === 'delete' ? 'btn-danger' : (bulkAction === 'confirm' ? 'btn-success' : 'btn-warning')
    );
});
</script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-calendar-check-fill text-primary me-2"></i>
                Gestion des Réservations
            </h1>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Exporter les réservations">
                    <i class="bi bi-download me-1"></i>Exporter
                </button>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Statistiques">
                    <i class="bi bi-graph-up me-1"></i>Stats
                </button>
            </div>
        </div>

        <!-- Formulaire de Recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-funnel me-2"></i>Filtres de recherche
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reservations.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label for="search" class="form-label">Rechercher (Nom, Email...)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Entrez un nom...">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="date" class="form-label">Date d'arrivée</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select">
                                <option value="all" @selected(request('statut') == 'all')>Tous les statuts</option>
                                @foreach(\App\Models\Reservation::STATUTS as $value => $label)
                                    <option value="{{ $value }}" @selected(request('statut') == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-2 d-flex">
                            <button type="submit" class="btn btn-primary w-100 me-2">
                                <i class="bi bi-funnel-fill me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Réservations</h5>
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
                                <option value="confirm">Confirmer sélectionnées</option>
                                <option value="cancel">Annuler sélectionnées</option>
                                <option value="delete">Supprimer sélectionnées</option>
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
                                <th scope="col">Client</th>
                                <th scope="col">Chambre</th>
                                <th scope="col">Dates du séjour</th>
                                <th scope="col">Prix Total</th>
                                <th scope="col">Statut</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $reservation->id }}" id="item_{{ $reservation->id }}">
                                            <label class="form-check-label" for="item_{{ $reservation->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($reservation->client_nom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $reservation->client_nom }} {{ $reservation->client_prenom }}</div>
                                                <div class="small text-muted">
                                                    <i class="bi bi-envelope me-1"></i>{{ $reservation->client_email }}
                                                </div>
                                                @if($reservation->client_telephone)
                                                    <div class="small text-muted">
                                                        <i class="bi bi-telephone me-1"></i>{{ $reservation->client_telephone }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $reservation->chambre->nom ?? 'Chambre supprimée' }}</span>
                                        @if($reservation->chambre)
                                            <br><small class="text-muted">{{ $reservation->chambre->capacite }} pers.</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            <i class="bi bi-calendar-event text-primary me-1"></i>
                                            <strong>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</strong>
                                        </div>
                                        <div class="text-nowrap">
                                            <i class="bi bi-calendar-x text-danger me-1"></i>
                                            <strong>{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</strong>
                                        </div>
                                        <small class="text-muted">
                                            ({{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }} nuits)
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                    <td>
                                        <span class="badge fs-6
                                            @if($reservation->statut == 'confirmee') bg-success
                                            @elseif($reservation->statut == 'en_attente') bg-warning text-dark
                                            @elseif($reservation->statut == 'terminee') bg-secondary
                                            @else bg-danger @endif">
                                            <i class="bi bi-{{ $reservation->statut == 'confirmee' ? 'check-circle' : ($reservation->statut == 'en_attente' ? 'clock' : 'x-circle') }} me-1"></i>
                                            {{ $reservation->statut_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <!-- Bouton Fiche de Police -->
                                            <a href="{{ route('admin.reservations.police_form', $reservation) }}"
                                               class="btn btn-outline-info btn-sm"
                                               target="_blank"
                                               data-bs-toggle="tooltip"
                                               title="Imprimer Fiche de Police">
                                                <i class="bi bi-printer"></i>
                                            </a>

                                            <!-- Bouton Confirmer (affiché seulement si pas confirmée) -->
                                            @if($reservation->statut !== 'confirmee')
                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="confirmReservation({{ $reservation->id }}, @js($reservation->client_nom.' '.$reservation->client_prenom))"
                                                        data-bs-toggle="tooltip" title="Confirmer la réservation">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            @endif

                                            <!-- Bouton Annuler (affiché seulement si pas annulée) -->
                                            @if($reservation->statut !== 'annulee')
                                                <button type="button" class="btn btn-outline-warning btn-sm"
                                                        onclick="cancelReservation({{ $reservation->id }}, @js($reservation->client_nom.' '.$reservation->client_prenom))"
                                                        data-bs-toggle="tooltip" title="Annuler la réservation">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @endif

                                            <!-- Bouton Supprimer (toujours affiché) -->
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteReservation({{ $reservation->id }}, @js($reservation->client_nom.' '.$reservation->client_prenom))"
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
                                            <i class="bi bi-calendar-x fs-1 mb-3 d-block"></i>
                                            <h5>Aucune réservation trouvée</h5>
                                            <p>Aucune réservation ne correspond aux critères sélectionnés.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reservations->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

<!-- Modal de Confirmation Générique -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">
                    <i class="bi bi-question-circle text-warning me-2"></i>Confirmation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i id="confirmationIcon" class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p id="confirmationMessage" class="mb-0">Êtes-vous sûr de vouloir effectuer cette action ?</p>
                        <small id="confirmationDetails" class="text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annuler
                </button>
                <button type="button" id="confirmationConfirm" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Succès -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="bi bi-check-circle me-2"></i>Succès
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                <p id="successMessage" class="mb-0">Opération réussie !</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'Erreur -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Erreur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-x-circle-fill text-danger fs-1 mb-3"></i>
                <p id="errorMessage" class="mb-0">Une erreur s'est produite !</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Chargement -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p id="loadingMessage" class="mb-0">Opération en cours...</p>
            </div>
        </div>
    </div>
</div>

@endsection

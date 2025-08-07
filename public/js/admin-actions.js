// Composant pour les actions admin avec confirmations élégantes
class AdminActionComponents {

    static initializeDeleteButtons() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const itemName = this.dataset.itemName || 'cet élément';
                const form = this.closest('form');

                AdminActionComponents.confirmDelete(itemName, () => {
                    window.notify.info('⏳ Suppression en cours...', { duration: 2000 });
                    if (form) form.submit();
                });
            });
        });
    }

static confirmStatusToggle(id, name, currentStatus) {
    const newStatus = !currentStatus;
    const actionText = newStatus ? 'rendre disponible' : 'rendre indisponible';
    const statusIcon = newStatus ? '✅' : '❌';

    const modal = AdminActionComponents.createModal({
        title: `${statusIcon} Changer le statut`,
        body: `
            <div class="text-center">
                <i class="bi bi-toggle-${newStatus ? 'on' : 'off'} text-${newStatus ? 'success' : 'warning'}" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Êtes-vous sûr de vouloir ${actionText} la chambre "${name}" ?</h5>
                <p class="text-muted">Cette action changera immédiatement la disponibilité de la chambre.</p>
            </div>
        `,
        confirmText: actionText.charAt(0).toUpperCase() + actionText.slice(1),
        confirmClass: newStatus ? 'btn-success' : 'btn-warning',
        onConfirm: () => {
            window.notify.info('⏳ Mise à jour du statut...', { duration: 2000 });

            fetch(`/admin/chambres/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.notify.success(data.message, { duration: 5000 });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.notify.error('❌ Erreur lors de la mise à jour du statut');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                window.notify.error('❌ Une erreur s\'est produite');
            });
        }
    });

    modal.show();
}

    static initializeEditButtons() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const itemName = this.dataset.itemName || 'élément';
                window.notify.info(`✏️ Modification de "${itemName}"`, { duration: 3000 });
            });
        });
    }

    static initializeStatusToggleButtons() {
        document.querySelectorAll('.status-toggle-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const itemName = this.dataset.itemName || 'cet élément';
                const currentStatus = this.dataset.currentStatus || 'actif';
                const newStatus = currentStatus === 'actif' ? 'inactif' : 'actif';
                const form = this.closest('form');

                AdminActionComponents.confirmStatusToggle(itemName, currentStatus, newStatus, () => {
                    window.notify.info(`⏳ Changement de statut en cours...`, { duration: 2000 });
                    if (form) form.submit();
                });
            });
        });
    }

    static initializeSaveButtons() {
        document.querySelectorAll('.save-btn, form').forEach(element => {
            if (element.tagName === 'FORM') {
                element.addEventListener('submit', function(e) {
                    const isUpdate = this.dataset.isUpdate === 'true';
                    const itemName = this.dataset.itemName || 'élément';

                    if (isUpdate) {
                        window.notify.warning(`💾 Sauvegarde des modifications de "${itemName}"...`, { duration: 3000 });
                    } else {
                        window.notify.success(`➕ Création de "${itemName}" en cours...`, { duration: 3000 });
                    }
                });
            } else {
                element.addEventListener('click', function(e) {
                    const itemName = this.dataset.itemName || 'élément';
                    window.notify.success(`💾 Sauvegarde de "${itemName}" en cours...`, { duration: 3000 });
                });
            }
        });
    }

    static initializeExportButtons() {
        document.querySelectorAll('.export-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const exportType = this.dataset.exportType || 'données';
                window.notify.info(`📊 Export ${exportType} en cours...`, { duration: 4000 });

                // Simuler un délai d'export
                setTimeout(() => {
                    window.notify.success(`✅ Export ${exportType} terminé !`, { duration: 3000 });
                }, 2000);
            });
        });
    }

    /**
     * Confirmation de suppression - Méthode unifiée
     * @param {number|string} itemIdOrName - ID de l'élément ou nom si callback fourni
     * @param {string} itemNameOrCallback - Nom de l'élément ou callback si 2 paramètres
     * @param {string} route - Route optionnelle pour suppression automatique
     */
    static confirmDelete(itemIdOrName, itemNameOrCallback, route) {
        // Si 3 paramètres : (id, name, route) - suppression automatique
        if (arguments.length === 3 && route) {
            return this.confirmDeleteWithRoute(itemIdOrName, itemNameOrCallback, route);
        }

        // Si 2 paramètres : (name, callback) - suppression avec callback
        if (arguments.length === 2 && typeof itemNameOrCallback === 'function') {
            return this.confirmDeleteWithCallback(itemIdOrName, itemNameOrCallback);
        }

        // Fallback vers callback
        return this.confirmDeleteWithCallback(itemIdOrName, itemNameOrCallback);
    }

    /**
     * Confirmation de suppression avec route automatique
     */
    static confirmDeleteWithRoute(itemId, itemName, route) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-trash3-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h6>Êtes-vous sûr de vouloir supprimer</h6>
                        <p class="text-muted">"<strong>${itemName}</strong>" ?</p>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Cette action est irréversible !
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="bi bi-trash3 me-2"></i>Supprimer définitivement
                        </button>
                    </div>
                </div>
            </div>
        `;

        AdminActionComponents.showModal(modal, '#confirmDeleteBtn', () => {
            // Créer et soumettre un formulaire de suppression
            window.notify.info('⏳ Suppression en cours...', { duration: 3000 });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/${route}/${itemId}`;
            form.style.display = 'none';

            // Token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            || document.querySelector('input[name="_token"]')?.value;

            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            document.body.appendChild(form);
            form.submit();
        });
    }

    /**
     * Confirmation de suppression avec callback
     */
    static confirmDeleteWithCallback(itemName, callback) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-trash3-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h6>Êtes-vous sûr de vouloir supprimer</h6>
                        <p class="text-muted">"<strong>${itemName}</strong>" ?</p>
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            Cette action est irréversible !
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="bi bi-trash3 me-2"></i>Oui, Supprimer
                        </button>
                    </div>
                </div>
            </div>
        `;

        AdminActionComponents.showModal(modal, '#confirmDeleteBtn', callback);
    }

    /**
     * Confirmation de changement de statut (compatible avec 3 paramètres)
     * @param {number} itemId - ID de l'élément
     * @param {string} itemName - Nom de l'élément
     * @param {string} currentStatus - Statut actuel
     */
    static confirmStatusToggle(itemId, itemName, currentStatus) {
        // Déterminer le nouveau statut
        const isCurrentlyActive = currentStatus === 'actif' || currentStatus === 'true' || currentStatus === true;
        const isActivating = !isCurrentlyActive;

        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header ${isActivating ? 'bg-success' : 'bg-warning'} text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-toggle-${isActivating ? 'on' : 'off'} me-2"></i>
                            ${isActivating ? 'Activer' : 'Désactiver'} la chambre
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-toggle-${isActivating ? 'on' : 'off'} ${isActivating ? 'text-success' : 'text-warning'}" style="font-size: 3rem;"></i>
                        </div>
                        <h6>Voulez-vous ${isActivating ? 'rendre disponible' : 'rendre indisponible'}</h6>
                        <p class="text-muted">la chambre "<strong>${itemName}</strong>" ?</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            ${isActivating ? 'La chambre sera visible pour les réservations' : 'La chambre sera masquée des réservations'}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn ${isActivating ? 'btn-success' : 'btn-warning'}" id="confirmStatusBtn">
                            <i class="bi bi-toggle-${isActivating ? 'on' : 'off'} me-2"></i>${isActivating ? 'Rendre disponible' : 'Rendre indisponible'}
                        </button>
                    </div>
                </div>
            </div>
        `;

        AdminActionComponents.showModal(modal, '#confirmStatusBtn', () => {
            // Appel AJAX pour changer le statut
            window.notify.info(`⏳ ${isActivating ? 'Activation' : 'Désactivation'} en cours...`, { duration: 3000 });

            // Obtenir le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(`/admin/chambres/${itemId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.notify.success(data.message, { duration: 4000 });
                    // Recharger la page pour refléter les changements
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    window.notify.error('❌ Erreur lors du changement de statut', { duration: 4000 });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                window.notify.error('❌ Erreur de connexion', { duration: 4000 });
            });
        });
    }

    static confirmModification(itemName, callback) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>
                            Confirmer la modification
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-pencil-square text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6>Sauvegarder les modifications de</h6>
                        <p class="text-muted">"<strong>${itemName}</strong>" ?</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Les modifications seront appliquées immédiatement
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-primary" id="confirmModifyBtn">
                            <i class="bi bi-check-circle me-2"></i>Sauvegarder
                        </button>
                    </div>
                </div>
            </div>
        `;

        AdminActionComponents.showModal(modal, '#confirmModifyBtn', callback);
    }

    static showModal(modal, confirmButtonSelector, callback) {
        document.body.appendChild(modal);

        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        // Gérer la confirmation
        modal.querySelector(confirmButtonSelector).addEventListener('click', () => {
            bootstrapModal.hide();
            callback();
        });

        // Nettoyer après fermeture
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }

    static initializeBulkActions() {
        const bulkForm = document.querySelector('#bulk-actions-form');
        if (!bulkForm) return;

        const checkboxes = bulkForm.querySelectorAll('input[type="checkbox"][name="selected[]"]');
        const bulkActionSelect = bulkForm.querySelector('#bulk-action');
        const bulkSubmitBtn = bulkForm.querySelector('#bulk-submit');

        // Surveiller les sélections
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const selected = bulkForm.querySelectorAll('input[type="checkbox"][name="selected[]"]:checked');
                bulkSubmitBtn.disabled = selected.length === 0;

                if (selected.length > 0) {
                    window.notify.info(`${selected.length} élément(s) sélectionné(s)`, { duration: 2000 });
                }
            });
        });

        // Gérer la soumission des actions groupées
        bulkForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const selected = this.querySelectorAll('input[type="checkbox"][name="selected[]"]:checked');
            const action = bulkActionSelect.value;

            if (selected.length === 0) {
                window.notify.warning('⚠️ Veuillez sélectionner au moins un élément');
                return;
            }

            if (!action) {
                window.notify.warning('⚠️ Veuillez choisir une action');
                return;
            }

            if (action === 'delete') {
                AdminActionComponents.confirmBulkDelete(selected.length, () => {
                    AdminNotifications.showBulkAction(selected.length, 'supprimé(s)');
                    this.submit();
                });
            } else {
                AdminNotifications.showBulkAction(selected.length, action);
                this.submit();
            }
        });
    }

    static confirmBulkDelete(count, callback) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Suppression groupée
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-trash3-fill text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h6>Confirmer la suppression de</h6>
                        <p class="h4 text-warning">${count} élément(s)</p>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Cette action supprimera définitivement tous les éléments sélectionnés !
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn">
                            <i class="bi bi-trash3 me-2"></i>Supprimer ${count} élément(s)
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        modal.querySelector('#confirmBulkDeleteBtn').addEventListener('click', () => {
            bootstrapModal.hide();
            callback();
        });

        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }

    static initializeQuickActions() {
        document.querySelectorAll('.quick-action').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const itemName = this.dataset.itemName || 'élément';

                AdminNotifications.showQuickAction(`${action} pour ${itemName}`);
            });
        });
    }

    /**
     * Confirmation d'actions groupées sophistiquée
     * @param {Array} itemIds - IDs des éléments sélectionnés
     * @param {string} actionText - Texte de l'action (activer, désactiver, supprimer)
     * @param {string} actionType - Type d'action (activate, deactivate, delete)
     */
    static confirmBulkAction(itemIds, actionText, actionType) {
        const count = itemIds.length;
        const isDelete = actionType === 'delete';

        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-${isDelete ? 'danger' : 'warning'} text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-${isDelete ? 'exclamation-triangle-fill' : 'lightning-charge-fill'} me-2"></i>
                            Action groupée - ${actionText}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-4">
                            <i class="bi bi-${isDelete ? 'trash3-fill text-danger' : 'lightning-charge-fill text-warning'}" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="mb-3">Confirmer l'action groupée</h5>
                        <p class="mb-3">
                            Vous êtes sur le point de <strong class="${isDelete ? 'text-danger' : 'text-primary'}">${actionText}</strong>
                            <span class="badge bg-primary fs-6">${count}</span> élément${count > 1 ? 's' : ''}.
                        </p>
                        ${isDelete ? `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Cette action est irréversible !
                            </div>
                        ` : ''}
                        <div class="bg-light p-3 rounded mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                IDs sélectionnés : ${itemIds.join(', ')}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-${isDelete ? 'danger' : 'warning'}" id="confirmBulkActionBtn">
                            <i class="bi bi-${isDelete ? 'trash3' : 'lightning-charge'} me-2"></i>
                            ${isDelete ? 'Supprimer définitivement' : actionText.charAt(0).toUpperCase() + actionText.slice(1)}
                        </button>
                    </div>
                </div>
            </div>
        `;

        AdminActionComponents.showModal(modal, '#confirmBulkActionBtn', () => {
            // Notifier le début de l'action
            window.notify.info(`⏳ ${actionText.charAt(0).toUpperCase() + actionText.slice(1)} en cours...`, { duration: 4000 });

            // Simuler l'action (remplacer par la vraie requête)
            setTimeout(() => {
                if (isDelete) {
                    window.notify.success(`🗑️ ${count} élément${count > 1 ? 's' : ''} supprimé${count > 1 ? 's' : ''} avec succès !`, { duration: 5000 });
                } else {
                    window.notify.success(`✅ ${count} élément${count > 1 ? 's' : ''} ${actionText}${count > 1 ? 's' : ''} avec succès !`, { duration: 5000 });
                }

                // Recharger la page après un délai
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }, 2000);
        });
    }

    static initializeExportButtons() {
        document.querySelectorAll('.export-btn, [data-export-type]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const exportType = this.dataset.exportType || 'PDF';
                const itemName = this.dataset.itemName || 'données';

                window.notify.info(`📄 Export ${exportType} en cours...`, { duration: 4000 });

                // Simuler l'export après un délai
                setTimeout(() => {
                    window.notify.success(`✅ Export ${exportType} de ${itemName} terminé!`, { duration: 3000 });
                }, 2000);
            });
        });
    }

    static initialize() {
        this.initializeDeleteButtons();
        this.initializeEditButtons();
        this.initializeStatusToggleButtons();
        this.initializeSaveButtons();
        this.initializeExportButtons();
        this.initializeBulkActions();
        this.initializeQuickActions();

        // Notification d'initialisation
        window.notify.info('⚡ Système d\'actions administrateur activé', { duration: 3000 });
    }
}

/**
 * Composants d'actions pour l'administration
 */
window.AdminActionComponents = {
    /**
     * Confirmation de suppression d'un élément
     */
    confirmDelete(id, name, type) {
        const typeNames = {
            'chambres': 'chambre',
            'reservations': 'réservation',
            'users': 'utilisateur',
            'categories': 'catégorie'
        };

        const typeName = typeNames[type] || 'élément';

        if (confirm(`⚠️ Êtes-vous sûr de vouloir supprimer ${typeName} "${name}" ?\n\nCette action est irréversible.`)) {
            // Créer et soumettre un formulaire de suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/${type}/${id}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            document.body.appendChild(form);
            form.submit();
        }
    },

    /**
     * Confirmation de changement de statut
     */
    confirmStatusToggle(id, name, currentStatus) {
        const isCurrentlyAvailable = currentStatus === 'true' || currentStatus === true;
        const newStatus = isCurrentlyAvailable ? 'indisponible' : 'disponible';
        const action = isCurrentlyAvailable ? 'désactiver' : 'activer';

        if (confirm(`🔄 Voulez-vous ${action} "${name}" ?\n\nStatut actuel: ${isCurrentlyAvailable ? 'Disponible' : 'Indisponible'}\nNouveau statut: ${newStatus}`)) {
            // Faire un appel AJAX pour changer le statut
            fetch(`/admin/chambres/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.notify.success(data.message);
                    // Recharger la page après un court délai
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.notify.error('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                window.notify.error('Erreur de connexion');
                console.error('Error:', error);
            });
        }
    },

    /**
     * Actions groupées avec confirmation
     */
    confirmBulkAction(itemIds, actionText, action) {
        const count = itemIds.length;

        if (confirm(`⚠️ Êtes-vous sûr de vouloir ${actionText} ${count} élément(s) sélectionné(s) ?\n\nCette action peut être irréversible.`)) {
            // Soumettre l'action groupée
            const form = document.getElementById('bulk-actions-form');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'ids';
            actionInput.value = JSON.stringify(itemIds);

            form.appendChild(actionInput);

            // Changer l'action du formulaire selon le contexte
            const currentPath = window.location.pathname;
            if (currentPath.includes('reservations')) {
                form.action = '/admin/reservations/bulk-action';
            } else if (currentPath.includes('chambres')) {
                form.action = '/admin/chambres/bulk-action';
            }

            form.submit();
        }
    }
};

// Auto-initialisation
document.addEventListener('DOMContentLoaded', () => {
    AdminActionComponents.initialize();
});

// Rendre disponible globalement
window.AdminActionComponents = AdminActionComponents;

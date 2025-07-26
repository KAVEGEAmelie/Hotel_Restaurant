@extends('layouts.admin')
@section('title', 'Gestion des Utilisateurs')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestion des Utilisateurs</h1>
            <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter un utilisateur
            </a>
        </div>
        {{-- ... (Message de succès/erreur) ... --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-primary">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">Utilisateur</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.utilisateurs.edit', $user) }}" class="btn btn-sm btn-primary">Modifier</a>
                                    {{-- ... (Formulaire de suppression) ... --}}
                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
        data-bs-toggle="modal"
        data-bs-target="#deleteConfirmationModal"
        data-action="{{ route('admin.utilisateurs.destroy', $user) }}">
    Supprimer
</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
{{-- ... fin de votre @endsection ... --}}

<!-- ======= Fenêtre Modale de Confirmation de Suppression ======= -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de Suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous absolument sûr de vouloir supprimer cet élément ?</p>
                <p class="text-danger fw-bold">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                {{-- Ce formulaire sera soumis par notre JavaScript --}}
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Oui, Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Après la modale --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteConfirmationModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                // Bouton qui a déclenché la modale
                const button = event.relatedTarget;

                // Récupère l'URL depuis l'attribut data-action du bouton
                const actionUrl = button.getAttribute('data-action');

                // Trouve le formulaire à l'intérieur de la modale
                const deleteForm = deleteModal.querySelector('#deleteForm');

                // Met à jour l'attribut 'action' du formulaire avec la bonne URL
                deleteForm.setAttribute('action', actionUrl);
            });
        }
    });
</script>
@endpush

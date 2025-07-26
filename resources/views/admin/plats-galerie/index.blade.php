@extends('layouts.admin')
@section('title', 'Plats en Galerie')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Plats en Galerie</h1>
            <a href="{{ route('admin.plats-galerie.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter un plat
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Prix</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plats as $plat)
                            <tr>
                                <td>
                                    @if($plat->image)
                                        <img src="{{ asset('uploads/'.$plat->image) }}" alt="{{ $plat->nom }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $plat->nom }}</td>
                                <td>{{ Str::limit($plat->description, 50) }}</td>
                                <td>{{ number_format($plat->prix, 0, ',', ' ') }} F</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.plats-galerie.edit', $plat) }}" class="btn btn-sm btn-primary me-2">
                                        <i class="bi bi-pencil-fill"></i> Modifier
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal"
                                            data-action="{{ route('admin.plats-galerie.destroy', $plat) }}">
                                        <i class="bi bi-trash-fill"></i> Supprimer
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Aucun plat n'a été ajouté à la galerie pour le moment.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($plats->hasPages())
                    <div class="mt-4 d-flex justify-content-center">{{ $plats->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Fenêtre Modale de Confirmation de Suppression -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmation de Suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                    <p class="text-danger fw-bold">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Oui, Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteConfirmationModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const actionUrl = button.getAttribute('data-action');
            const deleteForm = deleteModal.querySelector('#deleteForm');
            deleteForm.setAttribute('action', actionUrl);
        });
    });
</script>
@endpush

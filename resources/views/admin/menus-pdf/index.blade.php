@extends('layouts.admin')

@section('title', 'Gestion des Menus (PDF/Image)')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestion des Menus</h1>
            <a href="{{ route('admin.menus-pdf.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter un Menu
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
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Un seul menu peut être "Actif" à la fois. Le menu actif est celui qui s'affichera sur le site public.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Titre</th>
                                <th scope="col">Fichier</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Ajouté le</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus_pdf as $menu)
                                <tr>
                                    <td class="fw-bold">{{ $menu->titre }}</td>
                                    <td>
                                        @if($menu->fichier)
                                            @php
                                                $extension = pathinfo($menu->fichier, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp
                                            
                                            @if($isImage)
                                                <a href="{{ asset('storage/'.$menu->fichier) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">
                                                    <i class="bi bi-eye-fill"></i> Voir l'image
                                                </a>
                                                <img src="{{ asset('storage/'.$menu->fichier) }}" alt="{{ $menu->titre }}" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;" class="border">
                                            @else
                                                <a href="{{ asset('storage/'.$menu->fichier) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i> Voir le PDF
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">Aucun fichier</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->est_actif)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.menus-pdf.edit', $menu) }}" class="btn btn-sm btn-primary me-2">
                                            <i class="bi bi-pencil-fill"></i> Modifier
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmationModal"
                                                data-action="{{ route('admin.menus-pdf.destroy', $menu) }}">
                                            <i class="bi bi-trash-fill"></i> Supprimer
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        Aucun menu n'a été ajouté pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fenêtre Modale de Confirmation de Suppression -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
        {{-- ... (Le code de la modale est identique à celui des autres pages, pas besoin de le changer) ... --}}
    </div>
@endsection

@push('scripts')
{{-- Le script pour la modale est identique à celui des autres pages --}}
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

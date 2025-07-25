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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

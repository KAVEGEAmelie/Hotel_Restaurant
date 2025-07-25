@extends('layouts.admin')
@section('title', isset($user) ? 'Modifier' : 'Ajouter un Utilisateur')
@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">{{ isset($user) ? 'Modifier : ' . $user->name : 'Ajouter un utilisateur' }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ isset($user) ? route('admin.utilisateurs.update', $user) : route('admin.utilisateurs.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                {{-- Nom, Email, Mot de passe, Confirmation mot de passe --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                </div>
                {{-- ... autres champs ... --}}

                <!-- Case à cocher pour le rôle Admin -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="isAdmin" name="is_admin" @checked(old('is_admin', $user->is_admin ?? false))>
                    <label class="form-check-label" for="isAdmin">Nommer Administrateur</label>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-success">{{ isset($user) ? 'Mettre à jour' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

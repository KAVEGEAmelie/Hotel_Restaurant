@extends('layouts.admin')

@section('title', isset($user) ? 'Modifier l\'Utilisateur' : 'Ajouter un Utilisateur')

@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">{{ isset($user) ? 'Modifier : ' . $user->name : 'Ajouter un nouvel utilisateur' }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Oups ! Erreur de validation</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($user) ? route('admin.utilisateurs.update', $user) : route('admin.utilisateurs.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
                        @if(isset($user))
                            <small class="form-text text-muted">Laissez vide pour ne pas changer.</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_admin" name="is_admin" value="1"
                           @if(old('is_admin') || (isset($user) && $user->is_admin)) checked @endif>
                    <label class="form-check-label" for="is_admin">Nommer Administrateur</label>
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

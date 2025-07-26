@extends('layouts.admin')

@section('title', 'Mon Profil')

@section('content')
    <div class="container-fluid">
        <h1 class="h2 mb-4">Mon Profil</h1>

        <div class="row">
            <div class="col-lg-6">
                {{-- Formulaire de mise à jour des informations du profil --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        Informations du Profil
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted small">
                            Mettez à jour les informations de votre compte.
                        </p>
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                {{-- Formulaire de mise à jour du mot de passe --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        Mettre à jour le Mot de Passe
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                {{-- Formulaire de suppression du compte --}}
                <div class="card shadow-sm mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        Supprimer le Compte
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
                        </p>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

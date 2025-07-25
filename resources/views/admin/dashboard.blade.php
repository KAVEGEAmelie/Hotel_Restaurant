@extends('layouts.admin')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Tableau de Bord</h1>
        <a href="{{ route('admin.chambres.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une Chambre
        </a>
    </div>

    <!-- Section des statistiques -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Chambres Totales</h5>
                            <p class="h2">{{ $stats['chambres'] }}</p>
                        </div>
                        <i class="bi bi-door-open h1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Réservations</h5>
                            <p class="h2">{{ $stats['reservations'] }}</p>
                        </div>
                        <i class="bi bi-journal-text h1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Utilisateurs</h5>
                            <p class="h2">{{ $stats['utilisateurs'] }}</p>
                        </div>
                        <i class="bi bi-people h1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des dernières réservations -->
    <div class="card">
        <div class="card-header">
            Dernières Réservations
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Dates du séjour</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dernieresReservations as $reservation)
                            <tr>
                                <td>{{ $reservation->user->name ?? $reservation->client_nom }}</td>
                                <td>{{ $reservation->chambre->nom ?? 'N/A' }}</td>
                                <td>
                                    Du {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}<br>
                                    Au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge
                                        @if($reservation->statut == 'confirmée') bg-success @elseif($reservation->statut == 'pending') bg-warning @else bg-danger @endif">
                                        {{ ucfirst($reservation->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucune réservation pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

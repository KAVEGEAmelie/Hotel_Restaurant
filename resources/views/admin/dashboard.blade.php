@extends('layouts.admin')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Tableau de Bord</h1>
        <a href="{{ route('admin.chambres.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une Chambre
        </a>
    </div>

   <!-- Section des statistiques (Design Corrigé) -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card stat-card-v2 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle">Chambres Totales</h6>
                        <p class="h2 mb-0">{{ $stats['chambres'] }}</p>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="bi bi-door-open"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card stat-card-v2 border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle">Réservations</h6>
                        <p class="h2 mb-0">{{ $stats['reservations'] }}</p>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card stat-card-v2 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle">Utilisateurs</h6>
                        <p class="h2 mb-0">{{ $stats['utilisateurs'] }}</p>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="bi bi-people"></i>
                    </div>
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

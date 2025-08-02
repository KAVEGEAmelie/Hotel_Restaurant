@extends('layouts.admin')

@section('title', 'Gestion des Réservations')

@section('content')
    <div class="container-fluid">
        <h1 class="h2 mb-4">Gestion des Réservations</h1>

        <!-- Formulaire de Recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('admin.reservations.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label for="search" class="form-label">Rechercher (Nom, Email...)</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Entrez un nom...">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="date" class="form-label">Date d'arrivée</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select">
                                <option value="all" @selected(request('statut') == 'all')>Tous</option>
                                <option value="pending" @selected(request('statut') == 'pending')>En attente</option>
                                <option value="confirmée" @selected(request('statut') == 'confirmée')>Confirmée</option>
                                <option value="annulée" @selected(request('statut') == 'annulée')>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-2 d-flex">
                            <button type="submit" class="btn btn-primary w-100 me-2">Filtrer</button>
                            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

       @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Chambre</th>
                                <th>Dates du séjour</th>
                                <th>Prix Total</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $reservation->client_nom }} {{ $reservation->client_prenom }}</div>
                                        <div class="small text-muted">{{ $reservation->client_email }}</div>
                                    </td>
                                    <td>{{ $reservation->chambre->nom ?? 'Chambre supprimée' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge
                                            @if($reservation->statut == 'confirmée') bg-success @elseif($reservation->statut == 'pending') bg-warning text-dark @else bg-danger @endif">
                                            {{ ucfirst($reservation->statut) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                    <a href="{{ route('admin.reservations.police_form', $reservation) }}" class="btn btn-sm btn-info text-white" title="Imprimer Fiche de Police" target="_blank">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                        <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucune réservation trouvée pour les critères sélectionnés.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reservations->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

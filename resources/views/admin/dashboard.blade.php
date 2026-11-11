@extends('layouts.admin')

@section('title', 'Tableau de Bord')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification de bienvenue juste après la connexion
    @if(session('success'))
        setTimeout(() => {
            AdminNotifications.showUserLogin({ name: @js(Auth::user()->name) });
        }, 500);
    @endif
});
</script>
@endpush

@php
    $badgeStatut = fn ($statut) => match ($statut) {
        'confirmee' => 'bg-success',
        'en_attente' => 'bg-warning text-dark',
        'terminee' => 'bg-secondary',
        default => 'bg-danger',
    };
    $badgePaiement = fn ($statut) => match ($statut) {
        'paye' => ['bg-success', 'Payé'],
        'echec' => ['bg-danger', 'Échec'],
        'rembourse' => ['bg-secondary', 'Remboursé'],
        default => ['bg-light text-dark border', 'En attente'],
    };
@endphp

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h2 mb-0">Tableau de Bord</h1>
            <small class="text-muted">{{ ucfirst(now()->locale('fr')->translatedFormat('l j F Y')) }}</small>
        </div>
        <a href="{{ route('admin.chambres.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une Chambre
        </a>
    </div>

    {{-- Alerte : paiements reçus sur des réservations annulées --}}
    @if($aRembourser->isNotEmpty())
        <div class="alert alert-danger">
            <h5 class="alert-heading"><i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $aRembourser->count() }} remboursement(s) à effectuer</h5>
            <p class="mb-2">Ces clients ont payé après l'expiration de leur réservation, et la chambre avait déjà été reprise.</p>
            <ul class="mb-0">
                @foreach($aRembourser as $r)
                    <li>
                        <strong>{{ $r->client_prenom }} {{ $r->client_nom }}</strong>
                        ({{ $r->client_telephone }}) :
                        {{ number_format($r->montant_paye, 0, ',', ' ') }} FCFA,
                        {{ $r->chambre->nom ?? 'chambre supprimée' }},
                        transaction {{ $r->transaction_id }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Indicateurs --}}
    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card stat-card-v2 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Occupation ce soir</h6>
                            <p class="h2 mb-0">{{ $stats['taux_occupation'] }} %</p>
                            <small class="text-muted">{{ $stats['chambres_occupees'] }} / {{ $stats['total_chambres'] }} chambres</small>
                        </div>
                        <div class="stat-icon icon-success"><i class="bi bi-door-closed"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card stat-card-v2 border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Arrivées / départs du jour</h6>
                            <p class="h2 mb-0">{{ $arrivees->count() }} / {{ $departs->count() }}</p>
                            <small class="text-muted">voir le détail ci-dessous</small>
                        </div>
                        <div class="stat-icon icon-info"><i class="bi bi-arrow-left-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card stat-card-v2 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-subtitle">Encaissé ce mois</h6>
                            <p class="h3 mb-0">{{ number_format($stats['chiffre_affaires_mois'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></p>
                            <small class="text-muted">paiements CinetPay confirmés</small>
                        </div>
                        <div class="stat-icon icon-success"><i class="bi bi-cash-stack"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <a href="{{ route('admin.reservations.index', ['statut' => 'en_attente']) }}" class="text-decoration-none text-reset">
                <div class="card stat-card-v2 border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-subtitle">En attente de paiement</h6>
                                <p class="h2 mb-0">{{ $stats['en_attente_paiement'] }}</p>
                                <small class="text-muted">annulées après 2 h sans paiement</small>
                            </div>
                            <div class="stat-icon icon-warning"><i class="bi bi-hourglass-split"></i></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Arrivées et départs du jour --}}
    <div class="row">
        @foreach([['Arrivées du jour', 'bi-box-arrow-in-right', $arrivees, 'Aucune arrivée prévue aujourd\'hui.'], ['Départs du jour', 'bi-box-arrow-right', $departs, 'Aucun départ prévu aujourd\'hui.']] as [$titre, $icone, $liste, $vide])
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header"><i class="bi {{ $icone }} me-2"></i>{{ $titre }}</div>
                    <ul class="list-group list-group-flush">
                        @forelse($liste as $r)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                                <div>
                                    <strong>{{ $r->client_prenom }} {{ $r->client_nom }}</strong>
                                    <div class="small text-muted">
                                        {{ $r->chambre->nom ?? 'N/A' }} · {{ $r->nombre_invites }} pers. · {{ $r->client_telephone }}
                                    </div>
                                </div>
                                @php [$classe, $libelle] = $badgePaiement($r->statut_paiement); @endphp
                                <span class="badge {{ $classe }}">{{ $libelle }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">{{ $vide }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Dernières réservations --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Dernières Réservations</span>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-primary">Tout voir</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Dates du séjour</th>
                            <th class="text-end">Montant</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dernieresReservations as $reservation)
                            <tr>
                                <td>
                                    {{ $reservation->client_prenom }} {{ $reservation->client_nom }}
                                    <div class="small text-muted">{{ $reservation->client_telephone }}</div>
                                </td>
                                <td>{{ $reservation->chambre->nom ?? 'N/A' }}</td>
                                <td>
                                    Du {{ $reservation->check_in_date->format('d/m/Y') }}<br>
                                    Au {{ $reservation->check_out_date->format('d/m/Y') }}
                                </td>
                                <td class="text-end text-nowrap">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</td>
                                <td><span class="badge {{ $badgeStatut($reservation->statut) }}">{{ $reservation->statut_label }}</span></td>
                                <td>
                                    @php [$classe, $libelle] = $badgePaiement($reservation->statut_paiement); @endphp
                                    <span class="badge {{ $classe }}">{{ $libelle }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune réservation pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

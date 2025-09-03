<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Réservations - {{ $date_export->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 18px;
            margin: 0 0 5px 0;
        }
        
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        
        .export-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }
        
        .export-info h3 {
            margin: 0 0 8px 0;
            color: #28a745;
            font-size: 14px;
        }
        
        .filters {
            background: #fff3cd;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #ffeaa7;
        }
        
        .filters h4 {
            margin: 0 0 5px 0;
            color: #856404;
            font-size: 12px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
        }
        
        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        .table td {
            font-size: 8px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; }
        .badge-primary { background-color: #007bff; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .summary {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .summary h4 {
            margin: 0 0 8px 0;
            color: #495057;
            font-size: 12px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }
        
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
        }
        
        .summary-label {
            font-size: 8px;
            color: #666;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 5px;
            background: white;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>🏨 HÔTEL RESTAURANT LE PRINTEMPS</h1>
        <p>Export des Réservations - {{ $date_export->format('d/m/Y à H:i') }}</p>
        <p>Généré par : {{ $admin_name }}</p>
    </div>

    <!-- Informations d'export -->
    <div class="export-info">
        <h3>📊 Résumé de l'export</h3>
        <p><strong>Total des réservations :</strong> {{ $reservations->count() }}</p>
        <p><strong>Date de génération :</strong> {{ $date_export->format('d/m/Y à H:i:s') }}</p>
    </div>

    <!-- Filtres appliqués -->
    @if(!empty($filtres) && array_filter($filtres))
    <div class="filters">
        <h4>🔍 Filtres appliqués :</h4>
        @if(!empty($filtres['search']))
            <p><strong>Recherche :</strong> "{{ $filtres['search'] }}"</p>
        @endif
        @if(!empty($filtres['statut']) && $filtres['statut'] !== 'all')
            <p><strong>Statut :</strong> {{ ucfirst($filtres['statut']) }}</p>
        @endif
        @if(!empty($filtres['date']))
            <p><strong>Date d'arrivée :</strong> {{ \Carbon\Carbon::parse($filtres['date'])->format('d/m/Y') }}</p>
        @endif
    </div>
    @endif

    <!-- Tableau des réservations -->
    @if($reservations->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="width: 8%;">N° Rés.</th>
                <th style="width: 18%;">Client</th>
                <th style="width: 15%;">Contact</th>
                <th style="width: 15%;">Chambre</th>
                <th style="width: 15%;">Dates</th>
                <th style="width: 8%;">Invités</th>
                <th style="width: 10%;">Prix</th>
                <th style="width: 11%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
            <tr>
                <td class="text-center fw-bold">#{{ $reservation->id }}</td>
                <td>
                    <div class="fw-bold">{{ $reservation->client_nom }} {{ $reservation->client_prenom }}</div>
                    <div>Créé le {{ $reservation->created_at->format('d/m/Y') }}</div>
                </td>
                <td>
                    <div>📧 {{ $reservation->client_email }}</div>
                    <div>📞 {{ $reservation->client_telephone }}</div>
                </td>
                <td>
                    <div class="fw-bold">{{ $reservation->chambre->nom ?? 'N/A' }}</div>
                    @if($reservation->chambre)
                    <div>{{ number_format($reservation->chambre->prix_par_nuit, 0, ',', ' ') }} FCFA/nuit</div>
                    @endif
                </td>
                <td>
                    <div><strong>Du :</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</div>
                    <div><strong>Au :</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</div>
                    <div>({{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }} nuit(s))</div>
                </td>
                <td class="text-center">{{ $reservation->nombre_invites }}</td>
                <td class="text-right fw-bold">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</td>
                <td class="text-center">
                    @php
                        $statusClass = match($reservation->statut) {
                            'confirmée' => 'badge-success',
                            'en_attente' => 'badge-warning',
                            'annulée' => 'badge-danger',
                            default => 'badge-primary'
                        };
                        $statusText = match($reservation->statut) {
                            'confirmée' => 'Confirmée',
                            'en_attente' => 'En Attente',
                            'annulée' => 'Annulée',
                            default => ucfirst($reservation->statut)
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Résumé statistique -->
    <div class="summary">
        <h4>📈 Statistiques</h4>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $reservations->count() }}</div>
                <div class="summary-label">Total Réservations</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $reservations->where('statut', 'confirmée')->count() }}</div>
                <div class="summary-label">Confirmées</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $reservations->where('statut', 'en_attente')->count() }}</div>
                <div class="summary-label">En Attente</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $reservations->where('statut', 'annulée')->count() }}</div>
                <div class="summary-label">Annulées</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($reservations->sum('prix_total'), 0, ',', ' ') }}</div>
                <div class="summary-label">Revenus Total (FCFA)</div>
            </div>
        </div>
    </div>

    @else
    <div style="text-align: center; padding: 50px; background: #f8f9fa; border-radius: 10px;">
        <h3 style="color: #666; margin: 0;">📭 Aucune réservation trouvée</h3>
        <p style="color: #999; margin: 10px 0 0 0;">Aucune réservation ne correspond aux critères de filtrage.</p>
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        Hôtel Restaurant Le Printemps - Export généré le {{ $date_export->format('d/m/Y à H:i:s') }} - Page <span class="pagenum"></span>
    </div>
</body>
</html>

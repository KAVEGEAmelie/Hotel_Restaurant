<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - Réservation #{{ $reservation->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .receipt-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 8px 0;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            vertical-align: top;
        }
        
        .amount-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #1976d2;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 123, 255, 0.1);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
        <div class="watermark">HÔTEL RESTAURANT LE PRINTEMPS</div>    <div class="header">
        <div class="hotel-name">Hôtel Restaurant Le Printemps</div>
        <div>📧 hotelrestaurantleprintemps@yahoo.com | 📞 +228 71 34 88 88 / 96 06 88 88</div>
        <div class="document-title">REÇU DE PAIEMENT</div>
        <div>Réservation #{{ $reservation->id }}</div>
    </div>

    <div class="receipt-info">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Date d'émission :</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut :</div>
                <div class="info-value">
                    <span class="status-badge">{{ strtoupper($reservation->statut) }}</span>
                </div>
            </div>
            @if($reservation->transaction_ref)
            <div class="info-row">
                <div class="info-label">Référence de transaction :</div>
                <div class="info-value">{{ $reservation->transaction_ref }}</div>
            </div>
            @endif
        </div>
    </div>

    <h3>Informations Client</h3>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Nom complet :</div>
            <div class="info-value">{{ $reservation->client_prenom }} {{ $reservation->client_nom }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email :</div>
            <div class="info-value">{{ $reservation->client_email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Téléphone :</div>
            <div class="info-value">{{ $reservation->client_telephone }}</div>
        </div>
    </div>

    <h3>Détails de la Réservation</h3>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Chambre :</div>
            <div class="info-value">{{ $reservation->chambre->nom }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Type :</div>
            <div class="info-value">{{ $reservation->chambre->type ?? 'Standard' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date d'arrivée :</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de départ :</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nombre de nuits :</div>
            <div class="info-value">
                {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }}
            </div>
        </div>
        @if($reservation->nombre_personnes)
        <div class="info-row">
            <div class="info-label">Nombre de personnes :</div>
            <div class="info-value">{{ $reservation->nombre_personnes }}</div>
        </div>
        @endif
    </div>

    <div class="amount-section">
        <div>Montant Total Payé</div>
        <div class="amount">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</div>
    </div>

    @if($reservation->notes)
    <h3>Notes</h3>
    <p>{{ $reservation->notes }}</p>
    @endif

    <div class="footer">
        <p><strong>Merci de votre confiance !</strong></p>
        <p>Ce reçu confirme le paiement de votre réservation. Conservez-le précieusement.</p>
        <p>En cas de questions, contactez-nous : hotelrestaurantleprintemps@yahoo.com</p>
        <hr style="margin: 10px 0;">
        <p>Hôtel Restaurant Le Printemps - Tous droits réservés © {{ date('Y') }}</p>
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>

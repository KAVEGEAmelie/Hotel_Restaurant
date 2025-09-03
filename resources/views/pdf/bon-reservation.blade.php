<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Réservation #{{ $reservation->id }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #333;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        /* En-tête avec couleurs du thème */
        .header {
            background: linear-gradient(135deg, #2c5530 0%, #3d7c47 100%);
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .header .subtitle {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        /* Numéro de réservation compact */
        .reservation-number {
            text-align: center;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #2c5530;
        }
        
        .reservation-number h2 {
            margin: 0;
            font-size: 24px;
            color: #2c5530;
            font-weight: bold;
        }
        
        .reservation-number .date {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 10px;
        }
        
        /* Layout en deux colonnes */
        .content-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .content-row {
            display: table-row;
        }
        
        .content-col {
            display: table-cell;
            vertical-align: top;
            padding: 0 8px;
            width: 50%;
        }
        
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }
        
        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #2c5530;
            font-weight: bold;
            border-bottom: 1px solid #2c5530;
            padding-bottom: 3px;
        }
        
        .info-line {
            margin: 4px 0;
            font-size: 10px;
        }
        
        .info-line strong {
            color: #495057;
            font-weight: bold;
        }
        
        /* Section dates compacte */
        .dates-section {
            background: linear-gradient(135deg, #2c5530 0%, #3d7c47 100%);
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            text-align: center;
        }
        
        .dates-grid {
            display: table;
            width: 100%;
        }
        
        .date-cell {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 0 5px;
        }
        
        .date-cell h4 {
            margin: 0 0 3px 0;
            font-size: 10px;
            opacity: 0.9;
        }
        
        .date-cell .date-value {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        
        .date-cell .date-info {
            font-size: 9px;
            opacity: 0.8;
            margin: 2px 0 0 0;
        }
        
        /* Montant à payer - compact */
        .total-section {
            background: linear-gradient(135deg, #ffc107 0%, #ffcd39 100%);
            border: 2px solid #e0a800;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }
        
        .total-section h3 {
            margin: 0 0 5px 0;
            color: #856404;
            font-size: 12px;
            font-weight: bold;
        }
        
        .total-amount {
            font-size: 22px;
            font-weight: bold;
            color: #2c5530;
            margin: 0;
        }
        
        /* Instructions compactes */
        .instructions {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .instructions h4 {
            margin: 0 0 8px 0;
            color: #0c5460;
            font-size: 11px;
            font-weight: bold;
        }
        
        .instructions ul {
            margin: 0;
            padding-left: 15px;
        }
        
        .instructions li {
            margin-bottom: 3px;
            font-size: 9px;
            line-height: 1.2;
        }
        
        /* Statut compact */
        .status-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            margin: 10px 0;
        }
        
        .status-section h4 {
            margin: 0 0 3px 0;
            color: #856404;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-section p {
            margin: 0;
            font-size: 9px;
            color: #6c6c6c;
        }
        
        /* Contact pied de page */
        .contact-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #2c5530;
        }
        
        .contact-section h5 {
            margin: 0 0 8px 0;
            color: #2c5530;
            font-size: 11px;
            font-weight: bold;
        }
        
        .contact-info {
            display: table;
            width: 100%;
        }
        
        .contact-item {
            display: table-cell;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 0 5px;
        }
        
        /* Styles utilitaires */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-green { color: #2c5530; }
        .text-muted { color: #6c757d; }
        
        /* QR Code placeholder */
                .qr-section {
            position: absolute;
            top: 10mm;
            right: 15mm;
            text-align: center;
            width: 30mm;
        }
        
        .qr-code {
            margin-bottom: 3px;
        }
        
        .qr-code svg {
            width: 25mm !important;
            height: 25mm !important;
        }
        
        .qr-text {
            font-size: 8px;
            font-weight: bold;
            color: #2c5530;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- QR Code (coin supérieur droit) -->
        <div class="qr-section">
            <div class="qr-code">
                {!! $qrCode !!}
            </div>
            <div class="qr-text">#{{ $reservation->id }}</div>
        </div>

        <!-- En-tête -->
        <div class="header">
            <h1>🌿 HÔTEL LE PRINTEMPS</h1>
            <p class="subtitle">Bon de Réservation à présenter à l'hôtel</p>
        </div>

        <!-- Numéro de réservation -->
        <div class="reservation-number">
            <h2># {{ $reservation->id }}</h2>
            <p class="date">Créé le {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y à H:i') }}</p>
        </div>

        <!-- Contenu principal en deux colonnes -->
        <div class="content-grid">
            <div class="content-row">
                <div class="content-col">
                    <!-- Informations client -->
                    <div class="info-box">
                        <h3>👤 INFORMATIONS CLIENT</h3>
                        <div class="info-line"><strong>Nom :</strong> {{ $reservation->client_nom }} {{ $reservation->client_prenom }}</div>
                        <div class="info-line"><strong>Email :</strong> {{ $reservation->client_email }}</div>
                        <div class="info-line"><strong>Téléphone :</strong> {{ $reservation->client_telephone }}</div>
                        <div class="info-line"><strong>Invités :</strong> {{ $reservation->nombre_invites }} personne(s)</div>
                    </div>
                </div>
                <div class="content-col">
                    <!-- Informations chambre -->
                    <div class="info-box">
                        <h3>🛏️ DÉTAILS CHAMBRE</h3>
                        <div class="info-line"><strong>Chambre :</strong> {{ $reservation->chambre->nom }}</div>
                        <div class="info-line"><strong>Type :</strong> {{ $reservation->chambre->categorie->nom ?? 'Standard' }}</div>
                        <div class="info-line"><strong>Capacité :</strong> {{ $reservation->chambre->capacite }} personne(s)</div>
                        <div class="info-line"><strong>Prix/nuit :</strong> {{ number_format($reservation->chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates de séjour -->
        <div class="dates-section">
            <div class="dates-grid">
                <div class="date-cell">
                    <h4>📅 ARRIVÉE</h4>
                    <div class="date-value">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}</div>
                    <div class="date-info">À partir de 14h00</div>
                </div>
                <div class="date-cell">
                    <h4>📅 DÉPART</h4>
                    <div class="date-value">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}</div>
                    <div class="date-info">Avant 12h00</div>
                </div>
                <div class="date-cell">
                    <h4>🌙 DURÉE</h4>
                    <div class="date-value">{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }}</div>
                    <div class="date-info">nuit(s)</div>
                </div>
            </div>
        </div>

        <!-- Montant total -->
        <div class="total-section">
            <h3>💰 MONTANT À RÉGLER À L'HÔTEL</h3>
            <div class="total-amount">{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</div>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h4>ℹ️ INSTRUCTIONS IMPORTANTES</h4>
            <ul>
                <li><strong>Présentez ce bon à la réception</strong> avec une pièce d'identité valide</li>
                <li><strong>Paiement :</strong> Espèces, carte bancaire ou Mobile Money acceptés</li>
                <li><strong>Horaires :</strong> Arrivée dès 14h00 • Départ avant 12h00</li>
                <li><strong>Modification :</strong> Contactez-nous au +228 71 34 88 88</li>
            </ul>
        </div>

        <!-- Statut -->
        <div class="status-section">
            <h4>⏰ STATUT : EN ATTENTE DE CONFIRMATION</h4>
            <p>Votre réservation sera confirmée lors du règlement à l'hôtel</p>
        </div>

        <!-- Contact -->
        <div class="contact-section">
            <h5 class="text-green">📞 CONTACT HÔTEL LE PRINTEMPS</h5>
            <div class="contact-info">
                <div class="contact-item">+228 71 34 88 88</div>
                <div class="contact-item">+228 96 06 88 88</div>
                <div class="contact-item">hotelrestaurantleprintemps@yahoo.com</div>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Réservation</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 150px; }
        .header h1 { margin: 0; color: #6F4E37; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .details-table th { background-color: #f2f2f2; }
        .total-section { text-align: right; margin-top: 20px; }
        .total-section h2 { color: #228B22; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Mettez votre logo ici. Utilisez un chemin absolu ou encodez l'image en base64 pour plus de fiabilité. --}}
            {{-- <img src="{{ public_path('assets/img/logo.jpg') }}" alt="Logo"> --}}
            <h1>Hôtel Le Printemps</h1>
            <p>Reçu de Paiement</p>
        </div>

        <h3>Détails du Client</h3>
        <p>
            <strong>Nom :</strong> {{ $reservation->client_prenom }} {{ $reservation->client_nom }}<br>
            <strong>Email :</strong> {{ $reservation->client_email }}<br>
            <strong>Téléphone :</strong> {{ $reservation->client_telephone }}
        </p>

        <h3>Détails de la Réservation #{{ $reservation->id }}</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $reservation->chambre->nom }}</strong><br>
                        <small>
                            Du {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d/m/Y') }}
                            au {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays($reservation->check_out_date) }} Nuit(s)</td>
                    <td>{{ number_format($reservation->chambre->prix_par_nuit, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <h2>Total Payé : {{ number_format($reservation->prix_total, 0, ',', ' ') }} FCFA</h2>
        </div>

        <div class="footer">
            <p>Merci pour votre confiance.</p>
            <p>Hôtel Le Printemps - Rue de l'Hôtel, Kpalimé, Togo</p>
        </div>
    </div>
</body>
</html>

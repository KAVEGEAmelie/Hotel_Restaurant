<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Police - {{ $reservation->client_nom }}</title>
    <style>
        @page {
            size: 8cm 25cm; /* format ticket thermique */
            margin: 0.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #000;
        }

        .fiche-container {
            width: 100%;
            padding: 5px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .header-logo {
            width: 40px;
        }

        .header-hotel-info {
            text-align: center;
        }

        .header-hotel-info h1 {
            font-size: 10pt;
            margin: 0;
            font-weight: bold;
        }

        .header-hotel-info p {
            margin: 1px 0;
            font-size: 8pt;
        }

        .chambre-box {
            border: 1px solid black;
            padding: 3px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            font-size: 9pt;
        }

        .title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .form-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .label-col {
            width: 120px;
        }

        .label-main {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .label-sub {
            font-size: 7pt;
            color: #555;
        }

        .value-col {
            border-bottom: 1px dotted black;
            font-weight: bold;
            padding-left: 3px;
            font-family: monospace;
            font-size: 8pt;
        }

        .footer-table {
            width: 100%;
            margin-top: 15px;
        }

        .footer-table td {
            padding-top: 10px;
            vertical-align: bottom;
            font-size: 8pt;
        }
    </style>
</head>
<body>

<div class="fiche-container">
    <table class="header-table">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('assets/img/logo.jpg') }}" class="header-logo" alt="Logo"></td>
            <td style="width: 70%;" class="header-hotel-info">
                <h1>HÔTEL RESTAURANT LE PRINTEMPS</h1>
                <p>Tél.(00228) 71 34 88 88 / 96 06 88 88</p>
                <p>Kpalimé - Kouma Konda TOGO</p>
            </td>
            <td style="width: 15%; text-align: right;">
                <div class="chambre-box">Ch N° {{ $reservation->chambre->nom ?? '' }}</div>
            </td>
        </tr>
    </table>

    <div class="title">FICHE DE POLICE</div>

    <table class="form-table">
        <tr>
            <td class="label-col"><span class="label-main">NOM</span><br><span class="label-sub">Surname / Name</span></td>
            <td class="value-col">{{ $reservation->client_nom }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">PRENOMS</span><br><span class="label-sub">Christian name / Vorname</span></td>
            <td class="value-col">{{ $reservation->client_prenom }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DATE DE NAISSANCE</span><br><span class="label-sub">Date of Birth</span></td>
            <td class="value-col">{{ $reservation->client_date_naissance ? $reservation->client_date_naissance->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">NATIONALITE</span></td>
            <td class="value-col">{{ $reservation->client_nationalite }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">PROFESSION</span></td>
            <td class="value-col">{{ $reservation->client_profession }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DOMICILE</span></td>
            <td class="value-col">{{ $reservation->client_domicile }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">MOTIF DU VOYAGE</span></td>
            <td class="value-col">{{ $reservation->motif_voyage }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">VENANT DE</span></td>
            <td class="value-col">{{ $reservation->venant_de }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">ALLANT A</span></td>
            <td class="value-col">{{ $reservation->allant_a }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DATE D'ARRIVEE</span></td>
            <td class="value-col">{{ $reservation->check_in_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DATE DE DEPART</span></td>
            <td class="value-col">{{ $reservation->check_out_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">PIECE D'IDENTITE N°</span></td>
            <td class="value-col">{{ $reservation->piece_identite_numero }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DELIVREE LE</span></td>
            <td class="value-col">{{ $reservation->piece_identite_delivree_le ? $reservation->piece_identite_delivree_le->format('d/m/Y') : '' }} à {{ $reservation->piece_identite_delivree_a }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">VISA N°</span></td>
            <td class="value-col"> </td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DELIVRE LE</span></td>
            <td class="value-col"> </td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">Personne à prévenir</span></td>
            <td class="value-col">{{ $reservation->personne_a_prevenir }}</td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 60%;">Kouma Konda le .................. 20........</td>
            <td style="width: 40%; text-align: right;"><strong>Signature</strong><br><span style="font-size: 7pt;">Underschrift</span></td>
        </tr>
    </table>
</div>

</body>
</html>

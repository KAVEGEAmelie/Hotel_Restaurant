<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Police - {{ $reservation->client_nom }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #000;
        }

        .fiche-container {
            width: 100%;
            padding: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-logo {
            width: 60px;
        }

        .header-hotel-info {
            text-align: center;
        }

        .header-hotel-info h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
        }

        .header-hotel-info p {
            margin: 2px 0;
            font-size: 9pt;
        }

        .chambre-box {
            border: 1.5px solid black;
            padding: 5px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;

        }

        .form-table td {
            padding: 3px 0;
        }

        .label-col {
            width: 160px;
            vertical-align: top;
        }

        .label-main {
            font-weight: bold;
            text-transform: uppercase;
        }

        .label-sub {
            font-size: 8pt;
            color: #555;
        }

        .value-col {
            border-bottom: 1.5px dotted black;
            font-weight: bold;
            padding-left: 5px;
            font-family: monospace;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
        }

        @page {
            margin: 1cm;
        }

        .footer-table td {
            padding-top: 20px;
            vertical-align: bottom;
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
            <td class="label-col"><span class="label-main">DATE DE NAISSANCE</span><br><span class="label-sub">Date of Birth / Geburtsdatum</span></td>
            <td class="value-col">{{ $reservation->client_date_naissance ? $reservation->client_date_naissance->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">NATIONALITE</span><br><span class="label-sub">Nationality / Nationalität</span></td>
            <td class="value-col">{{ $reservation->client_nationalite }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">PROFESSION</span><br><span class="label-sub">Occupation / Beruf</span></td>
            <td class="value-col">{{ $reservation->client_profession }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DOMICILE</span><br><span class="label-sub">Residence / Wohnort</span></td>
            <td class="value-col">{{ $reservation->client_domicile }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">MOTIF DU VOYAGE</span><br><span class="label-sub">Motif of travelling / Reisemotiv</span></td>
            <td class="value-col">{{ $reservation->motif_voyage }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">VENANT DE</span><br><span class="label-sub">Arriving from / kommend aus</span></td>
            <td class="value-col">{{ $reservation->venant_de }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">ALLANT A</span><br><span class="label-sub">Traveling to / Reiseziel</span></td>
            <td class="value-col">{{ $reservation->allant_a }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DATE D'ARRIVEE</span><br><span class="label-sub">Arriving / Ankunftsdatum</span></td>
            <td class="value-col">{{ $reservation->check_in_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DATE DE DEPART</span><br><span class="label-sub">Leaving on / Abreisedatum</span></td>
            <td class="value-col">{{ $reservation->check_out_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">PIECE D'IDENTITE N°</span><br><span class="label-sub">Passport N° / Reise pass Nr.</span></td>
            <td class="value-col">{{ $reservation->piece_identite_numero }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DELIVREE LE</span><br><span class="label-sub">Issued on / ausgestellt am</span></td>
            <td class="value-col">{{ $reservation->piece_identite_delivree_le ? $reservation->piece_identite_delivree_le->format('d/m/Y') : '' }} à {{ $reservation->piece_identite_delivree_a }}</td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">VISA N°</span></td>
            <td class="value-col"> </td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">DELIVRE LE</span><br><span class="label-sub">Issued on / ausgestellt am</span></td>
            <td class="value-col"> </td>
        </tr>
        <tr>
            <td class="label-col"><span class="label-main">Personne à prévenir</span><br><span class="label-sub">Contact :</span></td>
            <td class="value-col">{{ $reservation->personne_a_prevenir }}</td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 60%;">Kouma Konda le .................. 20........</td>
            <td style="width: 40%; text-align: right;"><strong>Signature</strong><br><span style="font-size: 8pt;">Underschrift</span></td>
        </tr>
    </table>
</div>

</body>
</html>

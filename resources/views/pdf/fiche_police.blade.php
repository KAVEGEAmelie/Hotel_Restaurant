<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Police - {{ $reservation->client_nom }}</title>
    <style>
        /* Utilisation d'une police qui supporte bien les caractères spéciaux */
        @page { margin: 25px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; color: #000; }
        
        .fiche-container {
            width: 100%;
            border: 2px solid black;
            padding: 1cm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: top;
            text-align: center;
        }
        .header-logo {
            width: 60px;
        }
        .header-hotel-info {
            font-size: 10pt;
        }
        .header-hotel-info h1 {
            font-size: 14pt;
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .header-hotel-info p {
            margin: 2px 0;
        }
        .chambre-box {
            border: 1px solid black;
            padding: 5px;
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap; /* Empêche le texte de passer à la ligne */
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 15px 0 25px 0;
            letter-spacing: 1px;
        }

        .field {
            margin-bottom: 14px;
            white-space: nowrap;
            overflow: hidden;
        }
        .field-label {
            font-weight: bold;
            display: inline-block;
        }
        .field-sublabel {
            font-size: 8pt;
            color: #444;
            display: block;
            margin-top: -2px;
        }
        .dotted-line {
            border-bottom: 1.5px dotted black;
            width: 100%;
            display: block;
        }
        .field-content {
            position: relative;
            top: -1.2em; /* Remonte le texte pour qu'il soit sur la ligne */
            padding-left: 5px;
            background-color: #fff; /* Cache la ligne pointillée derrière le texte */
            padding-right: 5px;
        }
        .field-content.long {
            padding-left: 180px; /* Espace pour les longs labels */
        }
        .field-content.medium {
            padding-left: 170px;
        }
        .field-content.short {
            padding-left: 160px;
        }
        .field-content.city {
            padding-left: 100px;
        }

        .footer {
            margin-top: 25px;
            font-size: 10pt;
        }
    </style>
</head>
<body>

    <div class="fiche-container">
        <table class="header-table">
            <tr>
                <td style="width: 20%;"><img src="{{ public_path('assets/img/logo.jpg') }}" class="header-logo" alt="Logo"></td>
                <td style="width: 60%;" class="header-hotel-info">
                    <h1>HÔTEL RESTAURANT LE PRINTEMPS</h1>
                    <p>Tél.(00228) 71 34 88 88 / 96 06 88 88</p>
                    <p>Kpalimé - Kouma Konda TOGO</p>
                </td>
                <td style="width: 20%;"><span class="chambre-box">Ch N° {{ $reservation->chambre->nom ?? '' }}</span></td>
            </tr>
        </table>

        <div class="title">FICHE DE POLICE</div>

        <div class="field">
            <span class="field-label">NOM</span> <span class="field-sublabel">Surname / Name</span>
            <span class="dotted-line"></span><span class="field-content long">{{ $reservation->client_nom }}</span>
        </div>
        <div class="field">
            <span class="field-label">PRENOMS</span> <span class="field-sublabel">Christian name / Vorname</span>
            <span class="dotted-line"></span><span class="field-content long">{{ $reservation->client_prenom }}</span>
        </div>
        <div class="field">
            <span class="field-label">DATE DE NAISSANCE</span> <span class="field-sublabel">Date of Birth / Geburtsdatun</span>
            <span class="dotted-line"></span><span class="field-content short">{{ $reservation->client_date_naissance ? \Carbon\Carbon::parse($reservation->client_date_naissance)->format('d / m / Y') : '' }}</span>
        </div>
        <div class="field">
            <span class="field-label">NATIONALITE</span> <span class="field-sublabel">Nationality / Nationalität</span>
            <span class="dotted-line"></span><span class="field-content short">{{ $reservation->client_nationalite }}</span>
        </div>
        <div class="field">
            <span class="field-label">PROFESSION</span> <span class="field-sublabel">Occupation / Beruf</span>
            <span class="dotted-line"></span><span class="field-content medium">{{ $reservation->client_profession }}</span>
        </div>
        <div class="field">
            <span class="field-label">DOMICILE</span> <span class="field-sublabel">Residence / Morhnert</span>
            <span class="dotted-line"></span><span class="field-content long">{{ $reservation->client_domicile }}</span>
        </div>
        <div class="field">
            <span class="field-label">MOTIF DU VOYAGE</span> <span class="field-sublabel">Motif of travelling / Reisemotiv</span>
            <span class="dotted-line"></span><span class="field-content short">{{ $reservation->motif_voyage }}</span>
        </div>
        <div class="field">
            <span class="field-label">VENANT DE</span> <span class="field-sublabel">Arriving from / kommend aus</span>
            <span class="dotted-line"></span><span class="field-content medium">{{ $reservation->venant_de }}</span>
        </div>
        <div class="field">
            <span class="field-label">ALLANT A</span> <span class="field-sublabel">Traveling to / Reiseziel</span>
            <span class="dotted-line"></span><span class="field-content long">{{ $reservation->allant_a }}</span>
        </div>
        <div class="field">
            <span class="field-label">DATE D'ARRIVEE</span> <span class="field-sublabel">Arriving / Ankunftsdatum</span>
            <span class="dotted-line"></span><span class="field-content short">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d / m / Y') }}</span>
        </div>
        <div class="field">
            <span class="field-label">DATE DE DEPART</span> <span class="field-sublabel">Leaving on / Abreisedatum</span>
            <span class="dotted-line"></span><span class="field-content short">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d / m / Y') }}</span>
        </div>
        <div class="field">
            <span class="field-label">PIECE D'IDENTITE N°</span> <span class="field-sublabel">Passeport N° / Reise pass Nt.</span>
            <span class="dotted-line"></span><span class="field-content short">{{ $reservation->piece_identite_numero }}</span>
        </div>
        <div class="field">
            <span class="field-label">DELIVREE LE</span> <span class="field-sublabel">Issue on / ausgestellt am</span>
            <span class="dotted-line"></span><span class="field-content city">{{ $reservation->piece_identite_delivree_le ? \Carbon\Carbon::parse($reservation->piece_identite_delivree_le)->format('d / m / Y') : '' }}</span><span style="position:relative; top: -1.2em; padding-left: 20px;">À</span><span class="field-content" style="padding-left:15px;">{{ $reservation->piece_identite_delivree_a }}</span>
        </div>
        <div class="field">
            <span class="field-label">VISA N°</span> <span class="field-sublabel"></span>
            <span class="dotted-line"></span><span class="field-content long"></span>
        </div>
        <div class="field">
            <span class="field-label">DELIVRE LE</span> <span class="field-sublabel">Issue on / ausgestellt am</span>
            <span class="dotted-line"></span><span class="field-content long"></span>
        </div>
        <div class="field">
            <span class="field-label">Personne à prévenir</span> <span class="field-sublabel">Contact :</span>
            <span class="dotted-line"></span><span class="field-content medium">{{ $reservation->personne_a_prevenir }}</span>
        </div>

        <table class="footer" style="width: 100%;">
            <tr>
                <td style="width: 50%;">Kouma Konda le ..................... 20 ........</td>
                <td style="width: 50%; text-align: right;"><strong>Signature</strong></td>
            </tr>
        </table>
    </div>

</body>
</html>
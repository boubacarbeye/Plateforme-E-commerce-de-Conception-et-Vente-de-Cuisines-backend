<!-- resources/views/devis/pdf.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {{ $devis->numero }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 14px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .company { font-size: 24px; font-weight: bold; color: #2B6CB0; }
        .info-block { line-height: 1.6; }
        .text-right { text-align: right; }
        h1 { color: #2B6CB0; border-bottom: 2px solid #2B6CB0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2B6CB0; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .total-section { margin-top: 30px; float: right; width: 50%; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .grand-total { font-size: 18px; font-weight: bold; color: #2B6CB0; border-top: 2px solid #333; margin-top: 10px; padding-top: 10px; }
        .footer { position: absolute; bottom: 30px; width: 100%; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            DGS Africa<br>
            <span style="font-size: 14px; font-weight: normal;">Votre configurateur de cuisine</span>
        </div>
        <div class="info-block text-right">
            <strong>Devis N° :</strong> {{ $devis->numero }}<br>
            <strong>Date :</strong> {{ $devis->date_creation->format('d/m/Y') }}<br>
            @if($projet->client)
                <strong>Client :</strong> {{ $projet->client->prenom }} {{ $projet->client->nom }}<br>
            @endif
        </div>
    </div>

    <h1>Détail du Projet : {{ $projet->nom ?? 'Projet Cuisine' }}</h1>

    <div class="info-block" style="margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px;">
        <strong>Dimensions de la pièce :</strong> {{ $projet->longueur_cm }} cm (L) x {{ $projet->largeur_cm }} cm (l) x {{ $projet->hauteur_cm }} cm (H)<br>
        <strong>Forme :</strong> {{ ucfirst(str_replace('_', ' en ', $projet->forme)) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Module / Produit</th>
                <th>Largeur</th>
                <th>Options (Finition/Matériau)</th>
                <th class="text-right">Prix Unitaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projet->modules as $ligne)
                <tr>
                    <td>{{ $ligne->module->nom }}</td>
                    <td>{{ $ligne->module->largeur_cm }} cm</td>
                    <td>
                        @if($ligne->materiau)
                            {{ $ligne->materiau->nom }} (+{{ number_format($ligne->materiau->supplement_prix, 2) }} €)
                        @else
                            Standard
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($ligne->module->prix_base + ($ligne->materiau->supplement_prix ?? 0), 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Sous-total :</span>
            <span>{{ number_format($devis->montant_total, 2) }} €</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL ESTIMÉ :</span>
            <span>{{ number_format($devis->montant_total, 2) }} €</span>
        </div>
    </div>

    <div class="footer">
        DGS Africa - Sujet 4 - MVP Conception de Cuisines. Devis valable 30 jours.
    </div>
</body>
</html>

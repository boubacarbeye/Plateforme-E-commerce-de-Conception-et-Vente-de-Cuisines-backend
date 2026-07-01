<?php

namespace App\Http\Controllers;

use App\Models\ProjetCuisine;
use App\Models\Devis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DevisController extends Controller
{
    public function generate(Request $request, $projetId)
    {
        // 1. Charger le projet avec ses relations
        $projet = ProjetCuisine::with('modules.module', 'modules.materiau', 'client')
                               ->findOrFail($projetId);

        // 2. Créer l'entrée en base de données
        $numeroDevis = 'DEV-' . date('Y') . '-' . str_pad(Devis::count() + 1, 4, '0', STR_PAD_LEFT);
        
        $devis = Devis::create([
            'projet_id' => $projet->id,
            'numero' => $numeroDevis,
            'montant_total' => $projet->prix_estime, // RG-03 & RG-04
            'statut' => 'envoye',
            'date_creation' => now(),
        ]);

        // 3. Générer le PDF à partir d'une vue Blade simplifiée
        $html = '<h1 style="color: #2B6CB0;">Devis N° ' . $devis->numero . '</h1>';
        $html .= '<p><strong>Client :</strong> ' . ($projet->client->nom ?? 'Visiteur') . '</p>';
        $html .= '<p><strong>Dimensions :</strong> ' . $projet->longueur_cm . 'x' . $projet->largeur_cm . 'x' . $projet->hauteur_cm . ' cm</p>';
        $html .= '<hr>';
        $html .= '<table width="100%" border="1" cellpadding="5" style="border-collapse: collapse;">';
        $html .= '<tr style="background: #2B6CB0; color: white;"><th>Module</th><th>Option</th><th>Prix</th></tr>';
        
        foreach ($projet->modules as $ligne) {
            $prix = $ligne->module->prix_base + ($ligne->materiau->supplement_prix ?? 0);
            $html .= '<tr>';
            $html .= '<td>' . $ligne->module->nom . '</td>';
            $html .= '<td>' . ($ligne->materiau->nom ?? 'Standard') . '</td>';
            $html .= '<td>' . number_format($prix, 2) . ' FCFA</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '<h2 style="text-align: right; color: #2B6CB0;">Total : ' . number_format($devis->montant_total, 2) . ' FCFA</h2>';

        $pdf = Pdf::loadHTML($html);
        
        // 4. Sauvegarder le fichier physiquement
        $fileName = 'devis_' . $numeroDevis . '.pdf';
        $path = 'devis/' . $fileName;
        Storage::disk('public')->put($path, $pdf->output());
        
        $devis->update(['pdf_url' => $path]);

        // 5. Retourner le PDF pour affichage dans le navigateur
        return $pdf->stream($fileName);
    }
}
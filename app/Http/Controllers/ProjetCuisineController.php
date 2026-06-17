<?php

namespace App\Http\Controllers;

use App\Models\ProjetCuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProjetCuisineController extends Controller
{
    /**
     * Partie 1 : Création d'un projet de cuisine (Écran Création + RG-01)
     */
    public function store(Request $request)
    {
        // 1. Validation des données de l'écran avec application de la RG-01
        $validated = $request->validate([
            'longueur_cm' => 'required|integer|min:100', // Longueur du Mur A (min 1m)
            'hauteur_cm' => 'required|integer|min:150',  // Hauteur sous plafond
            'forme' => 'required|in:lineaire,en_L',       // Tes énumérations de forme
            
            // RG-01 : largeur_cm (Mur B) est requis UNIQUEMENT SI la forme saisie est 'en_L'
            'largeur_cm' => 'required_if:forme,en_L|nullable|integer|min:100',
            
            'statut' => 'nullable|in:brouillon,devis_demande,traite',
        ]);

        // 2. Création de l'enregistrement en utilisant tes vrais noms de colonnes
        $projet = ProjetCuisine::create([
            'ProjetCuisine_id' => (string) Str::uuid(),
            'client_id' => Auth::id(), // Récupère l'UUID de l'utilisateur connecté (via ton middleware JWT)
            'longueur_cm' => $validated['longueur_cm'],
            
            // Si la forme est linéaire, on force la largeur (Mur B) à null ou 0 selon ton choix (ici null)
            'largeur_cm' => $validated['forme'] === 'en_L' ? $validated['largeur_cm'] : null,
            
            'hauteur_cm' => $validated['hauteur_cm'],
            'forme' => $validated['forme'],
            'prix_estime' => 0.00, // Initialisé à 0 tant qu'aucun meuble n'est ajouté
            'statut' => $validated['statut'] ?? 'brouillon', // Prend 'brouillon' par défaut si non spécifié
        ]);

        // 3. Réponse JSON propre pour alimenter l'écran de confirmation
        return response()->json([
            'success' => true,
            'message' => 'Projet de cuisine initialisé avec succès !',
            'data' => $projet
        ], 201);
    }
    /**
     * Partie 2 : Récupérer un projet spécifique via son UUID (Public)
     */
    public function show($id)
    {
        // Recherche du projet par sa clé primaire personnalisée
        $projet = ProjetCuisine::find($id);

        // Si le projet n'existe pas, on renvoie une erreur propre
        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet de cuisine introuvable.'
            ], 404);
        }

        // Retourne les données du projet pour l'écran
        return response()->json([
            'success' => true,
            'data' => $projet
        ], 200);
    }
    /**
     * Partie 3 : Mettre à jour les dimensions ou la forme (Écran Modification)
     */
    public function update(Request $request, $id)
    {
        $projet = ProjetCuisine::find($id);

        if (!$projet) {
            return response()->json([
                'success' => false, 
                'message' => 'Projet introuvable.'
            ], 404);
        }

        $validated = $request->validate([
            'nom_projet' => 'sometimes|string|max:255',
            'forme' => 'sometimes|in:lineaire,en_L',
            'longueur_cm' => 'sometimes|integer|min:100',
            'largeur_cm' => 'required_if:forme,en_L|nullable|integer|min:100',
            'hauteur_cm' => 'sometimes|integer|min:150',
            'statut' => 'sometimes|in:brouillon,devis_demande,traite',
        ]);

        // 🌟 LA CORRECTION ICI : Si on passe en linéaire, on met 0 à la place de null
        if (isset($validated['forme']) && $validated['forme'] === 'lineaire') {
            $validated['largeur_cm'] = 0; 
        }

        $projet->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Projet de cuisine mis à jour avec succès !',
            'data' => $projet
        ], 200);
    }
    /**
     * Partie 4 : Supprimer un projet
     */
    public function destroy($id)
    {
        $projet = ProjetCuisine::find($id);

        if (!$projet) {
            return response()->json(['success' => false, 'message' => 'Projet introuvable.'], 404);
        }

        $projet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Projet de cuisine supprimé avec succès !'
        ], 200);
    }    
}
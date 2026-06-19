<?php

namespace App\Http\Controllers;

use App\Models\ProjetCuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // LA CORRECTION ICI : Si on passe en linéaire, on met 0 à la place de null
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


    /**
     * Jour 5 : Ajouter et positionner un module dans le Canvas 2D (RG-02)
     * Endpoint : POST /api/projets/{id}/modules
     */
/**
     * Jour 5 : Ajouter et positionner un module dans le Canvas 2D (RG-02)
     * Calcule le périmètre utile et met à jour dynamiquement le prix estimé.
     * * Endpoint : POST /api/projets/{id}/modules
     */
    public function ajouterModule(Request $request, $projetId)
    {
        // 1. Récupérer le projet de cuisine concerné
        $projet = ProjetCuisine::find($projetId);
        if (!$projet) {
            return response()->json([
                'success' => false, 
                'message' => 'Projet de cuisine introuvable.'
            ], 404);
        }

        // 2. Calculer le périmètre utile maximal de la pièce (RG-02)
        $perimetreUtile = $projet->longueur_cm; // Si linéaire = Mur A uniquement
        if ($projet->forme === 'en_L') {
            $perimetreUtile += $projet->largeur_cm; // Si en L = Mur A + Mur B
        }

        // 3. Valider les données envoyées par le canvas 2D
        $validated = $request->validate([
            'module_id' => 'required|uuid|exists:module_produits,ModuleProduit_id',
            'materiau_id' => 'required|uuid|exists:materiaux,Materiau_id',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
        ]);

        // 4. Récupérer le module du catalogue pour connaître sa largeur
        $moduleCatalogue = DB::table('module_produits')
            ->where('ModuleProduit_id', $validated['module_id'])
            ->first();

        $largeurNouveauModule = $moduleCatalogue->largeur_cm;

        // 5. Calculer la somme des largeurs des modules déjà installés dans ce projet
        $largeurTotaleActuelle = DB::table('projet_modules')
            ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
            ->where('projet_modules.projet_id', $projetId)
            ->sum('module_produits.largeur_cm');

        // 6. Vérification stricte de la règle de gestion RG-02
        if (($largeurTotaleActuelle + $largeurNouveauModule) > $perimetreUtile) {
            return response()->json([
                'success' => false,
                'message' => "RG-02 : Espace insuffisant. La somme des largeurs des meubles ({$largeurTotaleActuelle} + {$largeurNouveauModule} cm) dépasse le périmètre utile disponible ({$perimetreUtile} cm)."
            ], 422);
        }

        // 7. Tout est OK : On génère l'UUID et on enregistre la position sur le plan
        $projetModuleId = (string) \Illuminate\Support\Str::uuid();
        
        DB::table('projet_modules')->insert([
            'ProjetModule_id' => $projetModuleId,
            'projet_id' => $projetId,
            'module_id' => $validated['module_id'],
            'materiau_id' => $validated['materiau_id'],
            'position_x' => $validated['position_x'],
            'position_y' => $validated['position_y'],
        ]);

        // 💰 Recalcul et mise à jour automatique du prix total estimé du projet
        $nouveauPrixTotal = DB::table('projet_modules')
            ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
            ->where('projet_modules.projet_id', $projetId)
            ->sum('module_produits.prix_base');

        // Met à jour la ligne du projet dans la base de données
        $projet->update([
            'prix_estime' => $nouveauPrixTotal
        ]);

        // 8. Réponse finale retournée au Frontend (React)
        return response()->json([
            'success' => true,
            'message' => 'Module positionné avec succès dans le configurateur 2D et prix mis à jour !',
            'data' => [
                'ProjetModule_id' => $projetModuleId,
                'espace_occupe_cm' => $largeurTotaleActuelle + $largeurNouveauModule,
                'espace_maximal_cm' => $perimetreUtile,
                'nouveau_prix_total' => $nouveauPrixTotal
            ]
        ], 201);
    }
    /**
     * Jour 5 : Récupérer la liste des modules placés pour le Canvas 2D
     * Endpoint : GET /api/projets/{id}/modules
     */
    public function chargerCanvas($projetId)
    {
        // 1. Vérifier si le projet existe
        $projet = ProjetCuisine::find($projetId);
        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet de cuisine introuvable.'
            ], 404);
        }

        // 2. Récupérer les modules positionnés avec leurs coordonnées et les infos du catalogue
        $modulesPlaces = DB::table('projet_modules')
            ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
            ->where('projet_modules.projet_id', $projetId)
            ->select([
                'projet_modules.ProjetModule_id',
                'projet_modules.position_x',
                'projet_modules.position_y',
                'module_produits.ModuleProduit_id as module_id',
                'module_produits.ModuleProduit_nom as nom',
                'module_produits.categorie',
                'module_produits.largeur_cm',
                'module_produits.image_url'
            ])
            ->get();

        // 3. Retourner le tout pour que React puisse faire le rendu graphique
        return response()->json([
            'success' => true,
            'dimensions_piece' => [
                'forme' => $projet->forme,
                'longueur_cm' => $projet->longueur_cm,
                'largeur_cm' => $projet->largeur_cm, // Sera 0 ou null si linéaire
            ],
            'modules_positionnes' => $modulesPlaces
        ], 200);
    }
    
    /**
     * Charger les modules positionnés dans le Canvas 2D
     * Endpoint : GET /api/projets/{projetId}/modules
     */
    public function listerModules($projetId)
    {
        // Vérifier si le projet existe
        $projet = ProjetCuisine::find($projetId);
        if (!$projet) {
            return response()->json(['success' => false, 'message' => 'Projet introuvable.'], 404);
        }

        // Récupérer tous les modules associés à ce projet avec les détails du catalogue
        $modules = DB::table('projet_modules')
            ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
            ->join('materiaux', 'projet_modules.materiau_id', '=', 'materiaux.Materiau_id')
            ->where('projet_modules.projet_id', $projetId)
            ->select(
                'projet_modules.ProjetModule_id',
                'projet_modules.position_x',
                'projet_modules.position_y',
                'module_produits.nom_technique as module_nom',
                'module_produits.largeur_cm',
                'module_produits.prix_base',
                'materiaux.materiau_nom as materiau_nom'
            )
            ->get();

        return response()->json([
            'success' => true,
            'projet_id' => $projetId,
            'dimensions' => [
                'forme' => $projet->forme,
                'longueur_cm' => $projet->longueur_cm,
                'largeur_cm' => $projet->largeur_cm,
            ],
            'modules_positionnes' => $modules
        ], 200);
    }    
    /**
     * Supprimer un module du Canvas 2D et recalculer le prix
     * Endpoint : DELETE /api/projets/{projetId}/modules/{projetModuleId}
     */
    public function supprimerModule($projetId, $projetModuleId)
    {
        // 1. Vérifier si le module existe bien dans ce projet
        $moduleExiste = DB::table('projet_modules')
            ->where('ProjetModule_id', $projetModuleId)
            ->where('projet_id', $projetId)
            ->exists();

        if (!$moduleExiste) {
            return response()->json([
                'success' => false,
                'message' => 'Ce module n\'existe pas dans ce projet.'
            ], 404);
        }

        // 2. Supprimer le module du canvas
        DB::table('projet_modules')->where('ProjetModule_id', $projetModuleId)->delete();

        // 3. Recalculer le nouveau prix total après suppression
        $nouveauPrixTotal = DB::table('projet_modules')
            ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
            ->where('projet_modules.projet_id', $projetId)
            ->sum('module_produits.prix_base');

        // 4. Mettre à jour la table projet_cuisines
        $projet = ProjetCuisine::find($projetId);
        $projet->update([
            'prix_estime' => $nouveauPrixTotal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Module retiré du canvas avec succès et prix mis à jour !',
            'data' => [
                'nouveau_prix_total' => $nouveauPrixTotal
            ]
        ], 200);
    }    
}
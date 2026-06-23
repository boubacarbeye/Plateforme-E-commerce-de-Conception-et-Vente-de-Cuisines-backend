<?php

namespace App\Http\Controllers;

use App\Models\ProjetCuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjetCuisineController extends Controller
{
    /**
     * Créer un projet de cuisine (POST /api/projets)
     * RG-01 : largeur_cm (Mur B) requis uniquement si forme = 'en_L'
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'longueur_cm' => 'required|integer|min:100',
            'hauteur_cm'  => 'required|integer|min:150',
            'forme'       => 'required|in:lineaire,en_L',
            'largeur_cm'  => 'required_if:forme,en_L|nullable|integer|min:100',
            'statut'      => 'nullable|in:brouillon,devis_demande,traite',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();

            $projet = ProjetCuisine::create([
                'ProjetCuisine_id' => (string) Str::uuid(),
                'client_id'        => Auth::id(),
                'longueur_cm'      => $validated['longueur_cm'],
                // RG-01 : si linéaire, largeur_cm = null (cohérent avec show/update)
                'largeur_cm'       => $validated['forme'] === 'en_L' ? $validated['largeur_cm'] : null,
                'hauteur_cm'       => $validated['hauteur_cm'],
                'forme'            => $validated['forme'],
                'prix_estime'      => 0.00,
                'statut'           => $validated['statut'] ?? 'brouillon',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Projet de cuisine initialisé avec succès.',
                'data'    => $projet
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Erreur lors de la création du projet.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un projet par son UUID (GET /api/projets/{id})
     */
    public function show($id)
    {
        $projet = ProjetCuisine::find($id);

        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet de cuisine introuvable.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $projet
        ], 200);
    }

    /**
     * Mettre à jour un projet (PUT /api/projets/{id})
     * RG-01 : si on repasse en linéaire, on force largeur_cm à null
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

        $validator = Validator::make($request->all(), [
            'nom_projet'  => 'sometimes|string|max:255',
            'forme'       => 'sometimes|in:lineaire,en_L',
            'longueur_cm' => 'sometimes|integer|min:100',
            'largeur_cm'  => 'required_if:forme,en_L|nullable|integer|min:100',
            'hauteur_cm'  => 'sometimes|integer|min:150',
            'statut'      => 'sometimes|in:brouillon,devis_demande,traite',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();

            // RG-01 : si on repasse en linéaire, on supprime le Mur B
            // On utilise null (cohérent avec store) plutôt que 0
            $formeCible = $validated['forme'] ?? $projet->forme;
            if ($formeCible === 'lineaire') {
                $validated['largeur_cm'] = null;
            }

            $projet->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Projet de cuisine mis à jour avec succès.',
                'data'    => $projet->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Erreur lors de la mise à jour.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un projet (DELETE /api/projets/{id})
     */
    public function destroy($id)
    {
        $projet = ProjetCuisine::find($id);

        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet introuvable.'
            ], 404);
        }

        try {
            // Supprime en cascade les projet_modules liés (à configurer aussi en migration)
            DB::table('projet_modules')->where('projet_id', $id)->delete();
            $projet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Projet de cuisine supprimé avec succès.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Erreur lors de la suppression.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajouter un module dans le Canvas 2D (POST /api/projets/{id}/modules)
     * RG-02 : vérification du périmètre utile avant insertion
     */
    public function ajouterModule(Request $request, $projetId)
    {
        $projet = ProjetCuisine::find($projetId);

        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet de cuisine introuvable.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'module_id'  => 'required|uuid|exists:module_produits,ModuleProduit_id',
            'materiau_id' => 'required|uuid|exists:materiaux,Materiau_id',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Calcul du périmètre utile (RG-02)
            $perimetreUtile = $projet->longueur_cm;
            if ($projet->forme === 'en_L' && !is_null($projet->largeur_cm)) {
                $perimetreUtile += $projet->largeur_cm;
            }

            // Largeur du module à ajouter
            $moduleCatalogue = DB::table('module_produits')
                ->where('ModuleProduit_id', $request->input('module_id'))
                ->first();

            if (!$moduleCatalogue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module du catalogue introuvable.'
                ], 404);
            }

            $largeurNouveauModule = $moduleCatalogue->largeur_cm;

            // Somme des largeurs déjà occupées dans ce projet
            $largeurTotaleActuelle = DB::table('projet_modules')
                ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
                ->where('projet_modules.projet_id', $projetId)
                ->sum('module_produits.largeur_cm');

            // Vérification RG-02
            if (($largeurTotaleActuelle + $largeurNouveauModule) > $perimetreUtile) {
                return response()->json([
                    'success' => false,
                    'message' => "RG-02 : Espace insuffisant. Occupation actuelle : {$largeurTotaleActuelle} cm + nouveau module : {$largeurNouveauModule} cm > périmètre utile : {$perimetreUtile} cm."
                ], 422);
            }

            // Insertion
            $projetModuleId = (string) Str::uuid();

            DB::table('projet_modules')->insert([
                'ProjetModule_id' => $projetModuleId,
                'projet_id'       => $projetId,
                'module_id'       => $request->input('module_id'),
                'materiau_id'     => $request->input('materiau_id'),
                'position_x'      => $request->input('position_x'),
                'position_y'      => $request->input('position_y'),
            ]);

            // Recalcul du prix total estimé
            $nouveauPrixTotal = DB::table('projet_modules')
                ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
                ->where('projet_modules.projet_id', $projetId)
                ->sum('module_produits.prix_base');

            $projet->update(['prix_estime' => $nouveauPrixTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Module positionné avec succès.',
                'data'    => [
                    'ProjetModule_id'   => $projetModuleId,
                    'espace_occupe_cm'  => $largeurTotaleActuelle + $largeurNouveauModule,
                    'espace_maximal_cm' => $perimetreUtile,
                    'nouveau_prix_total' => $nouveauPrixTotal,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Erreur lors de l\'ajout du module.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Charger le Canvas 2D (GET /api/projets/{id}/modules)
     * Retourne les dimensions de la pièce + les modules positionnés
     */
    /**
     * Charger le Canvas 2D complet d'un projet (GET /api/projets/{id}/modules)
     */
    public function chargerCanvas($id)
    {
        $projet = ProjetCuisine::find($id);
        if (!$projet) {
            return response()->json([
                'success' => false,
                'message' => 'Projet introuvable.'
            ], 404);
        }

        try {
            $modulesCanvas = DB::table('projet_modules')
                ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
                ->where('projet_modules.projet_id', $id)
                ->select([
                    'projet_modules.ProjetModule_id',
                    'projet_modules.position_x',
                    'projet_modules.position_y',
                    'module_produits.ModuleProduit_id',
                    'module_produits.ModuleProduit_nom as nom',
                    'module_produits.categorie',
                    'module_produits.largeur_cm',
                    'module_produits.prix_base',
                    'module_produits.image_url' // <--- AJOUT CRITIQUE POUR LE REALISME DU FRONT-END
                ])
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $modulesCanvas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Impossible de charger l\'état du canvas.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Supprimer un module du Canvas et recalculer le prix
     * (DELETE /api/projets/{projetId}/modules/{projetModuleId})
     */
    public function supprimerModule($projetId, $projetModuleId)
    {
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

        try {
            DB::table('projet_modules')
                ->where('ProjetModule_id', $projetModuleId)
                ->delete();

            // Recalcul du prix après suppression
            $nouveauPrixTotal = DB::table('projet_modules')
                ->join('module_produits', 'projet_modules.module_id', '=', 'module_produits.ModuleProduit_id')
                ->where('projet_modules.projet_id', $projetId)
                ->sum('module_produits.prix_base');

            $projet = ProjetCuisine::find($projetId);
            $projet->update(['prix_estime' => $nouveauPrixTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Module retiré du canvas avec succès.',
                'data'    => [
                    'nouveau_prix_total' => $nouveauPrixTotal
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'       => false,
                'message'       => 'Erreur lors de la suppression du module.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}

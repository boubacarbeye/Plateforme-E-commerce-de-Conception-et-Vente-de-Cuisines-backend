<?php

namespace App\Http\Controllers;

use App\Models\ModuleProduit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModuleController extends Controller
{
    /**
     * GET /api/modules
     * Liste publique des modules actifs.
     */
    public function index()
    {
        $modules = ModuleProduit::where('actif', true)->get();
        return response()->json($modules, Response::HTTP_OK);
    }

    /**
     * POST /api/modules
     * Réservé à l'Admin : Création d'un module avec validation de la catégorie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ModuleProduit_nom' => 'required|string|max:20',
            'categorie'         => 'required|in:meuble_bas,plan_travail,electromenager,meuble_haut,evier,colonne,robinetterie',
            'prix_base'         => 'required|numeric|min:0',
            'image_url'         => 'nullable|string|max:30',
            'actif'             => 'nullable|boolean'
        ]);

        $module = ModuleProduit::create($validated);

        return response()->json([
            'message' => 'Module de cuisine ajouté avec succès',
            'module'  => $module
        ], Response::HTTP_CREATED);
    }

    /**
     * GET /api/modules/{id}
     * Afficher un module spécifique.
     */
    public function show($id)
    {
        $module = ModuleProduit::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module introuvable'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($module, Response::HTTP_OK);
    }

    /*
    Réservé à l'Admin : Mise à jour avec validation de la catégorie.
     */
    public function update(Request $request, $id)
    {
        $module = ModuleProduit::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module introuvable'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'ModuleProduit_nom' => 'sometimes|string|max:20',
            'categorie'         => 'sometimes|in:meuble_bas,plan_travail,electromenager,meuble_haut,evier,colonne,robinetterie',
            'prix_base'         => 'sometimes|numeric|min:0',
            'image_url'         => 'nullable|string|max:30',
            'actif'             => 'sometimes|boolean'
        ]);

        $module->update($validated);

        return response()->json([
            'message' => 'Module mis à jour avec succès',
            'module'  => $module
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /api/modules/{id}
     * Réservé à l'Admin : Supprimer un module.
     */
    public function destroy($id)
    {
        $module = ModuleProduit::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module introuvable'], Response::HTTP_NOT_FOUND);
        }

        $module->delete();

        return response()->json([
            'message' => 'Module supprimé du catalogue avec succès'
        ], Response::HTTP_OK);
    }
}
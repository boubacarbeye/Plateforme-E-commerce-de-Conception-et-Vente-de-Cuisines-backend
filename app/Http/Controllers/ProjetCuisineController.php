<?php

namespace App\Http\Controllers;

use App\Models\ProjetCuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetCuisineController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'longueur_cm' => 'required|integer|min:100',
            'largeur_cm' => 'required|integer|min:100',
            'hauteur_cm' => 'required|integer|min:200',
            'forme' => 'required|in:lineaire,en_L',
        ]);

        // On vérifie si l'utilisateur est connecté sans faire planter la page si le token est invalide
        try {
            if (Auth::guard('api')->check()) {
                $data['client_id'] = Auth::guard('api')->id();
            }
        } catch (\Exception $e) {
            // Un visiteur avec un token expiré sera traité comme un visiteur normal
        }

        $data['statut'] = 'brouillon';
        $data['prix_estime'] = 0;

        $projet = ProjetCuisine::create($data);

        return response()->json($projet, 201);
    }

    public function show($id)
    {
        // On charge le projet AVEC ses modules, les infos des modules et des matériaux
        $projet = ProjetCuisine::with('modules.module', 'modules.materiau', 'client')->findOrFail($id);

        return response()->json($projet);
    }

    public function update(Request $request, $id)
    {
        $projet = ProjetCuisine::findOrFail($id);

        $data = $request->validate([
            'modules' => 'sometimes|array',
            'modules.*.module_id' => 'required|exists:module_produits,id',
            'modules.*.materiau_id' => 'nullable|exists:materiaux,id',
            'modules.*.position_x' => 'required|integer',
            'modules.*.position_y' => 'required|integer',
            'modules.*.quantite' => 'sometimes|integer|min:1',
        ]);

        $projet->modules()->delete();

        if (isset($data['modules'])) {
            foreach ($data['modules'] as $modData) {
                $projet->modules()->create([
                    'module_id' => $modData['module_id'],
                    'materiau_id' => $modData['materiau_id'] ?? null,
                    'position_x' => $modData['position_x'],
                    'position_y' => $modData['position_y'],
                    'quantite' => $modData['quantite'] ?? 1,
                ]);
            }
        }

        $projet->recalculerPrixEstime();

        return response()->json([
            'message' => 'Projet mis à jour et prix recalculé.',
            'projet' => $projet->load('modules.module', 'modules.materiau'),
        ]);
    }

    public function index()
    {
        // Renvoie uniquement les projets du client connecté
        return response()->json(
            Auth::guard('api')->user()->projets()->with('modules')->orderBy('created_at', 'desc')->get()
        );
    }

    public function destroy($id)
    {
        $projet = ProjetCuisine::findOrFail($id);
        $projet->modules()->delete();
        $projet->delete();

        return response()->json(['message' => 'Projet supprimé avec succès.']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materiau;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MateriauController extends Controller
{
    /**
     * Récupérer tous les matériaux (GET /api/materiaux)
     */
    public function index()
    {
        try {
            // Note: Votre migration n'ayant pas de colonne 'actif', on récupère tout.
            $materiaux = Materiau::all();

            return response()->json([
                'status' => 'success',
                'data'   => $materiaux
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Impossible de charger les matériaux.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un matériau spécifique (GET /api/materiaux/{id})
     */
    public function show($id)
    {
        $materiau = Materiau::where('Materiau_id', $id)->first();

        if (!$materiau) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Matériau introuvable.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $materiau
        ], 200);
    }

    /**
     * Enregistrer un matériau (POST /api/materiaux)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'materiau_nom'    => 'required|string|max:20',
            'type'            => 'required|string|max:20',
            'supplement_prix' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Certains champs sont incorrects ou manquants.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $materiau = Materiau::create([
                'Materiau_id'     => (string) Str::uuid(), // Génération automatique du UUID
                'materiau_nom'    => $request->input('materiau_nom'),
                'type'            => $request->input('type'),
                'supplement_prix' => $request->input('supplement_prix'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Matériau enregistré avec succès.',
                'data'    => $materiau
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Erreur lors de l\'enregistrement en base de données.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier un matériau (PUT /api/materiaux/{id})
     */
    public function update(Request $request, $id)
    {
        $materiau = Materiau::where('Materiau_id', $id)->first();

        if (!$materiau) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Le matériau demandé est introuvable.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'materiau_nom'    => 'sometimes|required|string|max:20',
            'type'            => 'sometimes|required|string|max:20',
            'supplement_prix' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Certains champs sont incorrects.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $materiau->update($request->only(['materiau_nom', 'type', 'supplement_prix']));

            return response()->json([
                'status'  => 'success',
                'message' => 'Matériau mis à jour avec succès.',
                'data'    => $materiau->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Impossible de modifier l\'enregistrement.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer définitivement un matériau (DELETE /api/materiaux/{id})
     */
    public function destroy($id)
    {
        $materiau = Materiau::where('Materiau_id', $id)->first();

        if (!$materiau) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Matériau introuvable.'
            ], 404);
        }

        try {
            $materiau->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Matériau supprimé du catalogue avec succès.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Erreur technique lors de la suppression.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}
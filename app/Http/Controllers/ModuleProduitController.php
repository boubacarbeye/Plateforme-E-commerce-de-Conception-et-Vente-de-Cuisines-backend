<?php

namespace App\Http\Controllers;

use App\Models\ModuleProduit;
use Illuminate\Http\Request;
use Exception;

class ModuleProduitController extends Controller
{
    public function index()
    {
        try {
            $modules = ModuleProduit::where('actif', true)->get();
            return response()->json($modules, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur catalogue', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $module = ModuleProduit::findOrFail($id);
            return response()->json($module, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Module introuvable'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ModuleProduit_nom' => 'required|string|max:20',
                'categorie'         => 'required|string|max:20',
                'largeur_cm'        => 'nullable|integer|min:10',
                'prix_base'         => 'required|numeric|min:0',
                'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Nullable désormais
                'modele_3d'         => 'nullable|file|max:15360',
                'actif'             => 'nullable'
            ]);

            $data = [
                'ModuleProduit_nom' => $request->ModuleProduit_nom,
                'categorie'         => $request->categorie,
                'largeur_cm'        => $request->largeur_cm ?? 60,
                'prix_base'         => $request->prix_base,
                'actif'             => $request->has('actif') ? (bool)$request->actif : true,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/modules'), $imageName);
                $data['image_url'] = url('/api/images/modules/' . $imageName);
            }

            if ($request->hasFile('modele_3d')) {
                $model = $request->file('modele_3d');
                $modelName = time() . '_' . $model->getClientOriginalName();
                $model->move(public_path('images/modules'), $modelName);
                $data['modele_3d_url'] = url('/api/images/modules/' . $modelName);
            }

            $module = ModuleProduit::create($data);
            return response()->json(['status' => 'success', 'data' => $module], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur création', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $module = ModuleProduit::findOrFail($id);
            $module->update($request->all());
            return response()->json(['status' => 'success', 'data' => $module], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur modification', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $module = ModuleProduit::findOrFail($id);
            if ($module->image_url) {
                @unlink(public_path('images/modules/' . basename($module->image_url)));
            }
            if ($module->modele_3d_url) {
                @unlink(public_path('images/modules/' . basename($module->modele_3d_url)));
            }
            $module->delete();
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur suppression'], 500);
        }
    }
}
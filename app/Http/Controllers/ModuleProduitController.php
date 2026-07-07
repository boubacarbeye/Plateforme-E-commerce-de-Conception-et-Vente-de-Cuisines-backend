<?php

namespace App\Http\Controllers;

use App\Models\ModuleProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleProduitController extends Controller
{
    // Liste publique (catalogue)
    public function index()
    {
        return response()->json(
            ModuleProduit::where('actif', true)->orderBy('nom')->get()
        );
    }

    // Dans store()
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie' => 'required|in:meuble_bas,meuble_haut,colonne,plan_travail,evier,robinetterie,electromenager',
            'largeur_cm' => 'required|integer|min:0',
            'prix_base' => 'required|numeric|min:0',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'model_3d' => 'nullable|file|mimes:glb|max:51200', // <-- AJOUT
        ]);

        $data['actif'] = true;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('modules', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Gestion du fichier 3D
        if ($request->hasFile('model_3d')) {
            $path3d = $request->file('model_3d')->store('modules', 'public');
            $data['model_3d_url'] = Storage::url($path3d);
        }

        $module = ModuleProduit::create($data);

        return response()->json($module, 201);
    }

    // Dans update()
    public function update(Request $request, $id)
    {
        $module = ModuleProduit::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'categorie' => 'sometimes|in:meuble_bas,meuble_haut,colonne,plan_travail,evier,robinetterie,electromenager',
            'largeur_cm' => 'sometimes|integer|min:0',
            'prix_base' => 'sometimes|numeric|min:0',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'model_3d' => 'nullable|file|mimes:glb|max:51200', // <-- AJOUT
        ]);

        if ($request->hasFile('image')) {
            if ($module->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $module->image_url));
            }
            $data['image_url'] = Storage::url($request->file('image')->store('modules', 'public'));
        }

        if ($request->hasFile('model_3d')) {
            if ($module->model_3d_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $module->model_3d_url));
            }
            $data['model_3d_url'] = Storage::url($request->file('model_3d')->store('modules', 'public'));
        }

        $module->update($data);

        return response()->json($module);
    }

    // Admin : Désactiver un module
    public function destroy($id)
    {
        $module = ModuleProduit::findOrFail($id);
        $module->update(['actif' => false]);

        return response()->json(['message' => 'Module désactivé avec succès.']);
    }
}

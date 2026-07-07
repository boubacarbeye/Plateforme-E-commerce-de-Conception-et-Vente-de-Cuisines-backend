<?php

namespace App\Http\Controllers;

use App\Models\Materiau;
use Illuminate\Http\Request;

class MateriauController extends Controller
{
    // Liste publique
    public function index()
    {
        return response()->json(
            Materiau::where('actif', true)->orderBy('nom')->get()
        );
    }

    // Admin : Ajouter
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:couleur,finition,poignee,materiau',
            'supplement_prix' => 'required|numeric|min:0',
        ]);

        $data['actif'] = true;

        $materiau = Materiau::create($data);

        return response()->json($materiau, 201);
    }

    // Admin : Modifier
    public function update(Request $request, $id)
    {
        $materiau = Materiau::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:couleur,finition,poignee,materiau',
            'supplement_prix' => 'sometimes|numeric|min:0',
        ]);

        $materiau->update($data);

        return response()->json($materiau);
    }

    // Admin : Supprimer (désactiver)
    public function destroy($id)
    {
        $materiau = Materiau::findOrFail($id);
        $materiau->update(['actif' => false]); // On désactive au lieu de supprimer pour préserver l'historique

        return response()->json(['message' => 'Matériau supprimé avec succès.']);
    }
}

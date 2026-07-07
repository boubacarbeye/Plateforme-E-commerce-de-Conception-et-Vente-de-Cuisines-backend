<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    // Lister tous les utilisateurs
    public function index()
    {
        return response()->json(Utilisateur::orderBy('created_at', 'desc')->get());
    }

    // Modifier un utilisateur (nom, email, et surtout son rôle)
    public function update(Request $request, $id)
    {
        $user = Utilisateur::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string',
            'prenom' => 'sometimes|string',
            'email' => 'sometimes|email|unique:utilisateurs,email,'.$id,
            'role' => 'sometimes|in:client,admin,commercial',
        ]);

        $user->update($data);

        return response()->json($user);
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        $user = Utilisateur::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs',
            'password' => 'required|string|min:6',
            'role' => 'required|in:client,admin,commercial',
        ]);

        $user = Utilisateur::create($data);

        return response()->json($user, 201);
    }
}

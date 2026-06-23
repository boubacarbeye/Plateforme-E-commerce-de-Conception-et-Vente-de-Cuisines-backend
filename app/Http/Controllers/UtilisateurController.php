<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = Utilisateur::select([
            'Utilisateur_id', 'nom', 'prenom', 'email', 'role'
        ])->orderBy('nom')->get();

        return response()->json(['status' => 'success', 'data' => $utilisateurs], 200);
    }

    public function changerRole(Request $request, $id)
    {
        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) {
            return response()->json(['status' => 'error', 'message' => 'Utilisateur introuvable.'], 404);
        }

        $validator = Validator::make($request->all(), ['role' => 'required|in:client,commercial,admin']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        if ($utilisateur->Utilisateur_id === auth('api')->id()) {
            return response()->json(['status' => 'error', 'message' => 'Vous ne pouvez pas modifier votre propre rôle.'], 403);
        }

        $utilisateur->update(['role' => $request->input('role')]);
        return response()->json(['status' => 'success', 'message' => 'Rôle mis à jour.'], 200);
    }

    public function destroy($id)
    {
        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) {
            return response()->json(['status' => 'error', 'message' => 'Utilisateur introuvable.'], 404);
        }

        if ($utilisateur->Utilisateur_id === auth('api')->id()) {
            return response()->json(['status' => 'error', 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        $utilisateur->delete();
        return response()->json(['status' => 'success', 'message' => 'Utilisateur supprimé.'], 200);
    }
}

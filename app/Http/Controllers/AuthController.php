<?php

namespace App\Http\Controllers;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        // A. Validation : On vérifie que les données reçues sont conformes
        $request->validate([
            'nom' => 'required|string|max:30',
            'prenom' => 'required|string|max:30',
            'email' => 'required|string|email|max:30|unique:utilisateurs',
            'motDePasse' => 'required|string|min:6',
            'role' => 'required|string|in:client,commercial,admin',
        ]);

        // B. Création : On insère en base de données via le Modèle
        $user = Utilisateur::create([
            'Utilisateur_id' => (string) Str::uuid(),
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'motDePasse' => $request->motDePasse, // Le modèle va le hacher automatiquement !
            'role' => $request->role,
        ]);

        // C. Réponse : On génère un token JWT immédiatement pour le connecter
        $token = auth('api')->login($user);

        return response()->json(['token' => $token, 'user' => $user], 201);
    }


    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'motDePasse' => 'required|string',
        ]);

        // JWT attend les clés 'email' et 'password'. On adapte notre 'motDePasse'
        $credentials = [
            'email' => $request->email,
            'password' => $request->motDePasse
        ];

        // auth('api')->attempt() va appliquer Bcrypt sur le mot de passe reçu 
        // et le comparer avec la chaîne cryptée en BDD.
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        // Si c'est bon, on récupère l'utilisateur pour connaître son rôle
        $user = auth('api')->user();

        // Logique de redirection selon le rôle transmise au Frontend
        $redirectTo = match($user->role) {
            'admin' => '/admin/dashboard',
            'commercial' => '/commercial/espace',
            'client' => '/mon-projet-cuisine',
            default => '/',
        };

        return response()->json([
            'token' => $token,
            'role' => $user->role,
            'redirect_to' => $redirectTo
        ]);
    }    
}

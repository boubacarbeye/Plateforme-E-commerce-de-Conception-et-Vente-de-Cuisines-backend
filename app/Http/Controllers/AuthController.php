<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Utilisateur;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (POST /api/register)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'email'      => 'required|email|unique:utilisateurs,email',
            'password'   => 'required|string|min:8|confirmed',
            'telephone'  => 'nullable|string|max:20',
            'role'       => 'nullable|in:client,commercial,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Données d\'inscription invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user = Utilisateur::create([
                'Utilisateur_id' => (string) Str::uuid(),
                'nom'            => $request->input('nom'),
                'prenom'         => $request->input('prenom'),
                'email'          => $request->input('email'),
                'motDePasse'     => bcrypt($request->input('password')),
                'telephone'      => $request->input('telephone'),
                'role'           => $request->input('role', 'client'),
            ]);

            $token = auth('api')->login($user);

            return response()->json([
                'status'  => 'success',
                'message' => 'Compte créé avec succès.',
                'user'    => [
                    'id'     => $user->Utilisateur_id,
                    'nom'    => $user->nom,
                    'prenom' => $user->prenom,
                    'email'  => $user->email,
                    'role'   => $user->role,
                ],
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Erreur lors de la création du compte.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur existant (POST /api/login)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Champs requis manquants.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $credentials = [
            'email'    => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Identifiants de connexion invalides.'
            ], 401);
        }

        $user = auth('api')->user();

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'     => $user->Utilisateur_id,
                'nom'    => $user->nom,
                'prenom' => $user->prenom,
                'email'  => $user->email,
                'role'   => $user->role,
            ],
            'token' => $token,
            'role'  => $user->role,
        ], 200);
    }

    /**
     * Déconnexion (POST /api/logout)
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status'  => 'success',
            'message' => 'Déconnecté avec succès.'
        ], 200);
    }

    /**
     * Récupérer l'utilisateur connecté (GET /api/me)
     */
    public function me()
    {
        $user = auth('api')->user();

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'        => $user->Utilisateur_id,
                'nom'       => $user->nom,
                'prenom'    => $user->prenom,
                'email'     => $user->email,
                'telephone' => $user->telephone,
                'role'      => $user->role,
            ]
        ], 200);
    }
}

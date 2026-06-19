<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProjetCuisineController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// 1. Routes publiques (Ouvertes aux visiteurs)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 2. Routes sécurisées de l'application (Nécessitent un Token JWT valide)
Route::middleware('auth:api')->group(function () { 

    // Espace réservé uniquement aux Administrateurs
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function() {
            return response()->json(['message' => 'Espace Secrétariat / Admin']);
        });
    });

    // Espace accessible aux Commerciaux ET aux Admins
    Route::middleware('role:commercial,admin')->group(function () {
        Route::get('/commercial/devis', function() {
            return response()->json(['message' => 'Gestion des paniers et devis clients']);
        });
    });

    // Espace accessible aux Clients
    Route::middleware('role:client')->group(function () {
        Route::get('/client/cuisine', function() {
            return response()->json(['message' => 'Suivi de la conception de ma cuisine']);
        });
    });
    
});

// --- ROUTES DU CATALOGUE DES MODULES ---

// Routes publiques (Accessibles par les clients pour composer leur cuisine)
Route::get('/modules', [ModuleController::class, 'index']);
Route::get('/modules/{id}', [ModuleController::class, 'show']);

// Routes protégées (Seul l'admin connecté peut modifier le catalogue)
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/modules', [ModuleController::class, 'store']);       // Créer
    Route::put('/modules/{id}', [ModuleController::class, 'update']);   // Modifier
    Route::delete('/modules/{id}', [ModuleController::class, 'destroy']); // Supprimer
});


// ROUTES PUBLIQUES
Route::post('/projets', [ProjetCuisineController::class, 'store']);    // Créer un projet (Écran création)
Route::get('/projets/{id}', [ProjetCuisineController::class, 'show']); // Lire un projet spécifique
Route::put('/projets/{id}', [ProjetCuisineController::class, 'update']); // Modifier
Route::delete('/projets/{id}', [ProjetCuisineController::class, 'destroy']); // Supprimer

Route::post('/projets/{id}/modules', [ProjetCuisineController::class, 'ajouterModule']); // Placer un meuble + Test RG-02
Route::get('/projets/{id}/modules', [ProjetCuisineController::class, 'chargerCanvas']);   // Charger le plan 2D
Route::get('/projets/{projetId}/modules', [ProjetCuisineController::class, 'listerModules']);
Route::delete('/projets/{projetId}/modules/{projetModuleId}', [ProjetCuisineController::class, 'supprimerModule']);


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes pour la gestion des modules (doit correspondre à /api/modules)
Route::get('/modules', [ModuleController::class, 'index']);
Route::post('/modules', [ModuleController::class, 'store']);
Route::put('/modules/{id}', [ModuleController::class, 'update']);
Route::delete('/modules/{id}', [ModuleController::class, 'destroy']);
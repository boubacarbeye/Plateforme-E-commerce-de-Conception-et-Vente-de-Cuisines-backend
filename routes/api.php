<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleProduitController;
use App\Http\Controllers\ProjetCuisineController;
use App\Http\Controllers\UtilisateurController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Catalogue lecture publique
Route::get('/modules',      [ModuleProduitController::class, 'index']);
Route::get('/modules/{id}', [ModuleProduitController::class, 'show']);

// Servir les images sans CORS
Route::get('/images/modules/{filename}', function ($filename) {
    $path = public_path('images/modules/' . $filename);
    if (!file_exists($path)) abort(404);
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control'               => 'public, max-age=86400',
    ]);
});

// Servir les modèles 3D (.glb) sans CORS
Route::get('/models/{filename}', function ($filename) {
    $path = public_path('models/' . $filename);
    if (!file_exists($path)) abort(404);
    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Content-Type'                => 'model/gltf-binary',
        'Cache-Control'               => 'public, max-age=86400',
    ]);
});

/*
|--------------------------------------------------------------------------
| Routes protégées JWT
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Modules — écriture admin
    Route::middleware('role:admin')->group(function () {
        Route::post('/modules',        [ModuleProduitController::class, 'store']);
        Route::put('/modules/{id}',    [ModuleProduitController::class, 'update']);
        Route::delete('/modules/{id}', [ModuleProduitController::class, 'destroy']);
    });

    // Projets de cuisine
    Route::post('/projets',        [ProjetCuisineController::class, 'store']);
    Route::get('/projets/{id}',    [ProjetCuisineController::class, 'show']);
    Route::put('/projets/{id}',    [ProjetCuisineController::class, 'update']);
    Route::delete('/projets/{id}', [ProjetCuisineController::class, 'destroy']);

    Route::post('/projets/{id}/modules',                          [ProjetCuisineController::class, 'ajouterModule']);
    Route::get('/projets/{id}/modules',                           [ProjetCuisineController::class, 'chargerCanvas']);
    Route::delete('/projets/{projetId}/modules/{projetModuleId}', [ProjetCuisineController::class, 'supprimerModule']);

    // Tableaux de bord par rôle
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Espace Admin']));

        // Gestion utilisateurs
        Route::get('/admin/utilisateurs',             [UtilisateurController::class, 'index']);
        Route::patch('/admin/utilisateurs/{id}/role', [UtilisateurController::class, 'changerRole']);
        Route::delete('/admin/utilisateurs/{id}',     [UtilisateurController::class, 'destroy']);
    });

    Route::middleware('role:commercial,admin')->group(function () {
        Route::get('/commercial/devis', fn() => response()->json(['message' => 'Gestion des devis']));
    });

    Route::middleware('role:client')->group(function () {
        Route::get('/client/cuisine', fn() => response()->json(['message' => 'Suivi cuisine']));
    });
});
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\MateriauController;
use App\Http\Controllers\ModuleProduitController;
use App\Http\Controllers\ProjetCuisineController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route; // <-- AJOUT

// Routes Publiques
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/modules', [ModuleProduitController::class, 'index']);
Route::get('/materiaux', [MateriauController::class, 'index']);
Route::post('/projets', [ProjetCuisineController::class, 'store']);
Route::get('/projets/{id}', [ProjetCuisineController::class, 'show']);

// Routes Protégées (Connecté)
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/projets', [ProjetCuisineController::class, 'index']);
    Route::put('/projets/{id}', [ProjetCuisineController::class, 'update']);
    Route::delete('/projets/{id}', [ProjetCuisineController::class, 'destroy']); // <-- AJOUT (Suppression projet)
    Route::post('/projets/{id}/devis', [DevisController::class, 'generate']);
});

// Routes Admin
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Modules
    Route::post('/modules', [ModuleProduitController::class, 'store']);
    Route::put('/modules/{id}', [ModuleProduitController::class, 'update']);
    Route::delete('/modules/{id}', [ModuleProduitController::class, 'destroy']);

    // Matériaux
    Route::post('/materiaux', [MateriauController::class, 'store']);
    Route::put('/materiaux/{id}', [MateriauController::class, 'update']);
    Route::delete('/materiaux/{id}', [MateriauController::class, 'destroy']);

    // Utilisateurs <-- AJOUT
    Route::get('/users', [UtilisateurController::class, 'index']);
    Route::put('/users/{id}', [UtilisateurController::class, 'update']);
    Route::delete('/users/{id}', [UtilisateurController::class, 'destroy']);
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // ... autres routes
    Route::post('/users', [UtilisateurController::class, 'store']); // <-- AJOUT
    Route::get('/users', [UtilisateurController::class, 'index']);
    // ...
});

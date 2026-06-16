<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
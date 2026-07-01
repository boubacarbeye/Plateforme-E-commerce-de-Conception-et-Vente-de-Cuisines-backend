<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        // Compte Admin
        Utilisateur::create([
            'nom' => 'Admin',
            'prenom' => 'DGS',
            'email' => 'admin@dgs.com',
            'password' => Hash::make('password'), // Mot de passe : password
            'role' => 'admin'
        ]);

        // Compte Commercial
        Utilisateur::create([
            'nom' => 'Commercial',
            'prenom' => 'Jean',
            'email' => 'commercial@dgs.com',
            'password' => Hash::make('password'),
            'role' => 'commercial'
        ]);

        // Compte Client
        Utilisateur::create([
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'marie.dupont@email.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);
    }
}

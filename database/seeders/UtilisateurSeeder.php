<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        // Compte Admin
        Utilisateur::create([
            'nom' => 'Admin',
            'prenom' => 'DGS',
            'email' => 'admin@dgs.com',
            'password' => 'password', // Mot de passe : password (haché automatiquement par le modèle)
            'role' => 'admin',
        ]);

        // Compte Commercial
        Utilisateur::create([
            'nom' => 'Commercial',
            'prenom' => 'Jean',
            'email' => 'commercial@dgs.com',
            'password' => 'password',
            'role' => 'commercial',
        ]);

        // Compte Client
        Utilisateur::create([
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'marie.dupont@email.com',
            'password' => 'password',
            'role' => 'client',
        ]);
    }
}

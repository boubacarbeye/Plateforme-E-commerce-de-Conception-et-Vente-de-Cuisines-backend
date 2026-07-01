<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModuleProduit;

class ModuleProduitSeeder extends Seeder
{
    // database/seeders/ModuleProduitSeeder.php
    public function run(): void
    {
        $modules = [
            ['nom' => 'Meuble bas 60 cm', 'categorie' => 'meuble_bas', 'largeur_cm' => 60, 'prix_base' => 180.00],
            ['nom' => 'Meuble bas 80 cm', 'categorie' => 'meuble_bas', 'largeur_cm' => 80, 'prix_base' => 220.00],
            ['nom' => 'Meuble haut 60 cm', 'categorie' => 'meuble_haut', 'largeur_cm' => 60, 'prix_base' => 150.00],
            ['nom' => 'Colonne 60 cm', 'categorie' => 'colonne', 'largeur_cm' => 60, 'prix_base' => 260.00],
            ['nom' => 'Plan de travail 200 cm', 'categorie' => 'plan_travail', 'largeur_cm' => 200, 'prix_base' => 320.00],
            ['nom' => 'Évier inox 1 bac', 'categorie' => 'evier', 'largeur_cm' => 60, 'prix_base' => 140.00],
            ['nom' => 'Mitigeur chromé', 'categorie' => 'robinetterie', 'largeur_cm' => 0, 'prix_base' => 90.00],
            ['nom' => 'Four encastrable', 'categorie' => 'electromenager', 'largeur_cm' => 60, 'prix_base' => 450.00],
        ];
        foreach ($modules as $m) {
            ModuleProduit::create(array_merge($m, ['actif' => true]));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materiau;

class MateriauSeeder extends Seeder
{
    // database/seeders/MateriauSeeder.php
    public function run(): void
    {
        $data = [
            ['nom' => 'Blanc mat', 'type' => 'couleur', 'supplement_prix' => 0],
            ['nom' => 'Gris anthracite', 'type' => 'couleur', 'supplement_prix' => 25.00],
            ['nom' => 'Chêne naturel', 'type' => 'materiau', 'supplement_prix' => 45.00],
            ['nom' => 'Poignée barre inox', 'type' => 'poignee', 'supplement_prix' => 8.00],
            ['nom' => 'Finition brillante', 'type' => 'finition', 'supplement_prix' => 15.00],
        ];
        foreach ($data as $d) Materiau::create(array_merge($d, ['actif' => true]));
    }
}

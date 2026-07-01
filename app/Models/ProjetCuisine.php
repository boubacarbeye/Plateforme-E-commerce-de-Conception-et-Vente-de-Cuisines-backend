<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// app/Models/ProjetCuisine.php
class ProjetCuisine extends Model
{
    use HasUuids;
    protected $table = 'projet_cuisines';
    protected $fillable = ['client_id', 'nom', 'longueur_cm', 'largeur_cm',
                           'hauteur_cm', 'forme', 'prix_estime', 'statut'];

    public function client() { return $this->belongsTo(Utilisateur::class, 'client_id'); }
    public function modules() { return $this->hasMany(ProjetModule::class, 'projet_id'); }
    public function devis() { return $this->hasOne(Devis::class, 'projet_id'); }
    // app/Models/ProjetCuisine.php

    public function recalculerPrixEstime(): void
    {
        $total = 0;

        foreach ($this->modules as $projetModule) {
            // 1. Prix de base du module
            $total += $projetModule->module->prix_base * $projetModule->quantite;

            // 2. Supplément du matériau/finition choisi
            if ($projetModule->materiau_id) {
                $total += $projetModule->materiau->supplement_prix * $projetModule->quantite;
            }
        }

        $this->prix_estime = $total;
        $this->save();
    }
}

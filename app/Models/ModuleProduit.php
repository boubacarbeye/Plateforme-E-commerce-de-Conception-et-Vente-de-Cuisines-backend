<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// app/Models/ModuleProduit.php
class ModuleProduit extends Model
{
    use HasUuids;
    protected $table = 'module_produits';
    protected $fillable = ['nom', 'categorie', 'largeur_cm', 'hauteur_cm', 'profondeur_cm', 'prix_base', 'image_url', 'model_3d_url', 'actif'];

    public function projetsModules() {
        return $this->hasMany(ProjetModule::class, 'module_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// app/Models/Materiau.php
class Materiau extends Model
{
    use HasUuids;
    protected $table = 'materiaux';
    protected $fillable = ['nom', 'type', 'supplement_prix', 'actif'];

    public function projetsModules() {
        return $this->hasMany(ProjetModule::class, 'materiau_id');
    }
}
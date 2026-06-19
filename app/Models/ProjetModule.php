<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjetModule extends Model
{
    // Indique à Laravel le nom exact de ta table pivot
    protected $table = 'projet_modules';

    // Ta clé primaire personnalisée en UUID
    protected $primaryKey = 'ProjetModule_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Désactive les timestamps si tu ne les as pas mis dans ta migration
    public $timestamps = false; 

    // Autorise le remplissage de tes colonnes de positionnement spatial
    protected $fillable = [
        'ProjetModule_id',
        'projet_id',
        'module_id',
        'materiau_id',
        'position_x',
        'position_y',
    ];

    /**
     * Lien retour vers le projet de cuisine
     */
    public function projet(): BelongsTo
    {
        return $this->belongsTo(ProjetCuisine::class, 'projet_id', 'ProjetCuisine_id');
    }

    /**
     * Lien vers le module du catalogue utilisé
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(ModuleProduit::class, 'module_id', 'ModuleProduit_id');
    }
}
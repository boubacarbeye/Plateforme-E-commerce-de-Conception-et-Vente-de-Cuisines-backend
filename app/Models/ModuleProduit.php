<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModuleProduit extends Model
{
    use HasFactory;

    // Nom exact de ta table en base de données
    protected $table = 'module_produits';

    // Clé primaire personnalisée
    protected $primaryKey = 'ModuleProduit_id';

    // On indique à Laravel que la clé n'est pas un entier auto-incrémenté
    public $incrementing = false;
    protected $keyType = 'string';

    // Liste des colonnes modifiables (Mass Assignable)
    protected $fillable = [
        'ModuleProduit_nom',
        'categorie',
        'prix_base',
        'image_url',
        'actif'
    ];

    // Cast des types de données pour un formatage JSON propre
    protected $casts = [
        'prix_base' => 'float',
        'actif' => 'boolean',
    ];

    /**
     * Boot function pour générer automatiquement l'UUID à la création.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
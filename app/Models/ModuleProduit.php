<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModuleProduit extends Model
{
    use HasFactory;

    protected $table = 'module_produits';
    protected $primaryKey = 'ModuleProduit_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ModuleProduit_id',
        'ModuleProduit_nom',
        'categorie',
        'largeur_cm',
        'prix_base',
        'image_url',
        'modele_3d_url',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->ModuleProduit_id)) {
                $model->ModuleProduit_id = (string) Str::uuid();
            }
        });
    }
}
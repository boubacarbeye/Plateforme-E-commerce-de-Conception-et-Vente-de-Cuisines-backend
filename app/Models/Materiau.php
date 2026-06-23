<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materiau extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'materiaux';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'Materiau_id';

    /**
     * Indique si la clé primaire est en incrémentation automatique.
     * Obligatoire à 'false' car vous utilisez des UUIDs (chaînes).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de données de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indique si le modèle doit gérer les colonnes de repères de temps (created_at / updated_at).
     * Votre migration ne contenant pas $table->timestamps(), on le passe à false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont assignables en masse (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Materiau_id',
        'materiau_nom',
        'type',
        'supplement_prix',
    ];

    /**
     * Les attributs qui doivent être convertis (Casting).
     * Permet de s'assurer que le prix reste un float/double manipulable en PHP.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'supplement_prix' => 'float',
    ];
}
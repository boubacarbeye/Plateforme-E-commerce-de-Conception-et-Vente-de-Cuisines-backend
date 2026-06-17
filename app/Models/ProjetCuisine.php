<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjetCuisine extends Model
{
    use HasFactory;

    // 1. Définir le nom exact de la table si Laravel ne la devine pas (optionnel mais sécurisé)
    protected $table = 'projet_cuisines';

    // 2. Configurer ta clé primaire personnalisée (UUID)
    protected $primaryKey = 'ProjetCuisine_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. Désactiver les timestamps si tu ne les as pas mis dans la migration
    // Si tu as gardé $table->timestamps() dans ta migration, laisse cette ligne sur true ou supprime-la.
    public $timestamps = false; 

    // 4. Autoriser l'assignation de masse pour tes colonnes réelles
    protected $fillable = [
        'ProjetCuisine_id',
        'client_id',
        'longueur_cm',
        'largeur_cm',
        'hauteur_cm',
        'forme',
        'prix_estime',
        'statut'
    ];

    /**
     * Relation : Un projet appartient à un utilisateur (client)
     */
    public function client()
    {
        return $this->belongsTo(Utilisateur::class, 'client_id', 'Utilisateur_id');
    }
}
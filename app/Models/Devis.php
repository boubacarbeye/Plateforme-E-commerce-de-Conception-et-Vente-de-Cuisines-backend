<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// app/Models/Devis.php
class Devis extends Model
{
    use HasUuids;
    protected $fillable = ['projet_id', 'numero', 'montant_total',
                           'pdf_url', 'statut', 'date_creation'];

    public function projet() { return $this->belongsTo(ProjetCuisine::class, 'projet_id'); }
}

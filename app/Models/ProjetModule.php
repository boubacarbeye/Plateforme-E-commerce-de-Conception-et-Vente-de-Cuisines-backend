<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProjetModule extends Model
{
    use HasUuids;

    protected $fillable = ['projet_id', 'module_id', 'materiau_id',
        'position_x', 'position_y', 'quantite'];

    public function projet()
    {
        return $this->belongsTo(ProjetCuisine::class, 'projet_id');
    }

    public function module()
    {
        return $this->belongsTo(ModuleProduit::class, 'module_id');
    }

    public function materiau()
    {
        return $this->belongsTo(Materiau::class, 'materiau_id');
    }
}
